<template>
  <a17-modal ref="modal" class="modal--form" :title="modalTitle" :forceClose="true">
    <form :action="actionForm" @submit.prevent="submit">
      <slot></slot>
      <a17-modal-validation
        :mode="mode"
        ref="validation"
        :is-disable="createMode"
        :active-publish-state="withPublicationToggle"
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
  import { mapGetters,mapState } from 'vuex'

  import retrySubmitMixin from '@/mixins/retrySubmit'
  import ACTIONS from '@/store/actions'
  import { DATATABLE, FORM, LANGUAGE,NOTIFICATION } from '@/store/mutations'

  import a17ModalValidationButtons from './ModalValidationButtons.vue'

  export default {
    name: 'A17ModalCreate',
    mixins: [retrySubmitMixin],
    props: {
      formCreate: {
        type: String,
        default: '#'
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
        return this.createMode ? this.formCreate : this.action
      },
      modalTitle: function () {
        return this.createMode ? this.$trans('modal.create.title', 'Add new') : this.$trans('modal.update.title', 'Update')
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
        columns: state => state.datatable.columns,
        language: state => state.language.active
      }),
      ...mapGetters([
        'fieldValueByName'
      ])
    },
    watch: {
      language () {
        if (this.$refs.validation) {
          this.$refs.validation.addListeners()
        }
      }
    },
    methods: {
      open: function () {
        if (this.createMode) {
          this.$store.commit(LANGUAGE.RESET_LANGUAGES)
        }

        this.$refs.modal.open()
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
          this.$store.dispatch(ACTIONS.UPDATE_FORM_IN_LISTING, {
            endpoint: this.actionForm,
            method: this.mode === 'create' ? 'post' : 'put',
            redirect: submitMode !== 'create-another'
          }).then(() => {
            if (self.$refs.modal) self.$refs.modal.close()

            self.$nextTick(function () {
              if (submitMode === 'create-another' && self.$refs.modal) self.$refs.modal.open()
              if (this.mode === 'create') this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
              this.$store.commit(FORM.REMOVE_FORM_FIELD, 'published')
              this.$emit('reload')
            })
          }, (errorResponse) => {
            self.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: 'Your submission could not be validated, please fix and retry',
              variant: 'error'
            })
          }).finally(() => {
            self.$nextTick(function () {
              self._isSubmitting = false
            })
          })
        })
      }
    }
  }
</script>
