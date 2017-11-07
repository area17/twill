<template>
  <div class="mediasidebar">
    <a17-mediasidebar-upload v-if="mediasLoading.length"></a17-mediasidebar-upload>
    <template v-else>
      <template v-if="selectedMedias.length > 1">
        <div class="mediasidebar__inner mediasidebar__inner--single" v-if="selectedMedias.length > 1">
          <p class="mediasidebar__info">{{ selectedMedias.length }} files selected <a href="#" @click.prevent="clear" >Clear</a></p>

          <!--Actions-->
          <a17-buttonbar class="mediasidebar__buttonbar">
            <a href="#" download><span v-svg symbol="download"></span></a>
            <button type="button" @click="deleteSelectedMedias"><span v-svg symbol="trash"></span></button>
          </a17-buttonbar>
        </div>
        <form class="mediasidebar__inner mediasidebar__form" @submit="bulkUpdate" :class="formClasses">
          <input type="hidden" name="ids" :value="selectMediasIds" />
          <a17-vselect label="Tags" name="tags" :multiple="true" :searchable="true" emptyText="Sorry, no tags found." :taggable="true" :pushTags="true"  size="small"></a17-vselect>
          <a17-button type="submit" variant="ghost" :disabled="updateInProgress">Update</a17-button>
        </form>
      </template>
      <template v-else-if="selectedMedias.length === 1">
        <div class="mediasidebar__inner mediasidebar__inner--multi">
          <img :src="selectedMedias[0].src" class="mediasidebar__img" :alt="selectedMedias[0].original" />

          <p class="mediasidebar__name">{{ selectedMedias[0].name }}</p>

          <ul class="mediasidebar__metadatas">
            <li class="f--small">File size : {{ selectedMedias[0].size }}</li>
            <li class="f--small">Dimensions : {{ selectedMedias[0].width }} x {{ selectedMedias[0].height }}</li>
          </ul>

          <!--Actions-->
          <a17-buttonbar class="mediasidebar__buttonbar">
            <a :href="selectedMedias[0].original" download><span v-svg symbol="download"></span></a>
            <button type="button" @click="deleteSelectedMedias"><span v-svg symbol="trash"></span></button>
          </a17-buttonbar>
        </div>
        <form class="mediasidebar__inner mediasidebar__form" @submit="singleUpdate" :class="formClasses">
          <input type="hidden" name="id" :value="selectedMedias[0].id" />
          <a17-textfield label="Alt text" name="alt-text" :initialValue="selectedMedias[0].metadatas.default.altText" @change="updateAltText" size="small"></a17-textfield>
          <a17-textfield label="Caption" name="caption" :initialValue="selectedMedias[0].metadatas.default.caption" @change="updateCaption" size="small"></a17-textfield>
          <a17-vselect label="Tags" name="tags" :multiple="true" :searchable="true" :taggable="true" :pushTags="true" size="small"></a17-vselect>
          <a17-button type="submit" variant="ghost" :disabled="updateInProgress">Update</a17-button>
        </form>
      </template>
      <div class="mediasidebar__inner" v-else>
        <p class="f--note">No file selected</p>
      </div>
    </template>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import FormDataAsObj from '@/utils/formDataAsObj.js'

  import a17MediaSidebarUpload from '@/components/media-library/MediaSidebarUpload'

  export default {
    name: 'A17MediaSidebar',
    components: {
      'a17-mediasidebar-upload': a17MediaSidebarUpload
    },
    props: {
      selectedMedias: {
        default: function () { return [] }
      }
    },
    data: function () {
      return {
        updateInProgress: false
      }
    },
    computed: {
      selectMediasIds: function () {
        return this.selectedMedias.map(function (media) { return media.id }).join(',')
      },
      formClasses: function () {
        return {
          'mediasidebar__form--loading': this.updateInProgress
        }
      },
      ...mapState({
        mediasLoading: state => state.mediaLibrary.loading
      })
    },
    methods: {
      deleteSelectedMedias: function () {
        console.log('Deleted files')
      },
      clear: function () {
        this.$emit('clear')
      },
      getFormData: function (form) {
        return FormDataAsObj(form)
      },
      bulkUpdate: function (event) {
        event.preventDefault()

        let self = this
        let data = this.getFormData(event.target)

        this.updateInProgress = true

        console.log('Bulk Update Ajax goes here')
        console.log(data)

        setTimeout(function () {
          self.updateInProgress = false
        }, 500)
      },
      singleUpdate: function (event) {
        event.preventDefault()

        let self = this
        let data = this.getFormData(event.target)

        this.updateInProgress = true

        console.log('Single Update Ajax goes here')
        console.log(data)

        setTimeout(function () {
          self.updateInProgress = false
        }, 500)
      },
      updateAltText: function (val) {
        this.selectedMedias[0].metadatas.default.altText = val
      },
      updateCaption: function (val) {
        this.selectedMedias[0].metadatas.default.caption = val
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../scss/setup/variables.scss";
  @import "../../../scss/setup/colors.scss";
  @import "../../../scss/setup/mixins.scss";

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
    overflow: hidden;
  }

  .mediasidebar__inner button {
    margin-top:16px;
  }

  .mediasidebar__img {
    max-width:135px;
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

  .mediasidebar__buttonbar {
    display:inline-block;
  }

  .mediasidebar__form {
    border-top:1px solid $color__border;

    &.mediasidebar__form--loading {
      opacity:0.5;
    }
  }
</style>
