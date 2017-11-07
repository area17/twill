<template>
  <div class="mediasidebar__inner mediasidebar__inner--single">
    <p class="f--note">Uploading {{ mediasLoading.length }} file(s)</p>

    <div class="mediasidebar__progress"><span class="mediasidebar__progressBar" :style="loadingProgress"></span></div>

    <div class="mediasidebar__loading">
      <p class="f--small" v-for="(media, index) in mediasLoading" :key="media.id" :class="{ 's--error' : media.error }"><span>{{ media.name }}</span> <a href="#" v-if="media.error" @click.prevent="cancelUpload(media)">Cancel</a></p>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

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
        const total = this.mediasLoading.length * 100
        const allProgress = this.mediasLoading.map(media => media.progress)
        const sum = allProgress.reduce((a, b) => a + b, 0)

        return {
          'width': ((sum / total) * 100) + '%'
        }
      },
      ...mapState({
        mediasLoading: state => state.mediaLibrary.loading
      })
    },
    methods: {
      cancelUpload: function (media) {
        this.$store.commit('doneUploadMedia', media)
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
  }

  .mediasidebar__loading {
    margin-top:25px;

    p {
      margin-top:5px;
      display:flex;
      flex-flow: row nowrap;

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

  .s--error {
    color:$color__error;
  }
</style>
