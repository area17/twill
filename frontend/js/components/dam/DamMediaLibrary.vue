<template>
  <div>
    <a17-dam-modal title="DAM Media Library" mode="wide" ref="modal" @open="opened" class="modal--upload">
      <template #header>
        <div class="medialibrary__header">
          <div class="medialibrary__collections">
            <label>{{ $trans('dam.add-collection-uploads-text', 'Add files to') }}</label>
            <div class="collection__dropdown">
              <v-select :options="collections" :searchable="true"
                :placeholder="$trans('dam.select-collection', 'Select collection')"
                @input="updateMetadata($event, 'collection')">
              </v-select>
            </div>
          </div>
          <div class="medialibrary__actions">
            <a17-button variant="outline" @click="close" class="modal__close">{{ $trans('dam.cancel', 'Cancel') }}</a17-button>
            <a17-button v-if="mediaItems.length > 0" variant="validate" @click="openMetadataModal" :disabled="disabled">{{ btnLabel }}</a17-button>
            <a17-button v-else variant="validate" @click="$refs.uploader.$el.querySelector('input[type=file]').click()">{{ $trans('dam.browse-files', 'Browse files') }}</a17-button>
          </div>
        </div>
      </template>
      <div class="medialibrary dam-medialibrary">
        <div class="medialibrary__frame">
          <div class="medialibrary__inner">
            <div class="medialibrary__grid">
              <div class="medialibrary__list" ref="list">
                <a17-uploader ref="uploader" v-if="authorized" @loaded="addSavedMedia" @added="addMedia"
                  @uploaded="uploadSuccess" :type="currentTypeObject" />
                <div class="medialibrary__list-items">
                  <a17-mediagrid :items="renderedMediaItems" :selected-items="selectedMedias"
                    @deleteMedia="deleteMedia" />
                  <a17-spinner v-if="loading" class="medialibrary__spinner">{{ $trans('dam.loading', 'Loading') }}&hellip;</a17-spinner>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </a17-dam-modal>
    <a17-modal ref="metadataModal" :title="$trans('dam.assign-metadata', 'Assign metadata')">
      <div class="modal__metadata__content">
        <a17-vselect label="Tags" name="tags" :multiple="true" :searchable="true" :taggable="true" :push-tags="true"
          in-store="inputValue" @change="updateMetadata($event, 'tags')"></a17-vselect>
        <a17-vselect label="Disciplines" name="disciplines" :options="disciplines" :in-modal="true" :multiple="true"
          in-store="inputValue" @change="updateMetadata($event, 'disciplines')">
        </a17-vselect>
        <a17-vselect label="Sectors" :options="sectors" name="sectors" :in-modal="true" :multiple="true"
          in-store="inputValue" @change="updateMetadata($event, 'sectors')">
        </a17-vselect>
        <a17-inputframe label="Project" name="browsers.projects">
          <a17-browserfield name="project" itemLabel="project" browserNote="project" :endpoint="endpoint"
            :max="1"></a17-browserfield>
        </a17-inputframe>
        <a17-inputframe>
          <a17-button type="submit" name="create" variant="validate" @click="saveFiles">{{ uploadBtnLabel }}</a17-button>
        </a17-inputframe>
      </div>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import a17Spinner from '@/components/Spinner.vue'
  import { MEDIA_LIBRARY } from '@/store/mutations'
  import a17MediaGrid from './MediaGrid.vue'
  import a17Uploader from './Uploader.vue'
  import a17DamModal from './Modal.vue'
  import vSelect from "vue-select";
  export default {
    name: 'A17DAMMedialibrary',
    components: {
      'a17-uploader': a17Uploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-spinner': a17Spinner,
      'a17-dam-modal': a17DamModal,
      'v-select': vSelect
    },
    props: {
      initialPage: {
        type: Number,
        default: 1
      },
      authorized: {
        type: Boolean,
        default: false
      },
      showInsert: {
        type: Boolean,
        default: true
      },
      extraMetadatas: {
        type: Array,
        default() {
          return []
        }
      },
      translatableMetadatas: {
        type: Array,
        default() {
          return []
        }
      }
    },
    data: function () {
      return {
        loading: false,
        maxPage: 20,
        mediaItems: [],
        selectedMedias: [],
        gridHeight: 0,
        page: this.initialPage,
        tags: [],
        lastScrollTop: 0,
        gridLoaded: false,
        metadata: {},
      }
    },
    computed: {
      renderedMediaItems: function () {
        return this.mediaItems.map((item) => {
          item.disabled = (this.filesizeMax > 0 && item.filesizeInMb > this.filesizeMax) ||
            (this.widthMin > 0 && item.width < this.widthMin) ||
            (this.heightMin > 0 && item.height < this.heightMin)
          return item
        })
      },
      currentTypeObject: function () {
        return this.types.find((type) => {
          return type.value === this.type
        })
      },
      endpoint: function () {
        return this.currentTypeObject.endpoint
      },
      btnLabel: function () {
        if (this.mediaItems.length === 1) {
          return `${this.$trans('dam.add', 'Add')} ` + this.mediaItems.length + ` ${this.$trans('dam.file', 'file')}`
        } else if (this.mediaItems.length > 0) {
          return `${this.$trans('dam.add', 'Add')} ` + this.mediaItems.length + ` ${this.$trans('dam.files', 'files')}`
        }
        return this.$trans('dam.add-files', 'Add files')
      },
      uploadBtnLabel: function () {
        if (this.mediaItems.length === 1) {
          return `${this.$trans('dam.upload', 'Upload')} ` + this.mediaItems.length + ` ${this.$trans('dam.file', 'file')}`
        } else if (this.mediaItems.length > 0) {
          return `${this.$trans('dam.upload', 'Upload')} ` + this.mediaItems.length + ` ${this.$trans('dam.files', 'files')}`
        }
        return this.$trans('dam.upload-files', 'Upload files')
      },
      disabled: function () {
        return this.mediaItems.length < 1;
      },

      ...mapState({
        connector: state => state.mediaLibrary.connector,
        max: state => state.mediaLibrary.max,
        filesizeMax: state => state.mediaLibrary.filesizeMax,
        widthMin: state => state.mediaLibrary.widthMin,
        heightMin: state => state.mediaLibrary.heightMin,
        type: state => state.mediaLibrary.type, // image, video, file
        types: state => state.mediaLibrary.types,
        strict: state => state.mediaLibrary.strict,
        selected: state => state.mediaLibrary.selected,
        collections: state => state.mediaLibrary.forUploadCollections,
        sectors: state => state.mediaLibrary.forUploadSectors,
        disciplines: state => state.mediaLibrary.forUploadDisciplines,
        endpoint: state => state.mediaLibrary.projectBrowserUrl,
        project: state => state.browser.selected.project

      })
    },
    watch: {

    },
    methods: {
      deleteMedia: function (media) {
        const index = this.mediaItems.findIndex(function (m) {
          return m.id === media.id
        })
        if (index > -1) {
          this.mediaItems.splice(index, 1)
          this.$refs.uploader.removeMedia(media.id)
        }

      },

      openMetadataModal: function () {
        this.$refs.metadataModal.open()
      },
      updateMetadata(event, type) {
        if (type === 'tags') {
          this.metadata[type] = event
        } else {
          const ids = Array.isArray(event) ? event.map(item => item.id) : [event.id]
          this.metadata[type] = ids;
        }

      },
      saveFiles() {
        if (this.project && this.project.length > 0) {
          this.metadata.project = this.project[0].id;
        }
        this.$refs.uploader.uploadFiles(this.metadata)
      },
      open: function () {
        this.$refs.modal.open()
      },
      close: function () {
        this.$refs.uploader.cancelAll()
        this.mediaItems = []
        this.$refs.modal.hide()
      },
      uploadSuccess() {
        this.$refs.modal.hide()
        this.$refs.metadataModal.hide()
        this.mediaItems = []
      },
      opened: function () {
      },
      updateType: function (newType) {

      },
      addSavedMedia: function (media) {
        this.$emit('media-added', media)
      },
      addMedia: function (media) {
        const index = this.mediaItems.findIndex(function (item) {
          return item.id === media.id
        })
        if (index > -1) {
          this.$set(this.mediaItems, index, media)
        } else {
          this.mediaItems.push(media)
        }
      },
      saveAndClose: function () {
        this.$store.commit(MEDIA_LIBRARY.SAVE_MEDIAS, this.selectedMedias)
        this.close()
      }
    }
  }
