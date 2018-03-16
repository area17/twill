<template>
  <transition name="move_down_notif">
    <div v-if="show" :class="notifClasses" role="alert" aria-live="polite" aria-atomic="true">
      <div class="notif__inner">
        <button v-if="!important" type="button" class="notif__close" data-dismiss="alert" aria-label="alertClose" @click.stop.prevent="closeNotif" >
          <span v-svg symbol="close_modal"></span>
        </button>
        <span v-html="message"></span>
      </div>
    </div>
  </transition>
</template>

<script>
  import { NOTIFICATION } from '@/store/mutations'

  export default {
    name: 'A17Notification',
    props: {
      variant: {
        type: String,
        default: 'success'
      },
      duration: {
        type: Number,
        default: 3000
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
        css: 'notif'
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
      closeNotif: function () {
        this.closed = true
        this.clearNotification()

        if (this.timer) {
          clearTimeout(this.timer)
          this.timer = null
        }
      },
      clearNotification: function () {
        this.$store.commit(NOTIFICATION.CLEAR_NOTIF, this.variant)
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
          // if we have a message, let's show it
          this.closed = false

          if (this.autoHide) this.autoClose()
        }
      }
    }
  }
</script>
