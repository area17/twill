<template>
  <div class="mediasidebar">
    <a17-mediasidebar-upload v-if="mediasLoading.length"/>
    <template v-else>
      <div class="mediasidebar__inner" :class="containerClasses">
        <p v-if="!hasMedia" class="f--note">No file selected</p>
        <p v-if="hasMultipleMedias" class="mediasidebar__info">{{ medias.length }} files selected <a href="#" @click.prevent="clear" >Clear</a></p>

        <template v-if="hasSingleMedia">
          <img :src="firstMedia.src" class="mediasidebar__img" :alt="firstMedia.original" />
          <p class="mediasidebar__name">{{ firstMedia.name }}</p>
          <ul class="mediasidebar__metadatas">
            <li class="f--small" v-if="firstMedia.size" >File size: {{ firstMedia.size | uppercase }}</li>
            <li class="f--small" v-if="firstMedia.width + firstMedia.height">Dimensions: {{ firstMedia.width }} &times; {{ firstMedia.height }}</li>
          </ul>
        </template>

        <a17-buttonbar class="mediasidebar__buttonbar" v-if="hasMedia">
          <!-- Actions -->
          <a v-if="hasSingleMedia" :href="firstMedia.original" download><span v-svg symbol="download"></span></a>
          <button v-if="allowDelete && authorized" type="button" @click="deleteSelectedMediasValidation"><span v-svg symbol="trash"></span></button>
          <button v-else="" type="button" class="button--disabled"><span v-svg symbol="trash"></span></button>
        </a17-buttonbar>
          <p v-if="!allowDelete">{{ warningDeleteMessage }}</p>
      </div>

      <form v-if="hasMedia" class="mediasidebar__inner mediasidebar__form" @submit="update" :class="formClasses">
        <template v-if="hasMultipleMedias">
          <input type="hidden" name="ids" :value="mediasIdsToDeleteString" />
        </template>
        <template v-else>
          <input type="hidden" name="id" :value="firstMedia.id" />
          <a17-textfield label="Alt text" name="alt-text" :initialValue="firstMedia.metadatas.default.altText" size="small"/>
          <a17-textfield label="Caption" name="caption" :initialValue="firstMedia.metadatas.default.caption" size="small"/>
        </template>
        <a17-vselect label="Tags" name="tags" :multiple="true" :selected="hasMultipleMedias ? sharedTags : firstMedia.tags" :searchable="true" emptyText="Sorry, no tags found." :taggable="true" :pushTags="true" size="small" :endpoint="tagsEndpoint"/>
        <a17-button v-if="authorized" type="submit" variant="ghost" :disabled="loading">Update</a17-button>
      </form>
    </template>

    <a17-modal class="modal--tiny modal--form modal--withintro" ref="warningDelete" title="Warning Delete">
      <p class="modal--tiny-title"><strong>Are you sure ?</strong></p>
      <p>{{ warningDeleteMessage }}</p>
      <a17-inputframe>
        <a17-button variant="validate" @click="deleteSelectedMedias">Delete ( {{ mediasIdsToDelete.length }})</a17-button>
        <a17-button variant="aslink" @click="$refs.warningDelete.close()"><span>Cancel</span></a17-button>
      </a17-inputframe>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import FormDataAsObj from '@/utils/formDataAsObj.js'
  import api from '../../store/api/media-library'

  import a17MediaSidebarUpload from '@/components/media-library/MediaSidebarUpload'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17MediaSidebar',
    components: {
      'a17-mediasidebar-upload': a17MediaSidebarUpload
    },
    props: {
      medias: {
        default: function () { return [] }
      },
      authorized: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        loading: false
      }
    },
    filters: a17VueFilters,
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
      sharedTags: function () {
        return this.medias.map((media) => {
          return media.tags
        }).reduce((allTags, currentTags) => allTags.filter(tag => currentTags.includes(tag)))
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
        }) || (this.hasMultipleMedias &&
        !this.medias.every((media) => {
          return !media.deleteUrl
        }))
      },
      warningDeleteMessage: function () {
        let prefix = this.hasMultipleMedias ? this.allowDelete ? 'Some files are' : 'This files are' : 'This file is'
        return this.allowDelete ? prefix + ' used and can\'t be deleted. Do you want to delete the others ?' : prefix + ' used and can\'t be deleted.'
      },
      containerClasses: function () {
        return {
          'mediasidebar__inner--multi': this.hasMultipleMedias,
          'mediasidebar__inner--single': this.hasSingleMedia
        }
      },
      formClasses: function () {
        return {
          'mediasidebar__form--loading': this.loading
        }
      },
      ...mapState({
        mediasLoading: state => state.mediaLibrary.loading,
        tagsEndpoint: state => state.mediaLibrary.tagsEndpoint
      })
    },
    methods: {
      deleteSelectedMediasValidation: function () {
        if (this.loading) return false

        if (this.mediasIdsToDelete.length !== this.medias.length) {
          this.$refs.warningDelete.open()
          return
        }

        this.deleteSelectedMedias()
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
            this.$store.commit('setNotification', {
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
            this.$store.commit('setNotification', {
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
      update: function (event) {
        event.preventDefault()

        if (this.loading) return false

        let data = this.getFormData(event.target)

        this.loading = true

        // single or multi updates
        const url = this.hasMultipleMedias ? this.firstMedia.updateBulkUrl : this.firstMedia.updateUrl

        api.update(url, data, (resp) => {
          this.loading = false

          if (!this.hasMedia) return false

          // save caption and alt text on the media
          if (data['alt-text']) this.firstMedia.metadatas.default.altText = data['alt-text']
          if (data['caption']) this.firstMedia.metadatas.default.caption = data['caption']

          // save new tags on the medias
          if (data['tags']) {
            const newTags = data['tags'].split(',')
            this.medias.forEach(function (media) {
              newTags.forEach(function (tag) {
                if (!media.tags.includes(tag)) media.tags.push(tag)
              })
            })
          }
        }, (error) => {
          this.$store.commit('setNotification', {
            message: error.data.message,
            variant: 'error'
          })
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .mediasidebar {
    a {
      color:$color__link;
      text-decoration:none;

      &:hover {
        text-decoration:underline;
      }
    }
  }

  .mediasidebar__info {
    margin-bottom:30px;

    a {
      margin-left:15px;
    }
  }

  .mediasidebar__inner {
    padding:20px;
    // overflow: hidden;
  }

  .mediasidebar__img {
    max-width:135px;
    max-height:135px;
    height:auto;
    display:block;
    margin-bottom:17px;
  }

  .mediasidebar__name {
    margin-bottom:6px;
  }

  .mediasidebar__metadatas {
    color:$color__text--light;
    margin-bottom:16px;
  }

  .mediasidebar .mediasidebar__buttonbar {
    display:inline-block;
  }

  .mediasidebar__form {
    border-top:1px solid $color__border;

    button {
      margin-top:16px;
    }

    &.mediasidebar__form--loading {
      opacity:0.5;
    }
  }
</style>
