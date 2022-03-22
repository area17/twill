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
    captionTextarea: {
      type: Boolean,
      default: false
    },
    altTextMaxLength: {
      type: Number,
      default: 0
    },
    captionMaxLength: {
      type: Number,
      default: 0
    },
    note: {
      type: String,
      default: ''
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