</script>

<style lang="scss" scoped>
.medialibrary {
  display: block;
  width: 100%;
  min-height: 100%;
  padding: 0;
  position: relative;
}

.medialibrary__header {
  display: flex;
  flex-flow: column;
  width: 100%; 
  padding: 1.25rem;
  color: $color__grey--54;
  gap: rem-calc(20);

  @include breakpoint('small+') {
    flex-flow: row;
    justify-content: space-between;
    align-items: center;
  }
}

.medialibrary__collections,
.medialibrary__actions {
  display: flex;
  flex-direction: row;
  gap: 1.25rem;
  align-items: center;
  justify-content: center;
}

.medialibrary__actions .button--outline {
  color: $color__grey--54;
}

.medialibrary__frame {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-flow: column nowrap;
}

.medialibrary__inner {
  position: relative;
  width: 100%;
  overflow: hidden;
  flex-grow: 1;
}

.medialibrary__list {
  margin: 0;
  position: absolute;
  inset: 0;
  overflow: auto;
  padding: rem-calc(20);
  display: flex;
  flex-flow: column;
}

.medialibrary__list-items {
  position: relative;
  display: block;
  width: 100%;
}

.modal__metadata__content {
  display: flex;
  flex-flow: column;
  padding-bottom: 1rem;
}

