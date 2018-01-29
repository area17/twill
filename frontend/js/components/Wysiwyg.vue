<template>
  <a17-inputframe :error="error" :note="note" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name" :required="required">
    <div class="wysiwyg__outer" :class="textfieldClasses">
      <input ref="input" :name="name" :id="name" :disabled="disabled" :required="required" :readonly="readonly" type="hidden" value="" />
      <template v-if="editSource">
        <div class="wysiwyg" :class="textfieldClasses" v-show="!activeSource">
          <div class="wysiwyg__editor" ref="editor"></div>
        </div>
        <div class="form__field form__field--textarea" v-show="activeSource">
          <textarea :placeholder="placeholder" :autofocus="autofocus" v-model="value" :style="textareaHeight"></textarea>
        </div>
        <a17-button variant="ghost" @click="toggleSourcecode" class="wysiwyg__button">Source code</a17-button>
      </template>
      <template v-else>
        <div class="wysiwyg" :class="textfieldClasses">
          <div class="wysiwyg__editor" ref="editor"></div>
        </div>
      </template>
    </div>
  </a17-inputframe>
</template>

<script>
  import { mapState } from 'vuex'

  import 'quill/dist/quill.snow.css'
  import 'quill/dist/quill.bubble.css'
  import 'quill/dist/quill.core.css'

  import QuillConfiguration from '@/libs/Quill/QuillConfiguration'

  import debounce from 'lodash/debounce'

  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17Wysiwyg',
    mixins: [InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
    props: {
      editSource: {
        type: Boolean,
        default: false
      },
      type: {
        type: String,
        default: 'text'
      },
      prefix: {
        type: String,
        default: ''
      },
      maxlength: {
        type: Number,
        default: 0
      },
      initialValue: {
        default: ''
      },
      options: {
        type: Object,
        required: false,
        default: function () {
          return {}
        }
      }
    },
    computed: {
      textareaHeight: function () {
        return {
          height: this.editorHeight
        }
      },
      textfieldClasses: function () {
        return {
          's--disabled': this.disabled,
          's--focus': this.focused
        }
      },
      ...mapState({
        baseUrl: state => state.form.baseUrl
      })
    },
    data: function () {
      return {
        value: this.initialValue,
        editorHeight: 50,
        toolbarHeight: 52,
        focused: false,
        activeSource: false,
        defaultModules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'link']
          ],
          clipboard: {
            matchVisual: false,
            matchers: [
              QuillConfiguration.lineBreak.clipboard
            ]
          },
          keyboard: {
            bindings: {
              lineBreak: QuillConfiguration.lineBreak.handle
            }
          }
          // Complete Toolbar example :
          // ['blockquote', 'code-block', 'strike'],
          // [{ 'header': 1 }, { 'header': 2 }],
          // [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          // [{ 'script': 'sub'}, { 'script': 'super' }],
          // [{ 'indent': '-1'}, { 'indent': '+1' }],
          // [{ 'direction': 'rtl' }],
          // [{ 'size': ['small', false, 'large', 'huge'] }],
          // [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          // [{ 'color': [] }, { 'background': [] }],
          // [{ 'font': [] }],
          // [{ 'align': [] }],
          // ['clean'],
          // ['link', 'image', 'video']
        }
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (typeof newValue === 'undefined') newValue = ''

        this.value = newValue
        this.$refs.input.value = this.value
        this.quill.pasteHTML(newValue)
      },
      updateInput: function () {
        this.$refs.input.value = this.value

        // see formStore mixin
        this.saveIntoStore()
      },
      textUpdate: debounce(function () {
        this.updateInput()
      }, 500),
      toggleSourcecode: function () {
        this.editorHeight = (Math.max(50, this.$refs.editor.clientHeight) + this.toolbarHeight - 1) + 'px'
        this.activeSource = !this.activeSource

        // set editor content
        this.quill.pasteHTML(this.value)
        this.updateInput()
      }
    },
    // watch: {
    //   submitting: function () {
    //     if (this.submitting) { // The form is about to submit so lets make sure we saved the wysiwyg
    //       this.updateInput()
    //     }
    //   }
    // },
    mounted: function () {
      const self = this

      self.options.theme = self.options.theme || 'snow'
      self.options.boundary = self.options.boundary || document.body
      self.options.modules = self.options.modules || self.defaultModules
      self.options.modules.toolbar = self.options.modules.toolbar !== undefined ? self.options.modules.toolbar : self.defaultModules.toolbar
      self.options.modules.clipboard = self.options.modules.clipboard !== undefined ? self.options.modules.clipboard : self.defaultModules.clipboard
      self.options.modules.keyboard = self.options.modules.keyboard !== undefined ? self.options.modules.keyboard : self.defaultModules.keyboard
      self.options.placeholder = self.options.placeholder || self.placeholder
      self.options.readOnly = self.options.readOnly !== undefined ? self.options.readOnly : self.readonly
      self.options.formats = QuillConfiguration.getFormats(self.options.modules.toolbar) // Formats are based on current toolbar configuration

      // init Quill
      this.quill = new QuillConfiguration.Quill(self.$refs.editor, self.options)

      // set editor content
      if (self.value) {
        self.quill.pasteHTML(self.value)
        self.updateInput()
      }

      // update model if text changes
      self.quill.on('text-change', (delta, oldDelta, source) => {
        if (self.maxlength > 0 && self.quill.getLength() > self.maxlength + 1) {
          self.quill.deleteText(self.maxlength, self.quill.getLength())
        } else {
          let html = self.$refs.editor.children[0].innerHTML
          let text = self.quill.getText()
          if (html === '<p><br></p>') html = ''
          self.value = html

          self.$emit('input', self.value)
          self.$emit('change', {
            editor: self.quill,
            html: html,
            text: text
          })
        }

        self.textUpdate()
      })

      // focus / blur event
      self.quill.on('selection-change', function (range, oldRange, source) {
        if (range) {
          self.focused = true
          self.$emit('focus')
        } else {
          self.focused = false

          // save value in store or in input
          self.updateInput()

          self.$emit('blur')
        }
      })

      // disabled
      if (this.disabled) {
        this.quill.enable(false)
      }

      // Change the link placeholder + add a checkbox to add or not the target blank attribute to current url
      if (this.baseUrl) {
        const tooltip = this.quill.theme.tooltip
        const rootElement = tooltip.root

        if (rootElement) {
          const input = rootElement.querySelector('input[data-link]')
          if (input) input.setAttribute('data-link', this.baseUrl)
        }
      }

      // emit ready
      self.$emit('ready', self.quill)
    },
    beforeDestroy () {
      this.quill = null
    }
  }
