<template>
  <a17-modal ref="modal" class="modal--form" :title="modalTitle" :forceClose="true">
    <form :action="actionForm" method="post">
      <slot></slot>
      <a17-modal-validation :mode="mode" :is-disable="isDisabled" :active-publish-state="false" :is-publish="false" published-name="published"></a17-modal-validation>
    </form>
  </a17-modal>
</template>

<script>
  import { mapState } from 'vuex'
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
