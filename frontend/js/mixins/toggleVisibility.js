export default {
  props: {
    open: {
      type: Boolean,
      default: false
    }
  },
  data: function () {
    return {
      visible: this.open
    }
  },
  computed: {
    visibilityClasses: function () {
      return { 's--open': this.visible }
    }
  },
  methods: {
    onClickVisibility: function () {
      this.visible = !this.visible

      this.$emit('toggleVisibility', this.visible)
    }
  }
}
