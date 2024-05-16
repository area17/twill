<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <template v-slot:accordion__title>
      <span><slot></slot></span>
    </template>
    <template v-slot:accordion__value>
      <div>{{ currentLabel }}</div>
    </template>
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
    emits: ['open', 'selectionChanged'],
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
      },
      selectedLabel: {
        type: String,
        default: ''
      },
      updateLang: {
        type: Boolean,
        default: true
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
        return (
          (!this.currentValue ? '0' : this.currentValue.length) +
          ' ' +
          (this.selectedLabel
            ? this.selectedLabel
            : this.$trans('publisher.languages-published'))
        )
      }
    },
    methods: {
      changeValue: function (newValue) {
        this.currentValue = newValue

        if (this.updateLang) {
          this.$store.commit(LANGUAGE.PUBLISH_LANG, newValue)
        }

        this.$emit('selectionChanged', this.currentValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      }
    }
  }
</script>
