<template>
  <aside class="dam-sidebar">
    <a17-mediasidebar-upload v-if="mediasLoading.length" />
    <template v-else>
      <div class="dam-sidebar__content">
        <div class="dam-sidebar__inner" :class="containerClasses">
          <p v-if="!hasMedia" class="f--note">
            {{ $trans('media-library.sidebar.empty-text', 'No file selected') }}
          </p>
          <p v-if="hasMultipleMedias" class="dam-sidebar__info">
            {{ medias.length }}
            {{
              $trans('media-library.sidebar.files-selected', 'files selected')
            }}
            <a href="#" @click.prevent="clear">{{
              $trans('media-library.sidebar.clear', 'Clear')
            }}</a>
          </p>

          <div class="dam-sidebar__gallery">
            <template v-if="hasSingleMedia">
                <a
              v-if="isImage && hasPreview"
              :href="firstMedia.original"
              :data-pswp-width="firstMedia.width"
              :data-pswp-height="firstMedia.height"
              :data-pswp-video-src="isVideo ? firstMedia.original : null"
              :data-pswp-type="isVideo ? 'video' : 'image'"
              target="_blank"
              rel="noreferrer"
              class="pswp-lightbox"
              :aria-label="$trans('dam.view', 'View')"
            >
            <img
                tabindex="0"
                @keyup.left="handleKeyUp('previous')"
                @keyup.right="handleKeyUp('next')"
                :src="firstMedia.thumbnail"
                class="dam-sidebar__img"
                :alt="firstMedia.name"
              />
            </a>
            
              
            </template>
          </div>

          <template v-if="hasSingleMedia">
            <p class="dam-sidebar__name">{{ firstMedia.title ?? firstMedia.name }}</p>

            <h2 class="visually-hidden" id="metaTitle">
              {{ $trans('dam.meta-title', 'Meta data') }}
            </h2>

            <ul
              class="dam-sidebar__metadatas"
              :aria-owns="getOwnedListItems.join(' ')"
              aria-labelledby="metaTitle"
            >
              <li class="f--small" v-if="firstMedia.title" id="meta__title">
                {{ $trans('dam.title', 'Title') }}:
                {{ firstMedia.title }}
              </li>
              <li class="f--small" v-if="firstMedia.size" id="meta__size">
                {{ $trans('dam.file-size', 'File size') }}:
                {{ firstMedia.size | uppercase }}
              </li>
              <li class="f--small" v-if="firstMedia.creator" id="meta__creator">
                {{ $trans('dam.creator', 'Creator') }}:
                {{ firstMedia.creator }}
              </li>
              <li
                class="f--small"
                v-if="firstMedia.createdDate"
                id="meta__createdDate"
              >
                {{ $trans('dam.created-date', 'Created date') }}:
                {{ firstMedia.createdDate }}
              </li>
            </ul>
            <ul
              :class="[
                'dam-sidebar__metadatas',
                { 'dam-sidebar__metadatas--open': listExpanded }
              ]"
              aria-labelledby="metaTitle"
            >
              <li
                class="f--small"
                v-if="firstMedia.uploadDate"
                id="meta__uploadDate"
              >
                {{ $trans('dam.upload-date', 'Upload date') }}:
                {{ firstMedia.uploadDate }}
              </li>
              <li
                class="f--small"
                v-if="isImage && firstMedia.width + firstMedia.height"
              >
                {{ $trans('media-library.sidebar.dimensions', 'Dimensions') }}:
                {{ firstMedia.width }} &times; {{ firstMedia.height }}
              </li>
              <li
                class="f--small"
                v-if="firstMedia.duration"
                id="meta__duration"
              >
                {{ $trans('dam.duration', 'Duration') }}:
                {{ firstMedia.duration }}
              </li>
              <li
                class="f--small"
                v-if="firstMedia.colorProfile"
                id="meta__colorProfile"
              >
                {{ $trans('dam.color-profile', 'Color profile') }}:
                {{ firstMedia.colorProfile }}
              </li>
              <li class="f--small" v-if="firstMedia.codec" id="meta__codec">
                {{ $trans('dam.codec', 'Codec') }}:
                {{ firstMedia.codec }}
              </li>
              <li
                class="f--small"
                v-if="firstMedia.copyrightNotice"
                id="meta__copyrightNotice"
              >
                {{ $trans('dam.copyright-notice', 'Copyright notice') }}:
                {{ firstMedia.copyrightNotice }}
              </li>
            </ul>
            <a17-button
              variant="aslink"
              :class="[
                'dam-sidebar__meta-toggle',
                { 'dam-sidebar__meta-toggle--expanded': listExpanded }
              ]"
              :aria-expanded="listExpanded ? 'true' : 'false'"
              @click="listExpanded = !listExpanded"
            >
              <span class="f--small">{{
                listExpanded
                  ? $trans('dam.less', 'Less')
                  : $trans('dam.more', 'More')
              }}</span>
              <span
                aria-hidden="true"
                v-svg
                symbol="dropdown_module"
                class="toggle-icon"
              ></span
            ></a17-button>
          </template>

          <a17-buttonbar
            class="dam-sidebar__buttonbar"
            :class="{ 'dam-sidebar__buttonbar--hidden': hasMultipleMedias }"
            v-if="hasMedia"
          >
            <!-- Actions -->
            <a
              :href="firstMedia.editUrl"
              :aria-label="$trans('dam.edit', 'Edit')"
            >
              <span v-svg symbol="edit" aria-hidden="true"></span>
            </a>
            <a
              v-if="isImage && hasPreview"
              :href="firstMedia.original"
              :data-pswp-width="firstMedia.width"
              :data-pswp-height="firstMedia.height"
              :data-pswp-video-src="isVideo ? firstMedia.original : null"
              :data-pswp-type="isVideo ? 'video' : 'image'"
              target="_blank"
              rel="noreferrer"
              class="pswp-lightbox"
              :aria-label="$trans('dam.view', 'View')"
            >
              <span v-svg symbol="preview" aria-hidden="true"></span>
            </a>
            <a v-if="hasSingleMedia" :href="firstMedia.original" download><span v-svg symbol="download"></span></a>

          </a17-buttonbar>
        </div>

        <form
          v-if="hasMedia"
          ref="form"
          class="dam-sidebar__inner dam-sidebar__form"
          @submit="submit"
        >
          <span class="dam-sidebar__loader" v-if="loading"
            ><span class="loader loader--small"><span></span></span
          ></span>

          <template v-if="hasMultipleMedias">
            <input type="hidden" name="ids" :value="mediasIds" />
          </template>
          <template v-else>
            <input type="hidden" name="id" :value="firstMedia.id" />
            <div
              class="dam-sidebar__langswitcher"
              v-if="translatableMetadatas.length > 0"
            >
              <a17-langswitcher :in-modal="true" :all-published="true" />
            </div>

            <!--
            <a17-locale
              type="a17-textfield"
              v-if="isImage && translatableMetadatas.includes('alt_text')"
              :attributes="{
                label: $trans('media-library.sidebar.alt-text', 'Alt text'),
                name: 'alt_text',
                type: 'text',
                size: 'small'
              }"
              :keepInDom="true"
              :initialValues="altValues"
              @focus="focus"
              @blur="blur"
            ></a17-locale>
            <a17-textfield
              v-else-if="isImage"
              :label="$trans('media-library.sidebar.alt-text', 'Alt text')"
              name="alt_text"
              :initialValue="firstMedia.metadatas.default.altText"
              size="small"
              @focus="focus"
              @blur="blur"
            />

            <template v-if="useWysiwyg">
              <a17-locale
                type="a17-wysiwyg"
                v-if="isImage && translatableMetadatas.includes('caption')"
                :attributes="{
                  options: wysiwygOptions,
                  label: $trans('media-library.sidebar.caption', 'Caption'),
                  name: 'caption',
                  size: 'small'
                }"
                :keepInDom="true"
                :initialValues="captionValues"
                @focus="focus"
                @blur="blur"
              ></a17-locale>
              <a17-wysiwyg
                v-else-if="isImage"
                type="textarea"
                :rows="1"
                size="small"
                :label="$trans('media-library.sidebar.caption', 'Caption')"
                name="caption"
                :options="wysiwygOptions"
                :initialValue="firstMedia.metadatas.default.caption"
                @focus="focus"
                @blur="blur"
              />
            </template>
            <template v-else>
              <a17-locale
                type="a17-textfield"
                v-if="isImage && translatableMetadatas.includes('caption')"
                :attributes="{
                  type: 'textarea',
                  rows: 1,
                  label: $trans('media-library.sidebar.caption', 'Caption'),
                  name: 'caption',
                  size: 'small'
                }"
                :keepInDom="true"
                :initialValues="captionValues"
                @focus="focus"
                @blur="blur"
              ></a17-locale>
              <a17-textfield
                v-else-if="isImage"
                type="textarea"
                :rows="1"
                size="small"
                :label="$trans('media-library.sidebar.caption', 'Caption')"
                name="caption"
                :initialValue="firstMedia.metadatas.default.caption"
                @focus="focus"
                @blur="blur"
              />
            </template> -->
            <template v-for="(field, i) in singleOnlyMetadatas">
              <a17-locale
                type="a17-textfield"
                v-bind:key="field.name"
                v-if="
                  isImage &&
                    (field.type === 'text' || !field.type) &&
                    translatableMetadatas.includes(field.name)
                "
                :keepInDom="true"
                :attributes="{
                  label: field.label,
                  name: field.name,
                  type: 'textarea',
                  rows: 1,
                  size: 'small'
                }"
                :initialValues="firstMedia.metadatas.default[field.name]"
                @focus="focus"
                @blur="blur"
              />
              <a17-textfield
                v-bind:key="field.name + i"
                v-else-if="isImage && (field.type === 'text' || !field.type)"
                :label="field.label"
                :name="field.name"
                size="small"
                :initialValue="firstMedia.metadatas.default[field.name]"
                type="textarea"
                :rows="1"
                @focus="focus"
                @blur="blur"
              />
              <div
                class="dam-sidebar__checkbox"
                v-if="isImage && field.type === 'checkbox'"
                v-bind:key="field.name"
              >
                <a17-checkbox
                  :label="field.label"
                  :name="field.name"
                  :initialValue="firstMedia.metadatas.default[field.name]"
                  :value="1"
                  @change="blur"
                />
              </div>
            </template>
          </template>

          <template v-for="field in browserFields">
              <div
                class="dam-sidebar__editable"
                v-bind:key="field.name"
              >
                <div class="dam-sidebar__editable-header">
                  <h3 class="f--small">{{ field.label }}</h3>
                  <a17-button variant="aslink" @click="openBrowser(field)">
                    <span class="f--small">{{ $trans('dam.add', 'Add') }}</span>
                  </a17-button>
                </div>
                <ul>
                  <li
                    v-for="(link, i) in getSharedBrowserItems(field.name)"
                    :key="i"
                    class="f--small"
                  >
                    <a17-button el="a" variant="aslink" :href="link.edit" target="_blank"
                      ><span>{{ link.name }}</span></a17-button
                    >
