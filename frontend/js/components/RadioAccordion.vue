<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value">{{ currentLabel }}</div>
    <a17-radiogroup :name="name" :radios="radios" @change="changeValue" :initialValue="currentValue"></a17-radiogroup>
  </a17-accordion>
</template>

<script>
  import a17Accordion from './Accordion.vue'
  import VisibilityMixin from '@/mixins/toggleVisibility'

  export default {
    name: 'A17Radioaccordion',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    props: {
      value: {
        default: ''
      },
      title: {
        default: ''
      },
      name: {
        default: ''
      },
      radios: {
        default: function () { return [] }
      }
    },
    data: function () {
      return {
        currentValue: this.value
      }
    },
    computed: {
      currentLabel: function () {
        const selectRadios = this.radios.filter(this.isSameValue)
        if (selectRadios.length) return selectRadios[0].label
        else return ''
      }
    },
    methods: {
      isSameValue: function (radio) {
        return radio.value === this.currentValue
      },
      changeValue: function (newValue) {
        this.currentValue = newValue
        this.$emit('change', newValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      }
    }
  }
</script>
