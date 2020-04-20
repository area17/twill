export default {
  props: {
    withAddInfo: {
      type: Boolean,
      default: true
    },
    withVideoUrl: {
      type: Boolean,
      default: true
    },
    withCaption: {
      type: Boolean,
      default: true
    },
    altTextMaxLength: {
      type: Number,
      default: 0
    },
    captionMaxLength: {
      type: Number,
      default: 0
    },
    // current crop context put in store. eg: slideshow, cover...
    cropContext: {
      type: String,
      default: ''
    },
    extraMetadatas: {
      type: Array,
      default () {
        return []
      }
    }
  }
}
