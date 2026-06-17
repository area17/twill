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
    <a17-modal ref="visibilityModal" :title="$trans('dam.assign_visibility', 'Assign visibility')">
        <h3 class="visibility__header">{{ $trans('dam.visibility', 'Visibility') }}</h3>
      <div class="modal__metadata__content">
        <button
          v-for="toggle in visibilityToggles"
          :key="toggle.key"
          type="button"
          class="visibility-toggle"
          :class="{
            'visibility-toggle--active': metadata[toggle.key],
            'visibility-toggle--dam': toggle.key === 'showInDam'
          }"
          @click="toggleVisibility(toggle.key)"
        >
          <span class="visibility-toggle__label">{{ toggle.label }}</span>
          <span class="visibility-toggle__switch" aria-hidden="true">
            <span class="visibility-toggle__handle"></span>
          </span>
        </button>
        <a17-inputframe>
          <a17-button type="submit" name="create" variant="validate"  @click="saveFiles">{{ uploadBtnLabel }}</a17-button>
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
  export default {
    name: 'A17DamProjectAssetsUploaderModal',
    components: {
      'a17-uploader': a17Uploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-spinner': a17Spinner,
      'a17-dam-modal': a17DamModal
    },
    props: {
      title: {
        type: String,
        default() {
          return this.$trans('dam.add-files', 'Add files')
        }
      },
      projectId: {
        type: [Number, String],
        default: null
      },
      projectKey: {
        type: String,
        default: 'project'
      },
      metadataKeys: {
        type: Object,
        default: () => ({
          pushToArchive: 'push_to_archive',
          showInCmsMediaLibrary: 'show_in_cms_media_library',
          showInDam: 'show_in_dam'
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
          showInDam: true
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
            label: this.$trans('dam.show-in-dam-only', 'Show in DAM only')
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
        this.$refs.visibilityModal.open()
      },
      toggleVisibility: function (key) {
        this.metadata[key] = !this.metadata[key]
      },
      buildUploadMetadata: function () {
        const uploadMetadata = {
          [this.metadataKeys.pushToArchive]: this.metadata.pushToArchive ? 1 : 0,
          [this.metadataKeys.showInCmsMediaLibrary]: this.metadata.showInCmsMediaLibrary ? 1 : 0,
          [this.metadataKeys.showInDam]: this.metadata.showInDam ? 1 : 0
        }

        const resolvedProjectId = this.projectId || (this.project && this.project.length > 0 ? this.project[0].id : null)

        if (resolvedProjectId) {
          uploadMetadata[this.projectKey] = resolvedProjectId
        }

        return uploadMetadata
      },
      resetMetadata: function () {
        this.metadata = {
          pushToArchive: false,
          showInCmsMediaLibrary: false,
          showInDam: true
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
  display: flex;
  flex-flow: column;
  gap: rem-calc(16);
  padding-bottom: 1rem;
  padding-top: 1rem;
}



.modal__metadata__title {
  margin: 0 0 rem-calc(4);
  color: $color__text;
  font-size: rem-calc(18);
  font-weight: 600;
}

.visibility__header {
  margin-top: rem-calc(40);
  margin-bottom: rem-calc(5);
  color: $color__text;
  font-size: rem-calc(18);
  font-weight: 400;
}

.visibility-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: rem-calc(15) rem-calc(40);
  border: 0;
  border-radius: rem-calc(999);
  background: $color__black--5;
  color: $color__text;
  text-align: left;
  transition: background-color .2s ease, color .2s ease;
}

.visibility-toggle--dam {
  background: rgba($color__publish, .18);
  color: $color__publish;
}


.visibility-toggle__label {
  font-size: rem-calc(18);
  line-height: 1.3;
}

.visibility-toggle__switch {
  position: relative;
  display: inline-flex;
  align-items: center;
  width: rem-calc(50);
  height: rem-calc(12);
  padding: rem-calc(3);
  border-radius: rem-calc(999);
  background: $color__grey--54;
  transition: background-color .2s ease;
}

.visibility-toggle--active .visibility-toggle__switch {
  background: $color__publish;
}

.visibility-toggle__handle {
  width: rem-calc(20);
  height: rem-calc(20);
  border-radius: 50%;
  background: $color__white;
  box-shadow: 0 1px 3px rgba($color__black, .2);
  transition: transform .2s ease;
}

.visibility-toggle--active .visibility-toggle__handle {
  transform: translateX(rem-calc(30));
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
