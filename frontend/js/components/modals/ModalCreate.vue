<template>
  <a17-modal ref="modal" class="modal--form" :title="modalTitle" :forceClose="true" @close="resetLang">
    <form :action="actionForm" @submit.prevent="submit">
      <slot></slot>
      <a17-modal-validation
        :mode="mode"
        :is-disable="createMode"
        :fields-in-modal="fieldsInModal"
        :active-publish-state="withPublicationToggle || showPublication"
        :is-publish="published"
        published-name="published"
        :textEnabled="publishedLabel"
        :textDisabled="draftLabel"
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

  const CREATE_ANOTHER = 'create-another'

  export default {
    name: 'A17ModalCreate',
    props: {
      formCreate: {
        type: String,
        default: '#'
      },
      fieldsInModal: {
        type: Boolean,
        default: false
      },
      closeOnCreate: {
        type: Boolean,
        default: false
      },
      languages: {
        type: Array,
        default: () => []
      },
      showPublication: {
        type: Boolean,
        default: false
      },
      publishedLabel: {
        type: String,
        default () {
          return this.$trans('main.published', 'Live')
        }
      },
      draftLabel: {
        type: String,
        default () {
          return this.$trans('main.draft', 'Draft')
        }
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
        return this.createMode && this.formCreate ? this.formCreate : this.action
      },
      modalTitle: function () {
        return this.createMode ? this.$trans('modal.create.title', 'Add new') : this.$trans('modal.update.title', 'Update')
      },
      published: function () {
        return !this.createMode && !!this.fieldValueByName('published')
      },
      withPublicationToggle: function () {
        return this.columns && this.columns.find(c => {
          return c.name === 'published'
        }) !== undefined
      },
      ...mapState({
        action: state => state.modalEdition.action,
        mode: state => state.modalEdition.mode,
        columns: state => state.datatable && state.datatable.columns
      }),
      ...mapGetters([
        'fieldValueByName'
      ])
    },
    methods: {
      resetLang: function () {
        const submitMode = document.activeElement.name
        if (submitMode !== CREATE_ANOTHER) this.$store.commit(LANGUAGE.RESET_LANGUAGES)
      },
      open: function () {
        if (this.createMode) {
          this.resetLang()
        }

        if (this.languages.length) {
          this.$store.commit(LANGUAGE.REPLACE_LANGUAGES, this.languages)
        }

        this.$refs.modal.open()
      },
      submit: function (event) {
        const self = this

        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)
        const submitMode = document.activeElement.name
        const action = this.fieldsInModal ? ACTIONS.CREATE_FORM_IN_MODAL : ACTIONS.UPDATE_FORM_IN_LISTING

        this.$nextTick(function () {
          this.$store.dispatch(action, {
            endpoint: this.actionForm,
            method: this.mode === 'create' ? 'post' : 'put',
            redirect: submitMode !== CREATE_ANOTHER && !this.closeOnCreate
          }).then(() => {
            if (self.$refs.modal) self.$refs.modal.close()
            this.$store.commit(FORM.REMOVE_MODAL_FIELD, 'published')

            self.$nextTick(function () {
              if (submitMode === CREATE_ANOTHER && self.$refs.modal) {
                self.$refs.modal.open()
              } else {
                this.resetLang()
              }
              if (this.mode === 'create' && !this.fieldsInModal) this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
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
