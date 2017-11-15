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
      variant: {
        type: String,
        default: 'success'
      },
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
        closed: false,
        timer: null,
        duration: 3000,
        css: 'notif',
        keep: true,
        storage: null,
        key: '__vuexNotif'
      }
    },
    computed: {
      message: function () {
        return this.$store.getters['notifByVariant'](this.variant)
      },
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
      // getNotif: function () {
      //   return this.$store.getters['getNotifMessage'](this.variant)
      // },
      closeNotif: function () {
        this.closed = true
        this.clearNotification()

        if (this.timer) {
          clearTimeout(this.timer)
          this.timer = null
        }
      },
      clearNotification: function () {
        this.$store.commit('clearNotification', this.variant)
      },
      autoClose: function () {
        if (this.timer === null) {
          this.timer = setTimeout(() => {
            this.closeNotif()
          }, this.duration)
        }
      }
    },
    watch: {
      message: function () {
        if (this.message) {
          this.closed = false

          if (this.autoHide) {
            this.autoClose()
          }
        }
      }
    },
    mounted: function () {
      console.log('notification')
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

</style>
