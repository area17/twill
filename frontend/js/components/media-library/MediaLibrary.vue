<template>
  <div class="medialibrary">
    <div class="medialibrary__frame">
      <div class="medialibrary__header" ref="form">
        <a17-filter @submit="submitFilter">
          <ul class="secondarynav" slot="navigation">
            <li class="secondarynav__item" v-for="navType in types" :class="{ 's--on': type === navType.value, 's--disabled' : type !== navType.value && strict }">
              <a href="#" @click.prevent="updateType(navType.value)"><span class="secondarynav__link">{{ navType.text }}</span> <span class="secondarynav__number">({{ navType.total }})</span></a>
            </li>
          </ul>

          <div slot="hidden-filters">
            <input type="hidden" name="type" :value="type" />
            Additional filters goes here
          </div>
        </a17-filter>
      </div>

      <div class="medialibrary__inner">
        <div class="medialibrary__grid">
          <aside class="medialibrary__sidebar">
            <a17-mediasidebar :selectedMedias="selectedMedias" @clear="clearSelectedMedias"></a17-mediasidebar>
          </aside>
          <footer class="medialibrary__footer" v-if="selectedMedias.length && showInsert">
            <a17-button variant="action" @click="saveAndClose">{{ selectedMedias.length > 1 ? btnMultiLabel : btnLabel }}</a17-button>
          </footer>
          <div class="medialibrary__list" ref="list">
            <a17-uploader @loaded="addMedia" @clear="clearSelectedMedias"></a17-uploader>
            <a17-medialist :items="fullMedias" :selectedItems="selectedMedias" @change="updateSelectedMedias" v-if="type === 'file'"></a17-medialist>
            <a17-mediagrid :medias="fullMedias" :selectedMedias="selectedMedias" @change="updateSelectedMedias" v-else></a17-mediagrid>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import api from '../../store/api/media-library'

  import a17MediaSidebar from './MediaSidebar.vue'
  import a17Filter from '../Filter.vue'
  import a17Uploader from './Uploader.vue'
  import a17MediaGrid from './MediaGrid.vue'
  import a17MediaList from './MediaList.vue'
  import FormDataAsObj from '@/utils/formDataAsObj.js'

  export default {
    name: 'A17Medialibrary',
    components: {
      'a17-filter': a17Filter,
      'a17-mediasidebar': a17MediaSidebar,
      'a17-uploader': a17Uploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-medialist': a17MediaList
    },
    props: {
      btnLabel: {
        type: String,
        default: 'Insert file'
      },
      btnMultiLabel: {
        type: String,
        default: 'Insert files'
      },
      showInsert: {
        type: Boolean,
        default: true
      },
      endpoint: {
        type: String,
        default: ''
      },
      initialPage: {
        type: Number,
        default: 1
      }
    },
    data: function () {
      return {
        fullMedias: [],
        selectedMedias: [],
        page: this.initialPage
      }
    },
    computed: {
      ...mapState({
        max: state => state.mediaLibrary.max,
        type: state => state.mediaLibrary.type, // image, video, audio or pdf
        types: state => state.mediaLibrary.types,
        strict: state => state.mediaLibrary.strict
      })
    },
    methods: {
      updateType: function (newType) {
        if (this.strict) return
        if (this.type === newType) return

        this.$store.commit('updateMediaType', newType)
        this.submitFilter()
      },
      addMedia: function (media) {
        let self = this

        // this.selectedMedias = []

        // remove unecessary loading states
        if (media.hasOwnProperty('progress')) delete media.progress
        if (media.hasOwnProperty('error')) delete media.error
        if (media.hasOwnProperty('interval')) delete media.interval

        // add media in first position of the available media
        // see api/media-library for actual ajax
        api.add(media, function (resp) {
          self.fullMedias.unshift(resp)

          // select it
          self.updateSelectedMedias(media.id)
        })
      },
      updateSelectedMedias: function (id) {
        const alreadySelectedMedia = this.selectedMedias.filter(function (media) {
          return media.id === id
        })

        // not already selected
        if (alreadySelectedMedia.length === 0) {
          if (this.max === 1) this.clearSelectedMedias()
          if (this.selectedMedias.length >= this.max && this.max > 0) return

          const mediaToSelect = this.fullMedias.filter(function (media) {
            return media.id === id
          })

          // Add one media to the selected media
          if (mediaToSelect.length) this.selectedMedias.push(mediaToSelect[0])
        } else {
          // Remove one item from the selected media
          this.selectedMedias = this.selectedMedias.filter(function (media) {
            return media.id !== id
          })
        }
      },
      getFormData: function (form) {
        let data = FormDataAsObj(form)

        if (data) data.page = this.page

        return data
      },
      clearSelectedMedias: function () {
        this.selectedMedias.splice(0)
      },
      clearFullMedias: function () {
        this.selectedMedias.splice(0)
        this.fullMedias.splice(0)
      },
      reloadGrid: function () {
        let self = this

        const form = this.$refs.form
        const list = this.$refs.list
        const formdata = this.getFormData(form)

        // see api/media-library for actual ajax
        api.get(this.endpoint, formdata, function (resp) {
          // TEMP : randomize ID, ratios, name and SRC for demo purpose
          resp.data.forEach(function (media) {
            const ratio = (Math.round(Math.random() * 10) > 5) ? '300x200' : '200x300'
            media.id = Math.round(Math.random() * 999999)
            media.name = 'image_' + media.id + '.jpg'
            media.src = 'https://source.unsplash.com/random/' + ratio + '?sig=' + media.id
            media.original = media.src
            media.metadatas.default.altText = media.name
          })

          // add medias here
          self.fullMedias.push(...resp.data)

          // re-listen for scroll position
          self.$nextTick(function () {
            list.addEventListener('scroll', () => self.scrollToPaginate())
          })
        })
      },
      submitFilter: function (formData) {
        // when changing filters, reset the page to 1
        this.page = 1

        this.clearFullMedias()
        this.clearSelectedMedias()
        this.reloadGrid()
      },

      scrollToPaginate: function () {
        const list = this.$refs.list
        const maxPage = 20

        if (list.scrollTop + list.offsetHeight > list.scrollHeight - 50) {
          list.removeEventListener('scroll', () => self.scrollToPaginate())

          if (maxPage > this.page) {
            this.page = this.page + 1

            this.reloadGrid()
          }
        }
      },
      saveAndClose: function () {
        this.$store.commit('saveSelectedMedias', this.selectedMedias)

        this.$parent.close()
      }
    },
    mounted: function () {
      // bind scroll on the feed
      this.reloadGrid()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $width_sidebar:290px;

  .medialibrary {
    display: block;
    width: 100%;
    height: 100%;
    padding: 0;
    position:relative;
  }

  .medialibrary__header {
    background:$color__border--light;
    border-bottom:1px solid $color__border;
    padding:0 20px;
  }

  .medialibrary__frame {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display:flex;
    flex-flow: column nowrap;
  }

  .medialibrary__inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    flex-grow: 1;
  }

  .medialibrary__footer {
    position: absolute;
    right: 0;
    z-index: 76;
    bottom: 0;
    width: $width_sidebar; // fixed arbitrary width
    color: $color__text--light;
    padding:10px;
    overflow:hidden;
    background:$color__border--light;
    border-top:1px solid $color__border;

    > button {
      display: block;
      width: 100%;
    }
  }

  .medialibrary__sidebar {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: $width_sidebar; // fixed arbitrary width
    padding: 0 0 90px 0;
    z-index: 75;
    background:$color__border--light;
    overflow: auto;
  }

  .medialibrary__list {
    margin: 0;
    position: absolute;
    top: 0;
    left: 0;
    right:0;
    bottom: 0;
    overflow: auto;
    padding:10px;
  }

  /* with a sidebar visible */
  .medialibrary__list {
    right:$width_sidebar;
  }

</style>