.mediagrid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: auto;
  height: auto;
  line-height: normal;
  gap: rem-calc(16);
  margin-top: rem-calc(20);

  &:empty {
    margin: 0;
  }

  @include breakpoint('small+') {
    grid-template-columns: repeat(3, 1fr);
    gap: rem-calc(20);
  }

  @include breakpoint('medium+') {
    grid-template-columns: repeat(4, 1fr);
  }

  @include breakpoint('large+') {
    grid-template-columns: repeat(5, 1fr);
  }
}

.dam__add {
  height: 100%;
  min-height: calc(25% - rem-calc(10));
}
</style>

<style lang="scss">
.medialibrary__filter-item {
  .vselect {
    min-width: 200px;
  }
}

.medialibrary__filter-item.checkbox {
  margin-top: 8px;
  margin-right: 45px !important;
}

.modal.modal--upload {
  padding: 0;
  background: $color__black--90;

  .modal__header {
    border-radius: 0;
    padding: 0;
    border-bottom: 1px solid $color__grey--85;
  }

  .uploader__dropzone {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: $color__grey--54;
    border-color: $color__grey--54;

    .button {
      color: $color__white;
      border-color: $color__white;
    }

    .uploader__dropzone--desktop {
      margin-top: 0;
    }
  }
}

.collection__dropdown {
  width: rem-calc(220);
  
  .vs__dropdown-toggle {
    min-height: rem-calc(36);
    border: 1px solid $color__fborder;
    border-radius: 2px;
    padding: 0;
  }

  .vs__selected {
    margin: 0;
    padding: 0;
  }

  .v-select input[type=search],
  .v-select input[type=search]:focus {
    color: $color__grey--54;
    padding:0;

    &::placeholder {
      color: $color__grey--54;
    }
  }

  .v-select .vs__search {
    padding: 0 rem-calc(16);
  }

  .vs__selected-options {
    padding:  0 rem-calc(16);
  }

  .vs__actions {
    width: rem-calc(28);
    flex-shrink: 0;
    padding: 0;
    background-repeat: no-repeat;
    background-position: center center;
    background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 8L10 12L14 8H6Z' fill='%23757575'/%3E%3C/svg%3E%0A");

    svg {
      display: none;
    }
  }

  .v-select .vs__dropdown-menu {
    box-shadow: 0px 1px 3.5px 0px rgba(0, 0, 0, 0.30);
    top: 100%;
    margin-top: rem-calc(4);
    border-radius: 2px;
  }
}

.dam-medialibrary .mediagrid__item {
  width: auto;
  padding-bottom: 0;
  background: none;
  aspect-ratio: 1/1;
}

.dam-medialibrary .mediagrid__button {
  position: relative;
  top: auto;
  left: auto;
  right: auto;
  bottom: auto;
  width: 100%;
  height: 100%;

  &.s--picked::after {
    // TODO: move to colors
    border-color: #077FD7;
  }
}
</style>