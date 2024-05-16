<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <template v-slot:accordion__title>
      <span><slot></slot></span>
    </template>
    <template v-slot:accordion__value>
      <div>{{ currentLabel }}</div>
    </template>
    <a17-radiogroup :name="name" :radios="radios" @change="changeValue" :initialValue="currentValue"></a17-radiogroup>
  </a17-accordion>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'

  import a17Accordion from './Accordion.vue'

  export default {
    name: 'A17Radioaccordion',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    emits: ['change', 'open'],
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
