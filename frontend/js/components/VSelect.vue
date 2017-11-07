<template>
  <a17-inputframe :error="error" :label="label" :note="note" :size="size">
    <div class="vselect" :class="vselectClasses">
      <div class="vselect__field">
        <input type="hidden" :name="name" :value="uniqValue" v-if="hasUniqValue" />
        <v-select
          :multiple="multiple"
          :placeholder="placeholder"
          :value="value"
          :options="currentOptions"
          :searchable="searchable"
          :clearSearchOnSelect="clearSearchOnSelect"
          :label="optionsLabel"
          :on-search="getOptions"
          :taggable="taggable"
          :pushTags="pushTags"
          :transition="transition"
          :requiredValue="required"
          @input="updateValue"
        >
          <span slot="no-options">{{ emptyText }}</span>
        </v-select>
      </div>
    </div>
  </a17-inputframe>
</template>

<script>
  import debounce from 'lodash/debounce'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import extendedVSelect from '@/components/VSelect/ExtendedVSelect.vue' // check full options of the vueSelect here : http://sagalbot.github.io/vue-select/
//  import vSelect from 'vue-select' // check full options of the vueSelect here : http://sagalbot.github.io/vue-select/

  export default {
    name: 'A17VSelect',
    mixins: [InputframeMixin, FormStoreMixin],
    props: {
      placeholder: {
        type: String,
        default: ''
      },
      disabled: {
        type: Boolean,
        default: false
      },
      name: {
        type: String,
        default: ''
      },
      transition: {
        type: String,
        default: 'fade_move_dropdown'
      },
      multiple: {
        type: Boolean,
        default: false
      },
      taggable: { // Enable/disable creating options from searchInput.
        type: Boolean,
        default: false
      },
      pushTags: { // When true, newly created tags will be added to the options list.
        type: Boolean,
        default: false
      },
      searchable: {
        type: Boolean,
        default: false
      },
      clearSearchOnSelect: {
        type: Boolean,
        default: false
      },
      selected: {
        default: null
      },
      emptyText: {
        default: 'Sorry, no matching options.'
      },
      options: {
        default: function () { return [] }
      },
      optionsLabel: { // label in vueselect
        type: String,
        default: 'label'
      },
      endpoint: {
        type: String,
        default: ''
      },
      size: {
        type: String,
        default: ''
      },
      required: {
        type: Boolean,
        default: false
      }
    },
    components: {
      'v-select': extendedVSelect
    },
    data: function () {
      return {
        value: this.selected,
        currentOptions: this.options,
        ajaxUrl: this.endpoint
      }
    },
    computed: {
      uniqValue: function () {
        if (this.value) {
          return this.value.value
        } else {
          return ''
        }
      },
      hasUniqValue: function () {
        return !this.multiple && typeof this.value === 'object'
      },
      vselectClasses: function () {
        return [
          this.multiple ? 'vselect--multiple' : 'vselect--single',
          this.size === 'small' ? 'vselect--small' : '',
          this.size === 'large' ? 'vselect--large' : '',
          this.error ? 'vselect--error' : ''
        ]
      }
    },
    methods: {
      isAjax: function () {
        return this.ajaxUrl !== ''
      },
      updateValue: function (value) {
        // see formStore mixin
        this.value = value
        this.saveIntoStore(value)

        this.$emit('change', value)
      },
      getOptions: debounce(function (search, loading) {
        let self = this

        if (!this.isAjax) return true

        loading(true)
        this.$http.get(this.ajaxUrl, {params: {q: search}}).then(function (resp) {
          if (resp.data.items && resp.data.items.length) self.currentOptions = resp.data.items

          loading(false)
        }, function (resp) {
            // error callback

          loading(false)
        })
      }, 500)
    }
  }
</script>
