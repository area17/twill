<template>
  <div class="mediasidebar__inner mediasidebar__inner--single">
    <p class="f--note">Uploading {{ mediasLoading.length }} file{{ mediasLoading.length > 1 ? 's' : '' }}</p>

    <div class="mediasidebar__progress"><span class="mediasidebar__progressBar" :style="loadingProgress"></span></div>

    <div class="mediasidebar__loading">
      <p class="f--small" v-for="media in mediasLoading" :key="media.id" :class="{ 's--error' : media.error }">
        <span class="mediasidebar__errorMessage" v-if="media.error">{{media.errorMessage}}</span>
        <span>{{ media.name }}</span> <a href="#" v-if="media.error" @click.prevent="cancelUpload(media)">Cancel</a>
      </p>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { MEDIA_LIBRARY } from '@/store/mutations'

  export default {
    name: 'A17MediaSidebarUpload',
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
      loadingProgress: function () {
        const progress = -100 + this.uploadProgress
        return {
          'transform': 'translateX(' + progress + '%)'
        }
      },
      ...mapState({
        mediasLoading: state => state.mediaLibrary.loading,
        uploadProgress: state => state.mediaLibrary.uploadProgress
      })
    },
    methods: {
      cancelUpload: function (media) {
        this.$store.commit(MEDIA_LIBRARY.DONE_UPLOAD_MEDIA, media)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .mediasidebar__progress {
    height: 6px;
    background: $color__border--focus;
    border-radius: 3px;
    position: relative;
    margin-top:20px;
    overflow: hidden;
  }

  .mediasidebar__progressBar {
    position: absolute;
    display:block;
    top:0;
    left:0;
    width: 100%;
    border-radius: 3px;
    height:6px;
    background: $color__action;
    transform: translateX(-100%);
    transition: transform 250ms;
  }

  .mediasidebar__loading {
    margin-top:25px;

    p {
      margin-top:5px;
      display:flex;
      flex-flow: row wrap;

      span {
        flex-grow:1;
      }
    }

    a {
      color:$color__link;
      text-decoration:none;

      &:hover {
        text-decoration:underline;
      }
    }
  }

  .mediasidebar__errorMessage {
    display: block;
    width: 100%;
    color: $color__black;
    margin-top: 5px;
    margin-bottom: 5px;
  }

  .s--error {
    color:$color__error;
  }
</style>
