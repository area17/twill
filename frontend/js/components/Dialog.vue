<template>
  <a17-modal class="modal--tiny modal--form modal--withintro" ref="modal" :title="modalTitle" :forceClose="true">
    <slot></slot>
    <a17-inputframe>
        <a17-button variant="validate" class="dialog-confirm">{{ confirmLabel }}</a17-button> <a17-button variant="aslink" class="dialog-cancel"><span>{{ cancelLabel }}</span></a17-button>
    </a17-inputframe>
  </a17-modal>
</template>

<script>
  export default {
    name: 'A17Dialog',
    props: {
      name: {
        type: String,
        default: ''
      },
      modalTitle: {
        type: String,
        default: 'Move to Trash'
      },
      confirmLabel: {
        type: String,
        default: 'Ok'
      },
      cancelLabel: {
        type: String,
        default: 'Cancel'
      }
    },
    methods: {
      open: function (callback) {
        if (this.$refs.modal) this.$refs.modal.open()

        this.$nextTick(() => {
          this.$el.querySelector('.dialog-confirm').addEventListener('click', (e) => {
            callback()
            this.close()
          })

          this.$el.querySelector('.dialog-cancel').addEventListener('click', (e) => {
            this.close()
          })
        })
      },
      close: function () {
        if (this.$refs.modal) this.$refs.modal.close()
      }
    }
  }
</script>
