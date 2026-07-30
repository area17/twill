<template>
  <div>
    <a17-dam-modal :title="title" mode="wide" ref="modal" @open="opened" class="modal--upload">
      <template #header>
        <div class="medialibrary__header">
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
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </a17-dam-modal>
    <a17-modal ref="visibilityModal" :title="settingsModalTitle">
      <h3 class="visibility__header">{{ $trans('dam.visibility', 'Visibility') }}</h3>
      <div class="modal__metadata__content">
        <div class="dam-asset__modal">
          <a17-switcher v-for="toggle in visibilityToggles" :key="toggle.key" :name="toggle.key.replace(/\s+/g, '')"
            :title="toggle.label" :textEnabled="null" :textDisabled="null" :value="metadata[toggle.key]"
            :disabled="!hasEditPermissions"
            @change="toggleVisibility(toggle.key, $event)"></a17-switcher>
        </div>

        <div v-if="showMetadataFields" class="modal__metadata__content modal__metadata__content--stacked">
          <h3 class="modal__metadata__title">{{ $trans('dam.metadata', 'Metadata') }}</h3>
          <a17-vselect
            v-if="showTagFields"
            label="Tags"
            name="tags"
            :multiple="true"
            :searchable="true"
            :taggable="true"
            :push-tags="true"
            in-store="inputValue"
            @change="updateMetadata($event, 'tags')"
          ></a17-vselect>
          <template v-if="showExtendedMetadataFields">
            <a17-vselect
              label="Disciplines"
              name="disciplines"
              :options="disciplines"
              :in-modal="true"
              :multiple="true"
              in-store="inputValue"
              @change="updateMetadata($event, 'disciplines')"
            >
            </a17-vselect>
            <a17-vselect
              label="Sectors"
              :options="sectors"
              name="sectors"
              :in-modal="true"
              :multiple="true"
              in-store="inputValue"
              @change="updateMetadata($event, 'sectors')"
            >
            </a17-vselect>
            <a17-inputframe label="Project" name="browsers.projects">
              <a17-browserfield
                name="project"
                itemLabel="project"
                browserNote="project"
                :endpoint="endpoint"
                :max="1"
              ></a17-browserfield>
            </a17-inputframe>
          </template>
        </div>

        <a17-inputframe>
          <a17-button type="submit" name="create" variant="validate" @click="saveFiles">{{ uploadBtnLabel }}</a17-button>
        </a17-inputframe>
      </div>
      <a17-spinner v-if="loading" class="medialibrary__spinner">{{ $trans('dam.loading', 'Loading') }}&hellip;</a17-spinner>
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
  import a17Switcher from '@/components/Switcher.vue'
  export default {
    name: 'A17DamMediaLibrary',
    components: {
      'a17-uploader': a17Uploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-spinner': a17Spinner,
      'a17-dam-modal': a17DamModal,
      'a17-switcher': a17Switcher,
    },
    props: {
      title: {
        type: String,
        default() {
          return this.$trans('dam.add-files', 'Add files')
        }
      },
      mode: {
        type: String,
        default: 'landing',
        validator(value) {
          return ['landing', 'project'].includes(value)
        }
      },
      metadataKeys: {
        type: Object,
        default: () => ({
          damProject: 'dam_project',
          pushToArchive: 'push_to_archive',
          showInCmsMediaLibrary: 'show_in_cms',
          showInDam: 'private',
          tags: 'tags',
          disciplines: 'disciplines',
          sectors: 'sectors'
        })
      },
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
        metadata: {
          pushToArchive: false,
          showInCmsMediaLibrary: false,
          showInDam: true,
          tags: [],
          disciplines: [],
          sectors: []
        },
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
      isLandingMode: function () {
        return this.mode === 'landing'
      },
      showMetadataFields: function () {
        return this.showTagFields || this.showExtendedMetadataFields
      },
      showTagFields: function () {
        return this.mode === 'landing' || this.mode === 'project'
      },
      showExtendedMetadataFields: function () {
        return this.isLandingMode
      },
      resolvedProjectId: function () {
        if (this.project && this.project.length > 0) {
          return this.project[0].id
        }

        return this.mode === 'project' ? this.project : null
      },
      resolvedProjectMetadataKey: function () {
        return this.metadataKeys.damProject || 'dam_project'
      },
      settingsModalTitle: function () {
        return this.showMetadataFields
          ? this.$trans('dam.assign-asset-settings', 'Assign asset settings')
          : this.$trans('dam.assign_visibility', 'Assign visibility')
      },
      visibilityToggles: function () {
        return [
          {
            key: 'pushToArchive',
            label: this.$trans('dam.push-to-archive', 'Publish to Archive')
          },
          {
            key: 'showInCmsMediaLibrary',
            label: this.$trans('dam.show-in-cms-media-library', 'Show in CMS media library')
          },
          {
            key: 'showInDam',
            label: this.$trans('dam.private', 'Show in DAM only')
          }
        ]
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
        sectors: state => state.mediaLibrary.forUploadSectors,
        disciplines: state => state.mediaLibrary.forUploadDisciplines,
        endpoint: state => state.mediaLibrary.projectBrowserUrl,
        project: state => state.browser.selected.project,
        hasEditPermissions: state => state.permissions.hasEditPermissions
      })
    },
    watch: {},
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
        this.$refs.visibilityModal.open()
      },
      toggleVisibility: function (key, value) {
        this.metadata[key] = value

        if ((key === 'pushToArchive' || key === 'showInCmsMediaLibrary') && value === true) {
          this.metadata.showInDam = false
        } else if (key === 'showInDam' && value === true) {
          this.metadata.pushToArchive = false
          this.metadata.showInCmsMediaLibrary = false
        }
      },
      updateMetadata(event, type) {
        if (type === 'tags') {
          this.metadata[type] = event
          return
        }

        if (!event) {
          this.metadata[type] = []
          return
        }

        const items = Array.isArray(event) ? event : [event]
        this.metadata[type] = items.map(item => item.id)
      },
      buildUploadMetadata: function () {
        const uploadMetadata = {
          [this.metadataKeys.pushToArchive]: this.metadata.pushToArchive ? 1 : 0,
          [this.metadataKeys.showInCmsMediaLibrary]: this.metadata.showInCmsMediaLibrary ? 1 : 0,
          [this.metadataKeys.showInDam]: this.metadata.showInDam ? 1 : 0
        }

        if (this.showTagFields) {
          uploadMetadata[this.metadataKeys.tags] = this.metadata.tags
        }

        if (this.showExtendedMetadataFields) {
          uploadMetadata[this.metadataKeys.disciplines] = this.metadata.disciplines
          uploadMetadata[this.metadataKeys.sectors] = this.metadata.sectors
        }

        if (this.resolvedProjectId) {
          uploadMetadata[this.resolvedProjectMetadataKey] = this.resolvedProjectId
        }

        return uploadMetadata
      },
      resetMetadata: function () {
        this.metadata = {
          pushToArchive: false,
          showInCmsMediaLibrary: false,
          showInDam: true,
          tags: [],
          disciplines: [],
          sectors: []
        }
      },
      saveFiles() {
        this.loading = true;
        this.$refs.uploader.uploadFiles(this.buildUploadMetadata())
      },
      open: function () {
        this.$refs.modal.open()
      },
      close: function () {
        this.$refs.uploader.cancelAll()
        this.mediaItems = []
        this.resetMetadata()
        this.$refs.visibilityModal.hide()
        this.$refs.modal.hide()
      },
      uploadSuccess() {
        this.$refs.modal.hide()
        this.$refs.visibilityModal.hide()
        this.mediaItems = []
        this.resetMetadata()
        this.loading = false;
      },
      opened: function () {
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
    justify-content: flex-end;
    align-items: center;
  }
}

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
  margin-top: rem-calc(12);
  padding-bottom: rem-calc(20);
}

.modal__metadata__content--stacked {
  margin-top: rem-calc(24);
}



.modal__metadata__title,
.visibility__header {
  @include sans-serif();
  color: $color__text;
  font-size: rem-calc(18);
  font-weight: 600;
}

.modal__metadata__title {
  margin: 0 0 rem-calc(4);
}

.visibility__header {
  margin-top: rem-calc(36);
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

.dam-asset__modal {
  display: flex;
  flex-flow: column;
  gap: rem-calc(12);

  .switcher {
    background: $color__light;
    color: $color__text;
    border-radius: 130px;
    line-height: normal;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: auto;
    min-height: rem-calc(44);
  }

  .switcher.switcher--active {
    background:$color__lightGreen;
    color:$color__publish;
  }

  .switcher__title {
    @include sans-serif();
    font-weight: 400;
  }

  .switcher__button {
    top: 0;
  }

}
</style>
