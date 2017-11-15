import a17Notification from '@/components/Notification.vue'

const A17Notif = {
  install (Vue, opts) {
    Vue.mixin({
      methods: {
        notif: function (notifObj) {
          console.log(notifObj)
          this.$store.commit('setNotification', notifObj)
        }
      }
    })

    Vue.component('a17-notif', a17Notification)
  }
}

export default A17Notif