<!--                    <a17-button variant="aslink-grey"-->
<!--                      ><span>{{-->
<!--                        $trans('dam.remove', 'Remove')-->
<!--                      }}</span></a17-button-->
<!--                    >-->
                  </li>
                </ul>
              </div>
            </template>


          <template>
            <div
              :class="[
                'dam-sidebar__tags',
                { 'dam-sidebar__tags--edit': editTagsOpen }
              ]"
            >
              <template v-if="!editTagsOpen">
                <div class="dam-sidebar__editable-header">
                  <h3 class="f--small">{{ $trans('dam.tags', 'Tags') }}</h3>
                  <a17-button variant="aslink" @click="editTagsOpen = true">
                    <span class="f--small">{{
                      $trans('dam.edit', 'Edit')
                    }}</span>
                  </a17-button>
                </div>
                <ul class="dam-sidebar__tags-static">
                  <template v-for="(field, i) in tagFields">
                    <li
                      v-for="(tag, tagIndex) in getSharedItems(field.name, field.key)"
                      v-bind:key="`tag_${i}_${tagIndex}`"
                    >
                      {{ tag }}
                    </li>
                  </template>
                </ul>
              </template>
              <template v-else v-for="field in tagFields">
                <div v-bind:key="field.name">
                  <a17-vselect
                    :label="field.label"
                    :name="field.name"
                    :options="tagEndpoints[field.name].options"
                    :multiple="field.multiple"
                    :selected="getSharedItems(field.name)"
                    :searchable="field.searchable ?? false"
                    :emptyText="`Sorry, no ${field.name} found.`"
                    :taggable="field.taggable ?? false"
                    :pushTags="true"
                    :endpoint="tagEndpoints[field.name].endpoint"
                    maxHeight="175px"
                  />
                </div>
              </template>
            </div>
          </template>

          <template v-for="(field, i) in singleAndMultipleMetadatas">
            <a17-locale
              type="a17-textfield"
              v-bind:key="field.name"
              v-if="
                isImage &&
                  (field.type === 'text' || !field.type) &&
                  ((hasMultipleMedias &&
                    !fieldsRemovedFromBulkEditing.includes(field.name)) ||
                    hasSingleMedia) &&
                  translatableMetadatas.includes(field.name)
              "
              :keepInDom="true"
              :attributes="{
                label: field.label,
                name: field.name,
                type: 'textarea',
                rows: 1,
                size: 'small'
              }"
              :initialValues="sharedMetadata(field.name, 'object')"
              @focus="focus"
              @blur="blur"
            />

            <a17-textfield
              v-bind:key="field.name + i"
              v-else-if="
                isImage &&
                  (field.type === 'text' || !field.type) &&
                  ((hasMultipleMedias &&
                    !fieldsRemovedFromBulkEditing.includes(field.name)) ||
                    hasSingleMedia)
              "
              :label="field.label"
              :name="field.name"
              size="small"
              :initialValue="sharedMetadata(field.name)"
              type="textarea"
              :rows="1"
              @focus="focus"
              @blur="blur"
            />

            <div
              class="dam-sidebar__checkbox"
              v-bind:key="field.name"
              v-if="
                isImage &&
                  field.type === 'checkbox' &&
                  ((hasMultipleMedias &&
                    !fieldsRemovedFromBulkEditing.includes(field.name)) ||
                    hasSingleMedia)
              "
            >
              <a17-checkbox
                v-bind:key="field.name"
                :label="field.label"
                :name="field.name"
                :initialValue="sharedMetadata(field.name, 'boolean')"
                :value="1"
                @change="blur"
              />
            </div>
          </template>
        </form>
      </div>
      <div
        class="dam-sidebar__inner dam-sidebar__action"
      >
        <a17-button
          @click="save"
          v-if="editTagsOpen"
          variant="validate"
        >Update</a17-button
        >

        <a17-button
          v-else-if="getDownloadLink"
          :href="getDownloadLink"
          el="a"
          variant="action"
          download=""
          >{{ hasMultipleMedias ? 'Download all' : 'Download' }}</a17-button
        >
      </div>
    </template>

    <a17-modal
      class="modal--tiny modal--form modal--withintro"
      ref="warningDelete"
      title="Warning Delete"
    >
      <p class="modal--tiny-title">
        <strong>{{
          $trans('media-library.dialogs.delete.title', 'Are you sure ?')
        }}</strong>
      </p>
      <p>{{ warningDeleteMessage }}</p>
      <a17-inputframe>
        <a17-button variant="validate" @click="deleteSelectedMedias"
          >{{ $trans('dam.delete', 'Delete') }} ({{ mediasIdsToDelete.length }})
        </a17-button>
        <a17-button variant="aslink" @click="$refs.warningDelete.close()"
          ><span>{{ $trans('dam.cancel', 'Cancel') }}</span></a17-button
        >
      </a17-inputframe>
    </a17-modal>


    <a17-modal class="modal--browser" ref="damBrowser" mode="medium" :force-close="true">
      <a17-dambrowser @saveAndClose="saveBrowser"></a17-dambrowser>
    </a17-modal>
  </aside>
