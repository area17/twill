<template>
  <a17-modal ref="modal" class="modal--form" :title="modalTitle" :forceClose="true">
    <form :action="formCreate" @submit.prevent="submit">
      <slot></slot>
      <a17-modal-validation :is-disable="true"></a17-modal-validation>
    </form>
  </a17-modal>
</template>

<script>
  import retrySubmitMixin from '@/mixins/retrySubmit'
  import ACTIONS from '@/store/actions'
  import { FORM,NOTIFICATION } from '@/store/mutations'

  import a17ModalValidationButtons from './ModalValidationButtons.vue'

  export default {
    name: 'A17ModalAdd',
    mixins: [retrySubmitMixin],
    props: {
      name: {
        type: String,
        default: ''
      },
      modalTitle: {
        type: String,
        default: 'Add new'
      },
      formCreate: {
        type: String,
        default: '#'
      }
    },
    components: {
      'a17-modal-validation': a17ModalValidationButtons
    },
    methods: {
      open: function () {
        if (this.$refs.modal) this.$refs.modal.open()
      },
      submit: function () {
        if (this.isSubmitPrevented) {
          this.shouldRetrySubmitWhenAllowed = true
          return
        }

        if (this._isSubmitting) return
        this._isSubmitting = true

        const self = this

        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)

        const submitMode = document.activeElement.name

        this.$nextTick(function () {
          this.$store.dispatch(ACTIONS.CREATE_FORM_IN_MODAL, {
            name: this.name,
            endpoint: this.formCreate,
            method: 'post'
          }).then(() => {
            if (self.$refs.modal) self.$refs.modal.close()

            self.$nextTick(function () {
              self.$store.commit(NOTIFICATION.SET_NOTIF, {
                message: 'Your content has been added',
                variant: 'success'
              })

              if (submitMode === 'create-another' && self.$refs.modal) self.$refs.modal.open()
            })
          }, (errorResponse) => {
            self.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: 'Your content can not be added, please retry',
              variant: 'error'
            })
          })
        })
      }
    }
  }
</script>
