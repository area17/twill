<template>
  <a17-modal ref="modal" class="modal--form" :title="modalTitle" :forceClose="true">
    <form :action="actionForm" @submit.prevent="submit">
      <slot></slot>
      <a17-modal-validation
        :mode="mode"
        :is-disable="createMode"
        :active-publish-state="withPublicationToggle"
        :is-publish="published"
        published-name="published"
        :textEnabled="publishedLabel"
        :textDisabled="draftLabel"
        :create-label="createLabel"
        :create-another-label="createAnotherLabel"
        :updateLabel="updateLabel"
      >
      </a17-modal-validation>
    </form>
  </a17-modal>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { NOTIFICATION, FORM, DATATABLE, LANGUAGE } from '@/store/mutations'
  import ACTIONS from '@/store/actions'
  import a17ModalValidationButtons from './ModalValidationButtons.vue'

  export default {
    name: 'A17ModalCreate',
    props: {
      formCreate: {
        type: String,
        default: '#'
      },
      publishedLabel: {
        type: String,
        default: 'Live'
      },
      draftLabel: {
        type: String,
        default: 'Draft'
      },
      createModalTitle: {
        type: String,
        default: 'Add new'
      },
      updateModalTitle: {
        type: String,
        default: 'Update'
      },
      createLabel: {
        type: String,
        default: 'Create'
      },
      createAnotherLabel: {
        type: String,
        default: 'Create and add another'
      },
      updateLabel: {
        type: String,
        default: 'Update'
      }
    },
    components: {
      'a17-modal-validation': a17ModalValidationButtons
    },
    computed: {
      createMode: function () {
        return this.mode === 'create'
      },
      actionForm: function () {
        return this.createMode ? this.formCreate : this.action
      },
      modalTitle: function () {
        return this.createMode ? this.createModalTitle : this.updateModalTitle
      },
      published: function () {
        return !this.createMode && !!this.fieldValueByName('published')
      },
      withPublicationToggle: function () {
        return this.columns.find(c => {
          return c.name === 'published'
        }) !== undefined
      },
      ...mapState({
        action: state => state.modalEdition.action,
        mode: state => state.modalEdition.mode,
        columns: state => state.datatable.columns
      }),
      ...mapGetters([
        'fieldValueByName'
      ])
    },
    methods: {
      open: function () {
        if (this.createMode) {
          this.$store.commit(LANGUAGE.RESET_LANGUAGES)
        }

        this.$refs.modal.open()
      },
      submit: function (event) {
        const self = this

        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)
        const submitMode = document.activeElement.name

        this.$nextTick(function () {
          this.$store.dispatch(ACTIONS.UPDATE_FORM_IN_LISTING, {
            endpoint: this.actionForm,
            method: this.mode === 'create' ? 'post' : 'put',
            redirect: submitMode !== 'create-another'
          }).then(() => {
            if (self.$refs.modal) self.$refs.modal.close()

            self.$nextTick(function () {
              if (submitMode === 'create-another' && self.$refs.modal) self.$refs.modal.open()
              if (this.mode === 'create') this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
              this.$emit('reload')
            })
          }, (errorResponse) => {
            self.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: 'Your submission could not be validated, please fix and retry',
              variant: 'error'
            })
          })
        })
      }
    }
  }
</script>
