import { MEDIA_LIBRARY } from '@/store/mutations'

export default {
  props: {
    type: {
      type: String,
      default: 'image'
    },
    allowFile: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    openMediaLibrary: function (max = 1, name = this.name, index = -1) {
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_CONNECTOR, name)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_TYPE, this.type)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_REPLACE_INDEX, index)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_MAX, max)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_MODE, !this.allowFile)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_FILESIZE_MAX, this.filesizeMax || 0)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_WIDTH_MIN, this.widthMin || 0)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_HEIGHT_MIN, this.heightMin || 0)
      if (this.$root.$refs.mediaLibrary) this.$root.$refs.mediaLibrary.open()
    }
  }
}
