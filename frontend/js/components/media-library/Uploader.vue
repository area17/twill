<template>
  <div class="uploader">
    <div class="uploader__dropzone"><a17-button type="button" variant="ghost" @click="downloadMedias">Add new</a17-button> or drop new files here</div>
  </div>
</template>

<script>
  export default {
    name: 'A17Uploader',
    props: {
      type: {
        type: String,
        required: true
      }
    },
    data: function () {
      return {
        loadingMedias: []
      }
    },
    methods: {
      loadingProgress: function (media) {
        this.$store.commit('progressUploadMedia', media)
      },
      loadingFinished: function (media) {
        // add the loaded image to the main image list (+ do ajax to save image)
        this.$emit('loaded', media)

        this.$store.commit('doneUploadMedia', media)
      },
      loadingError: function (media) {
        this.$store.commit('errorUploadMedia', media)
      },
      downloadMedias: function () {
        let self = this

        // clear selected medias
        this.$emit('clear')

        // Fake download here
        const randId = Math.round(Math.random() * 99999)
        let name
        let media
        switch (this.type) {
          case 'file':
            const _ext = ['pdf', 'ppt', 'xls', 'txt', 'zip', 'dmg', 'fsfsfs']
            const extension = _ext[Math.floor(_ext.length * Math.random())]
            name = 'file_' + randId + '.' + extension
            media = {
              id: randId,
              name: name,
              size: '2mb',
              extension: extension,
              progress: 0,
              error: false,
              interval: null, // demos : to track progress of the image fake loading
              metadatas: {
                default: {
                  caption: '',
                  video: '',
                  altText: name
                }
              }
            }
            break
          default:
            name = 'image_' + randId + '.jpg'
            media = {
              id: randId,
              name: name,
              src: 'https://source.unsplash.com/random/300x200?sig=' + randId,
              original: 'https://source.unsplash.com/random/300x200?sig=' + randId,
              size: '227kb',
              width: 1280,
              height: 800,
              progress: 0,
              error: false,
              interval: null, // demos : to track progress of the image fake loading
              metadatas: {
                default: {
                  caption: '',
                  video: '',
                  altText: name
                },
                custom: {
                  caption: null,
                  video: null,
                  altText: null
                }
              }
            }
        }

        this.loadingMedias.push(media)

        const index = self.loadingMedias.findIndex(function (m) {
          return m.id === randId
        })

        if (index >= 0) {
          this.loadingMedias[index].interval = setInterval(function () {
            const media = self.loadingMedias[index]

            media.progress = media.progress + Math.round(Math.random() * 15)

            if (media.progress < 100) self.loadingProgress(media) // update progress
            else {
              clearInterval(media.interval)
              media.interval = null
              self.loadingFinished(media)  // finish to load
            }

            // Simulate random error
            if (Math.round(Math.random() * 100) < 2) {
              media.progress = 0
              clearInterval(media.interval)
              media.interval = null
              self.loadingError(media) // error
            }
          }, 600)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .uploader {
    margin:10px;
  }

  .uploader__dropzone {
    border:1px dashed $color__border;
    text-align:center;
    padding:26px 0;
    color:$color__text--light;

    button {
      margin-right:10px;
    }
  }
</style>
