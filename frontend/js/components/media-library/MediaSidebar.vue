<template>
  <div class="mediasidebar">
    <a17-mediasidebar-upload v-if="mediasLoading.length"/>
    <template v-else>
      <div class="mediasidebar__inner" :class="containerClasses">
        <p v-if="!hasMedia" class="f--note">{{ $trans('media-library.sidebar.empty-text', 'No file selected') }}</p>
        <p v-if="hasMultipleMedias" class="mediasidebar__info">
          {{ medias.length }} {{ $trans('media-library.sidebar.files-selected', 'files selected') }} <a href="#" @click.prevent="clear">{{ $trans('media-library.sidebar.clear', 'Clear') }}</a>
        </p>

        <template v-if="hasSingleMedia">
          <img v-if="isImage" :src="firstMedia.thumbnail" class="mediasidebar__img" :alt="firstMedia.original"/>
          <p class="mediasidebar__name">{{ firstMedia.name }}</p>
          <ul class="mediasidebar__metadatas">
            <li class="f--small" v-if="firstMedia.size" >File size: {{ firstMedia.size | uppercase }}</li>
            <li class="f--small" v-if="isImage && (firstMedia.width + firstMedia.height)">
              {{ $trans('media-library.sidebar.dimensions', 'Dimensions') }}: {{ firstMedia.width }} &times; {{ firstMedia.height }}
            </li>
          </ul>
        </template>

        <a17-buttonbar class="mediasidebar__buttonbar" v-if="hasMedia">
          <!-- Actions -->
          <a v-if="hasSingleMedia" :href="firstMedia.original" download><span v-svg symbol="download"></span></a>
          <button v-if="allowDelete && authorized" type="button" @click="deleteSelectedMediasValidation">
            <span v-svg symbol="trash"></span>
          </button>
          <button v-else type="button" class="button--disabled" :data-tooltip-title="warningDeleteMessage" v-tooltip>
            <span v-svg symbol="trash"></span></button>
          <button v-if="hasSingleMedia" type="button" @click="replaceMedia">
            <span v-svg symbol="replace"></span>
          </button>
        </a17-buttonbar>
      </div>

      <form v-if="hasMedia" ref="form" class="mediasidebar__inner mediasidebar__form" @submit="submit">
        <span class="mediasidebar__loader" v-if="loading"><span class="loader loader--small"><span></span></span></span>
        <a17-vselect v-if="!fieldsRemovedFromBulkEditing.includes('tags')" :label="$trans('media-library.sidebar.tags')"
                     :key="firstMedia.id + '-' + medias.length" name="tags" :multiple="true"
                     :selected="hasMultipleMedias ? sharedTags : firstMedia.tags" :searchable="true"
                     :emptyText="$trans('media-library.no-tags-found', 'Sorry, no tags found.')" :taggable="true" :pushTags="true" size="small"
                     :endpoint="type.tagsEndpoint" @change="save" maxHeight="175px"/>
        <span
          v-if="extraMetadatas.length && isImage && hasMultipleMedias && !fieldsRemovedFromBulkEditing.includes('tags')"
          class="f--tiny f--note f--underlined" @click="removeFieldFromBulkEditing('tags')"
          data-tooltip-title="Remove this field if you do not want to update it on all selected medias"
          data-tooltip-theme="default" data-tooltip-placement="top" v-tooltip>Remove from bulk edit</span>
        <template v-if="hasMultipleMedias">
          <input type="hidden" name="ids" :value="mediasIds"/>
        </template>
        <template v-else>
          <input type="hidden" name="id" :value="firstMedia.id"/>
          <div class="mediasidebar__langswitcher" v-if="translatableMetadatas.length > 0">
            <a17-langswitcher :in-modal="true" :all-published="true"/>
          </div>

          <a17-locale type="a17-textfield" v-if="isImage && translatableMetadatas.includes('alt_text')"
                      :attributes="{ label: $trans('media-library.sidebar.alt-text', 'Alt text'), name: 'alt_text', type: 'text', size: 'small' }"
                      :keepInDom="true"
                      :initialValues="altValues" @focus="focus" @blur="blur"></a17-locale>
          <a17-textfield v-else-if="isImage" :label="$trans('media-library.sidebar.alt-text', 'Alt text')" name="alt_text"
                         :initialValue="firstMedia.metadatas.default.altText" size="small" @focus="focus" @blur="blur"/>

          <template v-if="useWysiwyg">
            <a17-locale type="a17-wysiwyg" v-if="isImage && translatableMetadatas.includes('caption')"
                        :attributes="{ options: wysiwygOptions, label: $trans('media-library.sidebar.caption', 'Caption'), name: 'caption', size: 'small' }"
                        :keepInDom="true"
                        :initialValues="captionValues" @focus="focus" @blur="blur"></a17-locale>
            <a17-wysiwyg v-else-if="isImage" type="textarea" :rows="1" size="small" :label="$trans('media-library.sidebar.caption', 'Caption')" name="caption"
                           :options="wysiwygOptions"
                           :initialValue="firstMedia.metadatas.default.caption" @focus="focus" @blur="blur"/>
          </template>
          <template v-else>
            <a17-locale type="a17-textfield" v-if="isImage && translatableMetadatas.includes('caption')"
                        :attributes="{ type: 'textarea', rows: 1, label: $trans('media-library.sidebar.caption', 'Caption'), name: 'caption', size: 'small' }"
                        :keepInDom="true"
                        :initialValues="captionValues" @focus="focus" @blur="blur"></a17-locale>
            <a17-textfield v-else-if="isImage" type="textarea" :rows="1" size="small" :label="$trans('media-library.sidebar.caption', 'Caption')" name="caption"
                           :initialValue="firstMedia.metadatas.default.caption" @focus="focus" @blur="blur"/>
          </template>

          <template v-for="field in singleOnlyMetadatas">
            <a17-locale type="a17-textfield" v-bind:key="field.name"
                        v-if="isImage && (field.type === 'text' || !field.type) && translatableMetadatas.includes(field.name)"
                        :keepInDom="true"
                        :attributes="{ label: field.label, name: field.name, type: 'textarea', rows: 1, size: 'small' }"
                        :initialValues="firstMedia.metadatas.default[field.name]" @focus="focus" @blur="blur"/>
            <a17-textfield v-bind:key="field.name" v-else-if="isImage && (field.type === 'text' || !field.type)"
                           :label="field.label" :name="field.name" size="small"
                           :initialValue="firstMedia.metadatas.default[field.name]" type="textarea" :rows="1"
                           @focus="focus" @blur="blur"/>
            <div class="mediasidebar__checkbox"
                 v-if="isImage && (field.type === 'checkbox')"
                 v-bind:key="field.name">
              <a17-checkbox :label="field.label" :name="field.name"
                            :initialValue="firstMedia.metadatas.default[field.name]" :value="1" @change="blur"/>
            </div>
          </template>
        </template>
        <template v-for="field in singleAndMultipleMetadatas">
          <a17-locale type="a17-textfield" v-bind:key="field.name"
                      v-if="isImage && (field.type === 'text' || !field.type)&& ((hasMultipleMedias && !fieldsRemovedFromBulkEditing.includes(field.name)) || hasSingleMedia) && translatableMetadatas.includes(field.name)"
                      :keepInDom="true"
                      :attributes="{ label: field.label, name: field.name, type: 'textarea', rows: 1, size: 'small' }"
                      :initialValues="sharedMetadata(field.name, 'object')" @focus="focus" @blur="blur"/>
          <a17-textfield v-bind:key="field.name"
                         v-else-if="isImage && (field.type === 'text' || !field.type) && ((hasMultipleMedias && !fieldsRemovedFromBulkEditing.includes(field.name)) || hasSingleMedia)"
                         :label="field.label" :name="field.name" size="small" :initialValue="sharedMetadata(field.name)"
                         type="textarea" :rows="1" @focus="focus" @blur="blur"/>
          <div class="mediasidebar__checkbox"
               v-bind:key="field.name"
               v-if="isImage && (field.type === 'checkbox') && ((hasMultipleMedias && !fieldsRemovedFromBulkEditing.includes(field.name)) || hasSingleMedia)">
            <a17-checkbox v-bind:key="field.name" :label="field.label" :name="field.name"
                          :initialValue="sharedMetadata(field.name, 'boolean')" :value="1" @change="blur"/>
          </div>
          <span class="f--tiny f--note f--underlined" @click="removeFieldFromBulkEditing(field.name)"
                v-if="isImage && hasMultipleMedias && !fieldsRemovedFromBulkEditing.includes(field.name)"
                v-bind:key="field.name"
                data-tooltip-title="Remove this field if you do not want to update it on all selected medias"
                data-tooltip-theme="default" data-tooltip-placement="top" v-tooltip>Remove from bulk edit</span>
        </template>
      </form>
    </template>

    <a17-modal class="modal--tiny modal--form modal--withintro" ref="warningDelete" title="Warning Delete">
      <p class="modal--tiny-title"><strong>{{ $trans('media-library.dialogs.delete.title', 'Are you sure ?') }}</strong></p>
      <p>{{ warningDeleteMessage }}</p>
      <a17-inputframe>
        <a17-button variant="validate" @click="deleteSelectedMedias">Delete ({{ mediasIdsToDelete.length }})
        </a17-button>
        <a17-button variant="aslink" @click="$refs.warningDelete.close()"><span>Cancel</span></a17-button>
      </a17-inputframe>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import api from '@/store/api/media-library'
  import { NOTIFICATION } from '@/store/mutations'
  import isEqual from 'lodash/isEqual'
  import FormDataAsObj from '@/utils/formDataAsObj.js'
  import a17VueFilters from '@/utils/filters.js'
  import a17MediaSidebarUpload from '@/components/media-library/MediaSidebarUpload'
  import a17Langswitcher from '@/components/LangSwitcher'

  export default {
    name: 'A17MediaSidebar',
    components: {
      'a17-mediasidebar-upload': a17MediaSidebarUpload,
      'a17-langswitcher': a17Langswitcher
    },
    props: {
      medias: {
        default: function () { return [] }
      },
      authorized: {
        type: Boolean,
        default: false
      },
      type: {
        type: Object,
        required: true
      },
      extraMetadatas: {
        type: Array,
        default () {
          return []
        }
      },
      translatableMetadatas: {
        type: Array,
        default () {
          return []
        }
      }
    },
    data: function () {
      return {
        loading: false,
        focused: false,
        previousSavedData: {},
        fieldsRemovedFromBulkEditing: []
      }
    },
    filters: a17VueFilters,
    watch: {
      medias: function () {
        this.fieldsRemovedFromBulkEditing = []
      }
    },
    computed: {
      firstMedia: function () {
        return this.hasMedia ? this.medias[0] : null
      },
      hasMultipleMedias: function () {
        return this.medias.length > 1
      },
      hasSingleMedia: function () {
        return this.medias.length === 1
      },
      hasMedia: function () {
        return this.medias.length > 0
      },
      isImage: function () {
        return this.type.value === 'image'
      },
      sharedTags: function () {
        return this.medias.map((media) => {
          return media.tags
        }).reduce((allTags, currentTags) => allTags.filter(tag => currentTags.includes(tag)))
      },
      sharedMetadata () {
        return (name, type) => {
          if (!this.hasMultipleMedias) {
            return typeof this.firstMedia.metadatas.default[name] === 'object' || type === 'boolean' ? this.firstMedia.metadatas.default[name] : {}
          }

          return this.medias.map((media) => {
            return media.metadatas.default[name]
            // eslint-disable-next-line eqeqeq
          }).every((val, i, arr) => Array.isArray(val) ? (val[0] == arr[0]) : (val == arr[0])) ? this.firstMedia.metadatas.default[name] : (type === 'object' ? {} : type === 'boolean' ? false : '')
        }
      },
      captionValues () {
        return typeof this.firstMedia.metadatas.default.caption === 'object' ? this.firstMedia.metadatas.default.caption : {}
      },
      altValues () {
        return typeof this.firstMedia.metadatas.default.altText === 'object' ? this.firstMedia.metadatas.default.altText : {}
      },
      mediasIds: function () {
        return this.medias.map(function (media) { return media.id }).join(',')
      },
      mediasIdsToDelete: function () {
        return this.medias.filter(media => media.deleteUrl).map(media => media.id)
      },
      mediasIdsToDeleteString: function () {
        return this.mediasIdsToDelete.join(',')
      },
      allowDelete: function () {
        return this.medias.every((media) => {
          return media.deleteUrl
        }) || (this.hasMultipleMedias && !this.medias.every((media) => {
          return !media.deleteUrl
        }))
      },
      warningDeleteMessage: function () {
        if (this.allowDelete) {
          if (this.hasMultipleMedias) {
            return this.$trans('media-library.dialogs.delete.allow-delete-multiple-medias', 'Some files are used and can\'t be deleted. Do you want to delete the others ?')
          } else {
            return this.$trans('media-library.dialogs.delete.allow-delete-one-media', 'This file is used and can\'t be deleted. Do you want to delete the others ?')
          }
        } else {
          if (this.hasMultipleMedias) {
            return this.$trans('media-library.dialogs.delete.dont-allow-delete-multiple-medias', 'This files are used and can\'t be deleted.')
          } else {
            return this.$trans('media-library.dialogs.delete.dont-allow-delete-one-media', 'This file is used and can\'t be deleted.')
          }
        }
      },
      containerClasses: function () {
        return {
          'mediasidebar__inner--multi': this.hasMultipleMedias,
          'mediasidebar__inner--single': this.hasSingleMedia
        }
      },
      singleAndMultipleMetadatas: function () {
        return this.extraMetadatas.filter(m => m.multiple && !this.translatableMetadatas.includes(m.name))
      },
      singleOnlyMetadatas: function () {
        return this.extraMetadatas.filter(m => !m.multiple || (m.multiple && this.translatableMetadatas.includes(m.name)))
      },
      ...mapState({
        mediasLoading: state => state.mediaLibrary.loading,
        useWysiwyg: state => state.mediaLibrary.config.useWysiwyg,
        wysiwygOptions: state => state.mediaLibrary.config.wysiwygOptions
      })
    },
    methods: {
      replaceMedia: function () {
        // Open confirm dialog if any
        if (this.$root.$refs.replaceWarningMediaLibrary) {
          this.$root.$refs.replaceWarningMediaLibrary.open(() => {
            this.triggerMediaReplace()
          })
        } else {
          this.triggerMediaReplace()
        }
      },
      triggerMediaReplace: function () {
        this.$emit('triggerMediaReplace', {
          id: this.getMediaToReplaceId()
        })
      },
      deleteSelectedMediasValidation: function () {
        if (this.loading) return false

        if (this.mediasIdsToDelete.length !== this.medias.length) {
          this.$refs.warningDelete.open()
          return
        }

        // Open confirm dialog if any
        if (this.$root.$refs.deleteWarningMediaLibrary) {
          this.$root.$refs.deleteWarningMediaLibrary.open(() => {
            this.deleteSelectedMedias()
          })
        } else {
          this.deleteSelectedMedias()
        }
      },
      deleteSelectedMedias: function () {
        if (this.loading) return false
        this.loading = true

        if (this.hasMultipleMedias) {
          api.bulkDelete(this.firstMedia.deleteBulkUrl, { ids: this.mediasIdsToDeleteString }, (resp) => {
            this.loading = false
            this.$emit('delete', this.mediasIdsToDelete)
            this.$refs.warningDelete.close()
          }, (error) => {
            this.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: error.data.message,
              variant: 'error'
            })
          })
        } else {
          api.delete(this.firstMedia.deleteUrl, (resp) => {
            this.loading = false
            this.$emit('delete', this.mediasIdsToDelete)
            this.$refs.warningDelete.close()
          }, (error) => {
            this.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: error.data.message,
              variant: 'error'
            })
          })
        }
      },
      clear: function () {
        this.$emit('clear')
      },
      getFormData: function (form) {
        return FormDataAsObj(form)
      },
      getMediaToReplaceId: function () {
        return this.firstMedia.id
      },
      removeFieldFromBulkEditing: function (name) {
        this.fieldsRemovedFromBulkEditing.push(name)
      },
      focus: function () {
        this.focused = true
      },
      blur: function () {
        this.focused = false
        this.save()

        const form = this.$refs.form
        const data = this.getFormData(form)

        if (this.hasSingleMedia) {
          if (data.hasOwnProperty('alt_text')) this.firstMedia.metadatas.default.altText = data.alt_text
          else this.firstMedia.metadatas.default.altText = ''

          if (data.hasOwnProperty('caption')) this.firstMedia.metadatas.default.caption = data.caption
          else this.firstMedia.metadatas.default.caption = ''

          this.extraMetadatas.forEach((metadata) => {
            if (data.hasOwnProperty(metadata.name)) {
              this.firstMedia.metadatas.default[metadata.name] = data[metadata.name]
            } else {
              this.firstMedia.metadatas.default[metadata.name] = ''
            }
          })
        } else {
          this.singleAndMultipleMetadatas.forEach((metadata) => {
            if (data.hasOwnProperty(metadata.name)) {
              this.medias.forEach((media) => {
                media.metadatas.default[metadata.name] = data[metadata.name]
              })
            }
          })
        }
      },
      save: function () {
        const form = this.$refs.form
        if (!form) return

        const formData = this.getFormData(form)

        if (!isEqual(formData, this.previousSavedData) && !this.loading) {
          this.previousSavedData = formData
          this.update(form)
        }
      },
      submit: function (event) {
        event.preventDefault()
        this.save()
      },
      update: function (form) {
        if (this.loading) return

        this.loading = true

        const data = this.getFormData(form)
        data.fieldsRemovedFromBulkEditing = this.fieldsRemovedFromBulkEditing

        const url = this.hasMultipleMedias ? this.firstMedia.updateBulkUrl : this.firstMedia.updateUrl // single or multi updates

        api.update(url, data, (resp) => {
          this.loading = false

          // Refresh the select filter displaying all tags
          if (resp.data.tags) this.$emit('tagUpdated', resp.data.tags)

          // Bulk update : Refresh tags
          if (this.hasMultipleMedias && resp.data.items) {
            // Update the tags of all the selected medias
            this.medias.forEach(function (media) {
              resp.data.items.some(function (mediaFromResp) {
                if (mediaFromResp.id === media.id) media.tags = mediaFromResp.tags // replace tags with the one from the response
                return mediaFromResp.id === media.id
              })
            })
          }
        }, (error) => {
          this.loading = false

          if (error.data.message) {
            this.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: error.data.message,
              variant: 'error'
            })
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>

  .mediasidebar {
    a {
      color: $color__link;
      text-decoration: none;

      &:focus,
      &:hover {
        text-decoration: underline;
      }
    }
  }

  .mediasidebar__info {
    margin-bottom: 30px;

    a {
      margin-left: 15px;
    }
  }

  .mediasidebar__inner {
    padding: 20px;
    // overflow: hidden;
  }

  .mediasidebar__img {
    max-width: 135px;
    max-height: 135px;
    height: auto;
    display: block;
    margin-bottom: 17px;
  }

  .mediasidebar__name {
    margin-bottom: 6px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .mediasidebar__metadatas {
    color: $color__text--light;
    margin-bottom: 16px;
  }

  .mediasidebar .mediasidebar__buttonbar {
    display: inline-block;
  }

  .mediasidebar__form {
    border-top: 1px solid $color__border;
    position: relative;

    button {
      margin-top: 16px;
    }

    &.mediasidebar__form--loading {
      opacity: 0.5;
    }
  }

  .mediasidebar__loader {
    position: absolute;
    top: 20px;
    right: 20px + 8px + 8px;
  }

  .mediasidebar__checkbox {
    margin-top: 16px;
  }

  .mediasidebar__langswitcher {
    margin-top: 32px;
    margin-bottom: 32px;
  }
</style>
