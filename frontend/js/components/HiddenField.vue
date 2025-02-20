<template>
  <div class="form__input form__input--hidden">
    <input type="hidden" :name="name" :id="uniqId" :value="value" />
  </div>
</template>

<script>
  import FormStoreMixin from '@/mixins/formStore'
  import InputMixin from '@/mixins/input'
  import randKeyMixin from '@/mixins/randKey'

  export default {
    name: 'A17HiddenField',
    mixins: [randKeyMixin, InputMixin, FormStoreMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      initialValue: {
        default: ''
      }
    },
    computed: {
      uniqId: function () {
        return this.name + '-' + this.randKey
      }
    },
    data: function () {
      return {
        value: this.initialValue
      }
    },
    watch: {
      initialValue: function () {
        this.value = this.initialValue
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (typeof newValue === 'undefined') newValue = ''

        if (this.value !== newValue) {
          this.value = newValue
        }
      }
    }
  }
</script>