</template>

<script>
  import isEqual from 'lodash/isEqual'
  import { mapState } from 'vuex'
  import PhotoSwipeLightbox from 'photoswipe/lightbox'
  import PhotoSwipeVideoPlugin from 'photoswipe-video-plugin/dist/photoswipe-video-plugin.esm';

  import 'photoswipe/style.css'

  import a17Langswitcher from '@/components/LangSwitcher'
  import a17MediaSidebarUpload from '@/components/media-library/MediaSidebarUpload'
  import api from '@/store/api/media-library'
  import {BROWSER, NOTIFICATION} from '@/store/mutations'
  import a17VueFilters from '@/utils/filters.js'
  import FormDataAsObj from '@/utils/formDataAsObj.js'
  import Extensions from '@/components/files/Extensions.js'

  export default {
    name: 'A17DamSidebar',
    components: {
      'a17-mediasidebar-upload': a17MediaSidebarUpload,
      'a17-langswitcher': a17Langswitcher
    },
    props: {
      medias: {
        default: function() {
          return []
        }
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
    data: function() {
      return {
        metaItems: [
          'title',
          'fileSize',
          'creator',
          'createdDate',
          'uploadDate',
          'dimensions',
          'duration',
          'colorProfile',
          'codec',
          'copyrightNotice'
        ],
        lightbox: null,
        listExpanded: false,
        loading: false,
        focused: false,
        previousSavedData: {},
        fieldsRemovedFromBulkEditing: [],
        editTagsOpen: false,
        isOpen: true
      }
    },
    filters: a17VueFilters,
    watch: {
      medias: function() {
        this.fieldsRemovedFromBulkEditing = []
      }
    },
    computed: {
      firstMedia: function() {
        return this.hasMedia ? this.medias[0] : null
      },
      hasMultipleMedias: function() {
        return this.medias.length > 1
      },
      hasSingleMedia: function() {
        return this.medias.length === 1
      },
      hasMedia: function() {
        return this.medias.length > 0
      },
      isImage: function() {
        return this.type.value === 'image'
      },
      sharedMetadata() {
        return (name, type) => {
          if (!this.hasMultipleMedias) {
            return typeof this.firstMedia.metadatas.default[name] ===
              'object' || type === 'boolean'
              ? this.firstMedia.metadatas.default[name]
              : {}
          }

          return this.medias
            .map(media => {
              return media.metadatas.default[name]
              // eslint-disable-next-line eqeqeq
            })
            .every((val, i, arr) =>
              Array.isArray(val) ? val[0] === arr[0] : val === arr[0]
            )
            ? this.firstMedia.metadatas.default[name]
            : type === 'object'
              ? {}
              : type === 'boolean'
                ? false
                : ''
        }
      },
      captionValues() {
        return typeof this.firstMedia.metadatas.default.caption === 'object'
          ? this.firstMedia.metadatas.default.caption
          : {}
      },
      altValues() {
        return typeof this.firstMedia.metadatas.default.altText === 'object'
          ? this.firstMedia.metadatas.default.altText
          : {}
      },
      mediasIds: function() {
        return this.medias
          .map(function(media) {
            return media.id
          })
          .join(',')
      },
      mediasIdsToDelete: function() {
        return this.medias
          .filter(media => media.deleteUrl)
          .map(media => media.id)
      },
      mediasIdsToDeleteString: function() {
        return this.mediasIdsToDelete.join(',')
      },
      allowDelete: function() {
        return (
          this.medias.every(media => {
            return media.deleteUrl
          }) ||
          (this.hasMultipleMedias &&
            !this.medias.every(media => {
              return !media.deleteUrl
            }))
        )
      },
      warningDeleteMessage: function() {
        if (this.allowDelete) {
          if (this.hasMultipleMedias) {
            return this.$trans(
              'media-library.dialogs.delete.allow-delete-multiple-medias',
              "Some files are used and can't be deleted. Do you want to delete the others ?"
            )
          } else {
            return this.$trans(
              'media-library.dialogs.delete.allow-delete-one-media',
              "This file is used and can't be deleted. Do you want to delete the others ?"
            )
          }
        } else {
          if (this.hasMultipleMedias) {
            return this.$trans(
              'media-library.dialogs.delete.dont-allow-delete-multiple-medias',
              "This files are used and can't be deleted."
            )
          } else {
            return this.$trans(
              'media-library.dialogs.delete.dont-allow-delete-one-media',
              "This file is used and can't be deleted."
            )
          }
        }
      },
      containerClasses: function() {
        return {
          'dam-sidebar__inner--multi': this.hasMultipleMedias,
          'dam-sidebar__inner--single': this.hasSingleMedia
        }
      },
      singleAndMultipleMetadatas: function() {
        return this.extraMetadatas.filter(
          m => m.multiple && !this.translatableMetadatas.includes(m.name)
        )
      },
      singleOnlyMetadatas: function() {
        return this.extraMetadatas.filter(
          m =>
            !m.multiple ||
            (m.multiple && this.translatableMetadatas.includes(m.name))
        )
      },
      getSharedItems: function() {
        return function(fieldName, key = null) {
          const fieldValues = this.medias
            .map(media => media[fieldName] || [])
            .reduce((allItems, currentItems) => {
              if (Array.isArray(allItems) && Array.isArray(currentItems)) {
                return allItems.filter(item => {
                  if (typeof item === 'object') {
                    return currentItems.some(obj => isEqual(obj, item))
                  }
                  return currentItems.includes(item)
                })
              }
              return []
            })

          if (key) {
            return fieldValues.map(value => value[key])
          } else {
            return fieldValues
          }
        }
      },
      getSharedBrowserItems: function() {
        return function(fieldName) {
          return this.medias
            .map(media => media.browsers[fieldName] || [])
            .reduce((allItems, currentItems) => {
              if (Array.isArray(allItems) && Array.isArray(currentItems)) {
                return allItems.filter(item => {
                  if (typeof item === 'object') {
                    return currentItems.some(obj => isEqual(obj, item))
                  }
                  return currentItems.includes(item)
                })
              }
              return []
            })
        }
      },
      sharedKeywords: function() {
        return this.getSharedItems('keywords')
      },
      sharedSectors: function() {
        return this.getSharedItems('sectors')
      },
      sharedDiscipline: function() {
        return this.getSharedItems('discipline')
      },
      getOwnedListItems() {
        const itemClass = 'meta__'
        const allowedKeys = this.metaItems

        return Object.keys(this.firstMedia).reduce((items, key) => {
          if (allowedKeys.includes(key) && this.firstMedia[key]) {
            items.push(itemClass + key)
          }
          return items
        }, [])
      },
      getDownloadLink: function() {
        if (this.hasMultipleMedias) {
          return 'zip file url here'
        } else if (this.firstMedia) {
          return this.firstMedia.original
        }
        return null
      },
      hasPreview: function() {
        const extension = this.firstMedia.name
          .split('.')
          .pop()
          .toLowerCase()
        return (
          Extensions.img.extensions.includes(extension) ||
          Extensions.vid.extensions.includes(extension) ||
          extension === 'jpeg'
        )
      },
      isVideo: function() {
        const extension = this.firstMedia.name
          .split('.')
          .pop()
          .toLowerCase()
        return Extensions.vid.extensions.includes(extension)
      },
      ...mapState({
        mediasLoading: state => state.mediaLibrary.loading,
        useWysiwyg: state => state.mediaLibrary.config.useWysiwyg,
        wysiwygOptions: state => state.mediaLibrary.config.wysiwygOptions,
        tagEndpoints: state => state.mediaLibrary.tagEndpoints,
        currentBrowser: state => state.browser.connector,
        browserFields: state => state.mediaLibrary.browserFields,
        tagFields: state => state.mediaLibrary.tagFields
      })
    },
    methods: {
      replaceMedia: function() {
        // Open confirm dialog if any
        if (this.$root.$refs.replaceWarningMediaLibrary) {
          this.$root.$refs.replaceWarningMediaLibrary.open(() => {
            this.triggerMediaReplace()
          })
        } else {
          this.triggerMediaReplace()
        }
      },
      triggerMediaReplace: function() {
        this.$emit('triggerMediaReplace', {
          id: this.getMediaToReplaceId()
        })
      },
      handleKeyUp: function(direction){
        if (direction === 'next') {
          this.$emit('nextMedia')
        }else {
          this.$emit('previousMedia')
        }
      },
      deleteSelectedMediasValidation: function() {
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
      deleteSelectedMedias: function() {
        if (this.loading) return false
        this.loading = true

        if (this.hasMultipleMedias) {
          api.bulkDelete(
            this.firstMedia.deleteBulkUrl,
            { ids: this.mediasIdsToDeleteString },
            resp => {
              this.loading = false
              this.$emit('delete', this.mediasIdsToDelete)
              this.$refs.warningDelete.close()
            },
            error => {
              this.$store.commit(NOTIFICATION.SET_NOTIF, {
                message: error.data.message,
                variant: 'error'
              })
            }
          )
        } else {
          api.delete(
            this.firstMedia.deleteUrl,
            resp => {
              this.loading = false
              this.$emit('delete', this.mediasIdsToDelete)
              this.$refs.warningDelete.close()
            },
            error => {
              this.$store.commit(NOTIFICATION.SET_NOTIF, {
                message: error.data.message,
                variant: 'error'
              })
            }
          )
        }
      },
      clear: function() {
        this.$emit('clear')
        this.isOpen = false
      },
      getFormData: function(form) {
        return FormDataAsObj(form)
      },
      getMediaToReplaceId: function() {
        return this.firstMedia.id
      },
      removeFieldFromBulkEditing: function(name) {
        this.fieldsRemovedFromBulkEditing.push(name)
      },
      focus: function() {
        this.focused = true
      },
      blur: function() {
        this.focused = false
        this.save()

        const form = this.$refs.form
        const data = this.getFormData(form)

        if (this.hasSingleMedia) {
          if (data.hasOwnProperty('alt_text'))
            this.firstMedia.metadatas.default.altText = data.alt_text
          else this.firstMedia.metadatas.default.altText = ''

          if (data.hasOwnProperty('caption'))
            this.firstMedia.metadatas.default.caption = data.caption
          else this.firstMedia.metadatas.default.caption = ''

          this.extraMetadatas.forEach(metadata => {
            if (data.hasOwnProperty(metadata.name)) {
              this.firstMedia.metadatas.default[metadata.name] =
                data[metadata.name]
            } else {
              this.firstMedia.metadatas.default[metadata.name] = ''
            }
          })
        } else {
          this.singleAndMultipleMetadatas.forEach(metadata => {
            if (data.hasOwnProperty(metadata.name)) {
              this.medias.forEach(media => {
                media.metadatas.default[metadata.name] = data[metadata.name]
              })
            }
          })
        }
      },
      save: function() {
        this.$nextTick(() => {
          const form = this.$refs.form
          if (!form) return

          const formData = this.getFormData(form)

          if (!isEqual(formData, this.previousSavedData) && !this.loading) {
            this.previousSavedData = formData
            this.update(form)
          }
        })
      },
      submit: function(event) {
        event.preventDefault()
        this.save()
      },
      update: function(form) {
        if (this.loading) return

        this.loading = true

        const data = this.getFormData(form)
        data.fieldsRemovedFromBulkEditing = this.fieldsRemovedFromBulkEditing

        const url = this.hasMultipleMedias
          ? this.firstMedia.updateBulkUrl
          : this.firstMedia.updateUrl // single or multi updates

        api.update(
          url,
          data,
          resp => {
            this.loading = false
            this.editTagsOpen = false

            if (!this.hasMultipleMedias) {
              if (resp.data) this.$emit('tagFieldsUpdated', this.firstMedia, resp.data)
            } else if (this.hasMultipleMedias && resp.data.items) {
              this.$emit('bulkTagsUpdated', resp.data.items)
            }
          },
          error => {
            this.loading = false

            if (error.data.message) {
              this.$store.commit(NOTIFICATION.SET_NOTIF, {
                message: error.data.message,
                variant: 'error'
              })
            }
          }
        )
      },
      openBrowser: function (item) {
        const selected = {
          browsers: {
            [item.name]: this.getSharedBrowserItems(item.name)
          }
        }
        this.$store.commit(BROWSER.ADD_BROWSERS, selected)

        this.$store.commit(BROWSER.UPDATE_BROWSER_MAX, item.max || 100)
        this.$store.commit(BROWSER.UPDATE_BROWSER_CONNECTOR, item.name)
        this.$store.commit(BROWSER.DESTROY_BROWSER_ENDPOINTS)

        this.$store.commit(BROWSER.UPDATE_BROWSER_ENDPOINT, {
          value: item.endpoint,
          label: item.name
        })

        this.$store.commit(BROWSER.UPDATE_BROWSER_TITLE, item.browserTitle)

        this.$refs.damBrowser.open(true)
      },
      saveBrowser: function (items) {
        if (this.loading) return

        this.loading = true

        const data = {
          [this.currentBrowser]: items,
        }

        let url;

        if (this.hasMultipleMedias) {
          data.ids = this.mediasIds
          url = this.firstMedia.updateBulkBrowserUrl
        } else {
          data.id = this.firstMedia.id
          url = this.firstMedia.updateBrowserUrl
        }

        api.update(
          url,
          data,
          resp => {
            this.loading = false

            this.$refs.damBrowser.close()

            if (!this.hasMultipleMedias) {
              if (resp.data) this.$emit('browserUpdated', this.firstMedia, resp.data)
            } else if (this.hasMultipleMedias && resp.data.items) {
              this.$emit('bulkBrowsersUpdated', resp.data.items)
            }

          },
          error => {
            this.loading = false

            if (error.data.message) {
              this.$store.commit(NOTIFICATION.SET_NOTIF, {
                message: error.data.message,
                variant: 'error'
              })
            }
          }
        )
      }
    },
    mounted() {
      if (!this.lightbox) {
        this.lightbox = new PhotoSwipeLightbox({
          gallery: '.dam-sidebar__inner .pswp-lightbox',
          pswpModule: () => import('photoswipe'),
          paddingTop: 60,
          paddingBottom: 60,
          zoomSVG: '',
          closeSVG:
            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M3 3L17 17M17 3L3 17" stroke="white" stroke-width="1.5" stroke-miterlimit="10"/> </svg>'
        })

        // eslint-disable-next-line
        const videoPlugin = new PhotoSwipeVideoPlugin(this.lightbox, {});

        this.lightbox.init()
      }
    },
    unmounted() {
      if (this.lightbox) {
        this.lightbox.destroy()
        this.lightbox = null
      }
    }
  }
</script>

<style lang="scss">
  .dam-sidebar {
    .input,
    .input--small {
      margin-top: 0;
    }

    .vselect--multiple .vs__dropdown-toggle {
      min-height: rem-calc(36);
    }

    .vselect--multiple .vs__selected {
      @include font-small;
      height: rem-calc(28);
      margin: rem-calc(4) 0 0 rem-calc(5);
      line-height: normal;
    }

    .vselect--multiple .vs__selected .vs__deselect {
      top: rem-calc(14);
    }

    .vselect--multiple input[type='search'],
    .vselect--multiple input[type='search']:focus {
      height: rem-calc(28);
      margin-top: rem-calc(4);
      padding: 0 rem-calc(10);
    }
  }

  .pswp__button--zoom {
    display: none;
  }

  .pswp__button--close {
    width: rem-calc(36);
    height: rem-calc(36);
    margin: rem-calc(8) rem-calc(8) 0 0;

    @include breakpoint('medium+') {
      margin: rem-calc(20) rem-calc(20) 0 0;
    }
  }
</style>

<style lang="scss" scoped>
  .dam-sidebar {
    width: rem-calc(312);
    background: $color__light;
    height: 100%;
    margin-left: auto;
    display: none;
    flex-flow: column;
    flex-shrink: 0;

    @include breakpoint('medium+') {
      display: flex;
    }
  }

  .input--small {
    margin-top: 0;
  }

  .dam-sidebar__info {
    display: flex;
    justify-content: space-between;

    a {
      margin-left: rem-calc(16);
      color: $color__grey--54;
      text-decoration: none;

      &:focus,
      &:hover {
        text-decoration: underline;
      }
    }
  }

  .dam-sidebar__inner,
  .dam-sidebar .mediasidebar__inner {
    padding: rem-calc(20);
  }

  .dam-sidebar__inner--single {
    display: flex;
    flex-flow: column;
    align-items: flex-start;
  }

  .dam-sidebar__content {
    flex-grow: 1;
    overflow-y: auto;
  }

  .dam-sidebar__action {
    border-top: 1px solid $color__border;
    background: $color__light;
    display: flex;
    flex-flow: column;
    flex-shrink: 0;
    margin-top: auto;
  }

  .dam-sidebar__img {
    max-width: 100%;
    height: auto;
    display: block;
    margin-bottom: rem-calc(12);
  }

  .dam-sidebar__name {
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
  }

  .dam-sidebar__metadatas {
    color: $color__text--light;
    margin-top: rem-calc(4);

    & + .dam-sidebar__metadatas {
      margin-top: 0;
      max-height: 0;
      overflow: hidden;
      transition: all 300ms ease;

      &.dam-sidebar__metadatas--open {
        max-height: rem-calc(300);
      }
    }
  }

  .dam-sidebar__meta-toggle {
    padding: 0;
    margin-top: rem-calc(4);
    height: auto;
    line-height: normal;
    display: flex;
    align-items: center;

    .icon {
      background: none;
      margin-left: rem-calc(10);
    }

    &:hover .icon {
      background: none;
    }
  }

  .dam-sidebar__meta-toggle--expanded {
    .icon {
      transform: rotate(180deg);
    }
  }

  .dam-sidebar .dam-sidebar__buttonbar {
    display: inline-block;
    margin-top: rem-calc(20);

    &--hidden {
      display: none;
    }
  }

  .dam-sidebar__form {
    border-top: 1px solid $color__border;
    position: relative;
    display: flex;
    flex-flow: column;
    gap: rem-calc(20);

    button {
      margin-top: rem-calc(16);
    }

    &.dam-sidebar__form--loading {
      opacity: 0.5;
    }
  }

  .dam-sidebar__loader {
    position: absolute;
    top: 20px;
    right: 20px + 8px + 8px;
  }

  .dam-sidebar__checkbox {
    margin-top: rem-calc(16);
  }

  .dam-sidebar__langswitcher {
    margin-top: rem-calc(32);
    margin-bottom: rem-calc(32);
  }

  .dam-sidebar__editable {
    padding-bottom: rem-calc(16);
    border-bottom: 1px solid $color__border;

    ul {
      margin-top: rem-calc(8);
      display: flex;
      flex-direction: column;
      gap: rem-calc(8);
    }

    ul li {
      display: flex;
      flex-flow: row;
      justify-content: space-between;
    }

    ul li .button {
      margin: 0;
      padding: 0;
      height: auto;
      line-height: normal;
    }

    ul li button.button {
      color: $color__grey--54;
      margin-left: rem-calc(16);
      flex-shrink: 0;
    }
  }

  .dam-sidebar__editable-header {
    display: flex;
    flex-flow: row;
    justify-content: space-between;
    align-items: flex-start;

    h3 {
      font-weight: 600;
    }

    .button {
      margin: 0;
      padding: 0;
      height: auto;
      line-height: normal;
      margin-left: rem-calc(16);
      flex-shrink: 0;
    }
  }

  .dam-sidebar__tags {
    ul {
      margin-top: rem-calc(8);
      display: flex;
      flex-flow: row wrap;
      gap: rem-calc(8);
    }

    ul li {
      border-radius: 2px;
      display: inline-block;
      height: rem-calc(16);
      font-size: rem-calc(11);
      color: $color__background;
      text-transform: uppercase;
      background: $color__icons;
      padding: 0 rem-calc(5);
      position: relative;
      user-select: none;
      letter-spacing: 0;
      display: flex;
      align-items: center;
    }
  }

  .dam-sidebar__tags--edit {
    display: flex;
    flex-direction: column;
    gap: rem-calc(20);
  }
</style>
