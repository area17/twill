import { MEDIA_LIBRARY } from '@/store/mutations'

export default {
  props: {
    type: {
      type: String,
      default: 'image'
    }
  },
  methods: {
    openMediaLibrary: function (max = 1, name = this.name, index = -1) {
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_CONNECTOR, name)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_TYPE, this.type)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_REPLACE_INDEX, index)
      this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_MAX, max)
      if (this.$root.$refs.mediaLibrary) this.$root.$refs.mediaLibrary.open()
    }
  }
}
