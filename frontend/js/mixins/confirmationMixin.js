export default {
  props: {
    requireConfirmation: {
      type: Boolean,
      default: false
    },
    confirmMessageText: {
      type: String,
      default: 'Are you sure you want to change this option ?'
    },
    confirmTitleText: {
      type: String,
      default: 'Confirm selection'
    }
  }
}
