<template>
  <a17-modal ref="modal" class="modal--form" :title="modalTitle" :forceClose="true">
    <form :action="actionForm" method="post" @submit.prevent="submit">
      <slot></slot>
      <a17-modal-validation :mode="mode" :is-disable="isDisabled" :active-publish-state="false" :is-publish="false" published-name="published"></a17-modal-validation>
    </form>
  </a17-modal>
</template>

<script>
  import { mapState } from 'vuex'
  import { NOTIFICATION, FORM, DATATABLE } from '@/store/mutations'
  import a17ModalValidationButtons from '@/components/Modals/ModalValidationButtons.vue'

  export default {
    name: 'A17ModalCreate',
    props: {
      formCreate: {
        type: String,
        default: '#'
      }
    },
    components: {
      'a17-modal-validation': a17ModalValidationButtons
    },
    data: function () {
      return {
      }
    },
    computed: {
      isDisabled: function () {
        return this.mode === 'create'
      },
      actionForm: function () {
        return this.mode === 'create' ? this.formCreate : this.action
      },
      modalTitle: function () {
        return this.mode === 'create' ? 'Add new' : 'Update'
      },
      ...mapState({
        action: state => state.modalEdition.action,
        mode: state => state.modalEdition.mode
      })
    },
    methods: {
      open: function () {
        this.$refs.modal.open()
      },
      submit: function (event) {
        let self = this

        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)
        const submitMode = document.activeElement.name

        this.$nextTick(function () {
          this.$store.dispatch('updateFormInListing', {endpoint: this.actionForm, redirect: submitMode !== 'create-another'}).then(() => {
            self.$nextTick(function () {
              if (submitMode === 'create-another') {
                this.$store.commit(FORM.EMPTY_FORM_FIELDS, true)
              } else {
                if (self.$refs.modal) self.$refs.modal.close()
              }

              if (this.mode === 'create') this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
              this.$emit('reload')
            })
          }, (errorResponse) => {
            self.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: 'Your content can not be edited, please retry',
              variant: 'error'
            })
          })
        })
      }
    },
    mounted: function () {
    },
    beforeDestroy: function () {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';
</style>
