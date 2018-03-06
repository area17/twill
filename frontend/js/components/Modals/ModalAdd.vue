<template>
  <a17-modal ref="modal" class="modal--form" title="Add new" :forceClose="true">
    <form :action="formCreate" @submit.prevent="submit">
      <slot></slot>
      <a17-modal-validation :is-disable="true"></a17-modal-validation>
    </form>
  </a17-modal>
</template>

<script>
  import { NOTIFICATION, FORM } from '@/store/mutations'
  import * as ACTIONS from '@/store/actions'
  import a17ModalValidationButtons from '@/components/Modals/ModalValidationButtons.vue'

  export default {
    name: 'A17ModalAdd',
    props: {
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
        console.log('OPEN')
        if (this.$refs.modal) this.$refs.modal.open()
      },
      submit: function (event) {
        let self = this

        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)

        this.$nextTick(function () {
          this.$store.dispatch(ACTIONS.UPDATE_FORM_IN_LISTING, {
            endpoint: this.formCreate,
            method: 'post'
          }).then(() => {
            self.$nextTick(function () {
              console.log('Refresh attributes')
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
