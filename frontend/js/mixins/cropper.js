export default {
  props: {
    // Cropper options @see: https://github.com/fengyuanchen/cropperjs/blob/master/README.md#options
    aspectRatio: {
      type: Number,
      default: null
    },
    viewMode: {
      type: Number,
      default: 2 // restrict the minimum canvas size to fit within the container.
    },
    cropBoxMovable: {
      type: Boolean,
      default: true
    },
    cropBoxResizable: {
      type: Boolean,
      default: true
    },
    dragMode: {
      type: String, // 'crop', 'move', 'none'
      default: 'crop'
    },
    rotatable: {
      type: Boolean,
      default: false
    },
    scalable: {
      type: Boolean,
      default: false
    },
    zoomable: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    defaultCropsOpts: function () {
      return {
        aspectRatio: this.initAspectRatio,
        viewMode: this.viewMode,
        cropBoxResizable: this.cropBoxResizable,
        cropBoxMovable: this.cropBoxMovable,
        dragMode: this.dragMode,
        rotatable: this.rotatable,
        scalable: this.scalable,
        zoomable: this.zoomable,
        guides: false,
        center: false,
        checkCrossOrigin: false, // https://github.com/fengyuanchen/cropper#checkcrossorigin
        background: false
      }
    }
  }
}
