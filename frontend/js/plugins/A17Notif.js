import a17Notification from '@/components/Notification.vue'
import { NOTIFICATION } from '@/store/mutations'

const A17Notif = {
  install (Vue, opts) {
    Vue.mixin({
      methods: {
        notif: function (notifObj) {
          this.$store.commit(NOTIFICATION.SET_NOTIF, notifObj)
        }
      }
    })

    Vue.component('a17-notif', a17Notification)
  }
}

export default A17Notif
