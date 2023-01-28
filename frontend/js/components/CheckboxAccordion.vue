<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value">{{ currentLabel }}</div>
    <a17-checkboxgroup :name="name" :options="options" @change="changeValue" :selected="currentValue" :min="1"></a17-checkboxgroup>
  </a17-accordion>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'
  import { LANGUAGE } from '@/store/mutations'

  import a17Accordion from './Accordion.vue'

  export default {
    name: 'A17Checkboxaccordion',
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
        currentValue: this.value
      }
    },
    watch: {
      value: function (newValue) {
        this.currentValue = newValue
      }
    },
    computed: {
      currentLabel: function () {
        return this.currentValue.length + ' ' + this.$trans('publisher.languages-published')
      }
    },
    methods: {
      changeValue: function (newValue) {
        this.currentValue = newValue
        this.$store.commit(LANGUAGE.PUBLISH_LANG, newValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      }
    }
  }
</script>
