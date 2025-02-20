<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value">{{ currentLabel }}</div>
    <a17-checkboxgroup :name="name" :options="currentOptions" @change="changeValue" :selected="currentValue"></a17-checkboxgroup>
  </a17-accordion>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'
  import { PUBLICATION } from '@/store/mutations'

  import a17Accordion from './Accordion.vue'

  export default {
    name: 'A17Reviewaccordion',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    props: {
      value: {
        default: function () { return [] }
      },
      title: {
        type: String,
        default: ''
      },
      name: {
        type: String,
        default: ''
      },
      options: {
        default: function () { return [] }
      }
    },
    data: function () {
      return {
        currentOptions: this.options,
        currentValue: this.value
      }
    },
    computed: {
      currentLabel: function () {
        let label = 'Pending approval'
        const currentStep = this.currentValue[this.currentValue.length - 1]

        if (this.currentValue.length) {
          this.options.forEach(function (option) {
            if (option.value === currentStep) {
              label = option.display
            }
          })
        }

        return label
      }
    },
    methods: {
      changeValue: function (newValue) {
        this.currentValue = newValue
        this.$store.commit(PUBLICATION.UPDATE_REVIEW_PROCESS, newValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      }
    }
  }
</script>
