import { mapState } from 'vuex'

export default {
  data: function () {
    return {
      shouldRetrySubmitWhenAllowed: false
    }
  },
  computed: {
    ...mapState({
      isSubmitPrevented: state => state.form.isSubmitPrevented
    })
  },
  watch: {
    isSubmitPrevented: function (isSubmitPrevented) {
      if (!isSubmitPrevented && this.shouldRetrySubmitWhenAllowed) {
        this.shouldRetrySubmitWhenAllowed = false
        this.retrySubmit()
      }
    }
  },
  methods: {
    retrySubmit: function () {
      if (this.submitForm) {
        this.submitForm() // @see main-form.js
      } else if (this.submit) {
        this.submit() // @see ModalCreate.vue & ModalAdd.vue
      }
    }
  }
}
