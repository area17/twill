<template>
  <div class="vselectOuter">
    <a17-inputframe :error="error" :label="label" :note="note" :size="size" :name="name" :label-for="uniqId"
                    :required="required" :add-new="addNew">
      <div class="vselect" :class="vselectClasses">
        <div class="vselect__field">
          <input type="hidden" :name="name" :id="uniqId" :value="inputValue"/>
          <v-select
              :multiple="multiple"
              :placeholder="placeholder"
              :value="value"
              :options="currentOptions"
              :searchable="searchable"
              :selectable="selectable"
              :clearSearchOnSelect="clearSearchOnSelect"
              :label="optionsLabel"
              :taggable="taggable"
              :pushTags="pushTags"
              :transition="transition"
              :requiredValue="required"
              :maxHeight="maxHeight"
              :disabled="disabled"
              @input="updateValue"
              @search="getOptions"
          >
            <span slot="no-options">{{ emptyText }}</span>
          </v-select>
        </div>
      </div>
    </a17-inputframe>
    <template v-if="addNew">
      <a17-modal-add ref="addModal" :name="name" :form-create="addNew" :modal-title="'Add new ' + label">
        <slot name="addModal"></slot>
      </a17-modal-add>
    </template>
  </div>
</template>

<script>
  import debounce from 'lodash/debounce'

  import extendedVSelect from '@/components/VSelect/ExtendedVSelect.vue'
  import AttributesMixin from '@/mixins/addAttributes'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import randKeyMixin from '@/mixins/randKey'
  // check full options of the vueSelect here : http://sagalbot.github.io/vue-select/
  // import vSelect from 'vue-select' // check full options of the vueSelect here : http://sagalbot.github.io/vue-select/
  export default {
    name: 'A17VueSelect',
    mixins: [randKeyMixin, InputframeMixin, FormStoreMixin, AttributesMixin],
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
      selectable: {
        type: Function,
        default: option => option.selectable ?? true,
      },
      clearSearchOnSelect: {
        type: Boolean,
        default: true
      },
      selected: {
        default: null
      },
      emptyText: {
        default () {
          return this.$trans('select.empty-text', 'Sorry, no matching options.')
        }
      },
      options: {
        default: function () {
          return []
        }
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
        default: '' // 'small', 'large'
      },
      required: {
        type: Boolean,
        default: false
      },
      maxHeight: { // max-height of the dropdown menu
        type: String,
        default: '400px'
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
    watch: {
      options: function (options) {
        this.currentOptions = this.options
      }
    },
    computed: {
      uniqId: function (value) {
        return this.name + '-' + this.randKey
      },
      inputValue: {
        get: function () {
          if (this.value) {
            if (!this.multiple) { // single selects
              if (typeof this.value === 'object') {
                return this.value.value
              }
            } else { // multiple selects
              if (Array.isArray(this.value)) {
                if (typeof this.value[0] === 'object') {
                  return this.value.map(e => e.value)
                }
                return this.value.join(',')
              }
            }
            return this.value
          } else {
            return ''
          }
        },
        set: function (value) {
          if (Array.isArray(value)) {
            if (this.taggable) {
              this.value = value
            } else {
              this.value = this.options.filter(o => value.includes(o.value))
            }
          } else {
            this.value = this.options.find(o => {
              // Try to always compare to the same type. But we only check for a numeric value. Because it can only be
              // a string or a number (int or float) for now.
              if (typeof o.value === 'number') {
                if (o.value % 1 !== 0) {
                  return o.value === parseFloat(value)
                }
                return o.value === parseInt(value)
              }
              return o.value === String(value)
            })
          }
        }
      },
      vselectClasses: function () {
        return [
          this.value ? 'vselect--has-value' : '',
          this.multiple ? 'vselect--multiple' : 'vselect--single',
          this.size === 'small' ? 'vselect--small' : '',
          this.size === 'large' ? 'vselect--large' : '',
          this.error ? 'vselect--error' : ''
        ]
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        this.inputValue = newValue
      },
      isAjax: function () {
        return this.ajaxUrl !== ''
      },
      updateValue: function (value) {
        // see formStore mixin
        this.value = value
        this.saveIntoStore()
        this.$emit('change', value)
      },
      getOptions: debounce(function (search, loading) {
        if (!this.isAjax()) return true
        loading(true)
        this.$http.get(this.ajaxUrl, { params: { q: search } }).then((resp) => {
          if (resp.data.items && resp.data.items.length) {
            if (this.taggable) {
              if (Array.isArray(this.value)) {
                this.currentOptions = resp.data.items.filter(i => !this.value.includes(i))
              } else {
                this.currentOptions = resp.data.items
              }
            } else {
              this.currentOptions = resp.data.items
            }
          }
          loading(false)
        }, function (resp) {
          // error callback
          loading(false)
        })
      }, 500)
    }
  }
</script>
