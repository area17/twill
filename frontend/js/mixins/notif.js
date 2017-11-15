export default {
  methods: {
    showNotification: function (variant = 'success', message = '') {
      this.$store.commit('setNotification', { message: message, variant: variant })

      console.log(this.$root.$refs.notification)

      if (this.$root.$refs.notification) this.$root.$refs.notification.open(variant)
    }
  }
}