</script>

<style lang="scss" scoped>
  .wysiwyg__button {
    margin-top:20px;
  }
</style>
<style lang="scss">
  /* Not scoped style here because we want to overwrite default style of the wysiwig */
  @import '~styles/setup/_mixins-colors-vars.scss';

  .a17 {
    .ql-toolbar.ql-snow {
      border-top-left-radius:2px;
      border-top-right-radius:2px;
      background-color: $color__f--bg;
      font-family:inherit;
    }

    .ql-editor.ql-blank::before {
      font-style: normal;
      color:$color__f--placeholder;
      @include font-regular;
    }

    .ql-container.ql-snow {
      border-bottom-left-radius:2px;
      border-bottom-right-radius:2px;
    }

    .ql-editor {
      background-color: $color__f--bg;
      min-height:15px * 6;
      caret-color: $color__action;
      color:$color__text--forms;

      &:hover,
      &:focus {
        background:$color__background;
      }
    }

    .ql-snow .ql-editor {
      h1, h2, h3, h4, h5 {
        font-weight:700;
      }

      p, ul, ol, h1, h2, h3, h4, h5 {
        margin-bottom:1em;
      }
    }

    .ql-snow .ql-editor h1 {
      font-size: 2em;
    }

    .ql-snow .ql-editor h2 {
      font-size: 1.66em;
    }

    .ql-snow .ql-editor h3 {
      font-size: 1.33em;
    }

    .ql-snow .ql-editor h4 {
      font-size: 1.25em;
    }

    .ql-toolbar.ql-snow {
      border-color:$color__fborder;
      border-bottom-color:$color__border--light;
    }

    .ql-container.ql-snow {
      border-color:$color__fborder;
    }

    .input--error {
      .ql-toolbar.ql-snow {
        border-color:$color__error;
        border-bottom-color:$color__border--light;
      }

      .ql-container.ql-snow {
        border-color:$color__error;
      }
    }

    .s--focus {
      .ql-toolbar.ql-snow {
        border-color:$color__fborder--hover;
        border-bottom-color:$color__border--light;
      }
      .ql-container.ql-snow {
        border-color:$color__fborder--hover;
      }
    }

    .ql-snow a {
      color:$color__link;
    }

    .ql-snow.ql-toolbar {
      padding: 13px 8px;

      button {
        width: 24px;
        margin-right: 35px - 6px - 6px - 6px - 6px;
        text-align:center;
      }

      button.ql-underline {
        top:1px;
      }

      button.ql-link {
        width:24px + 9px;
      }

      .icon {
        position:relative;
      }
    }

    .ql-snow.ql-toolbar .ql-formats {
      border-right:1px solid $color__border--light;
    }

    .ql-snow.ql-toolbar,
    .ql-snow .ql-toolbar {
      button:hover,
      button:focus,
      button.ql-active,
      .ql-picker-label:hover,
      .ql-picker-label.ql-active,
      .ql-picker-item:hover,
      .ql-picker-item.ql-selected {
        color:$color__link;
      }
    }

    .ql-snow.ql-toolbar,
    .ql-snow .ql-toolbar {
      button:hover .ql-stroke,
      button:focus .ql-stroke,
      button.ql-active .ql-stroke,
      .ql-picker-label:hover .ql-stroke,
      .ql-picker-label.ql-active .ql-stroke,
      .ql-picker-item:hover .ql-stroke,
      .ql-picker-item.ql-selected .ql-stroke,
      button:hover .ql-stroke-miter,
      button:focus .ql-stroke-miter,
      button.ql-active .ql-stroke-miter,
      .ql-picker-label:hover .ql-stroke-miter,
      .ql-picker-label.ql-active .ql-stroke-miter,
      .ql-picker-item:hover .ql-stroke-miter,
      .ql-picker-item.ql-selected .ql-stroke-miter {
        color:$color__link;
      }
    }

    /* dropdown style */
    .ql-toolbar.ql-snow .ql-picker-label {
      border:0 none;
      position:relative;
      padding-right: 30px;

      &::after {
        content: " ";
        position: absolute;
        top: 50%;
        right: 1em;
        z-index: 2;
        position:absolute;
        width: 0;
        height: 0;
        margin-top: -3px;
        border-width: 4px 4px 0;
        border-style: solid;
        border-color: $color__text transparent transparent;
      }

      svg {
        opacity:0;
      }
    }
    .ql-toolbar.ql-snow .ql-picker-options {
      background:rgba($color__background,0.98);
      border-radius:2px;
      box-shadow:$box-shadow;
      padding:10px 0;
      border:0 none;
      margin-top:6px;

      .ql-picker-item {
        display:block;
        color:$color__text--light;
        padding:0 15px;
        padding-right:50px;
        height:40px;
        line-height: 40px;
        text-decoration: none;
        white-space: nowrap;
        font-family:inherit;

        &:hover {
          color:$color__text;
          background:$color__light;
        }
      }
    }

    .ql-toolbar.ql-snow .ql-picker {
      font-size:1em;
    }

    .ql-toolbar.ql-snow .ql-picker .ql-picker-label {
      white-space: nowrap;

      &::before {
        line-height:24px
      }
    }

    .ql-snow .ql-picker.ql-header {
      width:auto;
      min-width:120px;

      .ql-picker-item,
      .ql-picker-item[data-value="1"],
      .ql-picker-item[data-value="2"],
      .ql-picker-item[data-value="3"],
      .ql-picker-item[data-value="4"],
      .ql-picker-item[data-value="5"] {
        &::before {
          font-weight:normal;
          font-size:1em;
          white-space: nowrap;
        }
      }
    }
  }
</style>
