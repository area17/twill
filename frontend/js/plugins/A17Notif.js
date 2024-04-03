import a17Notification from '@/components/Notification.vue'
import { NOTIFICATION } from '@/store/mutations'

const A17Notif = {
  install (app, opts) {
    app.mixin({
      methods: {
        notif: function (notifObj) {
          this.$store.commit(NOTIFICATION.SET_NOTIF, notifObj)
        }
      }
    })

    app.component('a17-notif', a17Notification)
  }
}

export default A17Notif
