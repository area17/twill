<template>
  <transition name="move_down_notif">
    <div v-if="show"
    :class="notifClasses"
    role="alert"
    aria-live="polite"
    aria-atomic="true"
    >
      <button v-if="!important"
        type="button"
        class="close"
        data-dismiss="alert"
        aria-label="alertClose"
        @click.stop.prevent="closeNotif"
        >
        <span aria-hidden="true">&times</span>
      </button>
    {{ message }}
    </div>
  </transition>
</template>

<script>
  // storage = window && window.sessionStorage

  export default {
    name: 'A17Notification',
    props: {
      important: {
        type: Boolean,
        default: true
      },
      autoHide: {
        type: Boolean,
        default: true
      }
    },
    data: function () {
      return {
        message: null,
        closed: true,
        timer: null,
        duration: 3000,
        css: 'notif',
        keep: true,
        storage: null,
        key: '__vuexNotif',
        variant: ''
      }
    },
    computed: {
      variantClass: function () {
        return `notif--${this.variant}`
      },
      notifClasses: function () {
        return this.css && Array.isArray(this.css)
          ? [ ...this.css, this.variantClass ]
          : [ 'notif', this.variantClass ]
      },
      show: function () {
        return !this.closed && !!this.message
      }
    },
    methods: {
      clearPersisted: function () {
        this.storage.removeItem(this.key)
      },
      getNotif: function () {
        return this.$store.getters['getNotifMessage'](this.variant)
      },
      closeNotif: function () {
        this.closed = true
        this.clearNotification()

        if (this.timer) {
          clearTimeout(this.timer)
          this.timer = null
        }
      },
      // check if all notification messages have been notified when use persist data and keep = false
      // usefull when there are multiple notifications and have to clear persist storage every time
      notified: function () {
        return this.$store.getters['notified']
      },
      clearNotification: function () {
        this.$store.commit('clearNotification', this.variant)

        if (!this.keep && this.notified()) {
          this.clearPersisted()
        }
      },
      autoClose: function () {
        if (this.timer === null) {
          this.timer = setTimeout(() => {
            this.closeNotif()
          }, this.duration)
        }
      },
      open: function (variant) {
        console.log('OPEN notification')

        this.message = ''
        this.variant = variant

        let notifMessage = this.getNotif()

        if (this.closed && notifMessage) {
          this.closed = false
          this.message = notifMessage

          if (this.autoHide) {
            this.autoClose()
          }
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

</style>
