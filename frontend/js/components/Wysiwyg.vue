<template>
  <a17-inputframe :error="error" :note="note" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name">
    <div class="wysiwyg" :class="textfieldClasses">
      <input ref="input" :name="name" :id="name" :disabled="disabled" :required="required" :readonly="readonly" type="hidden" value="" />
      <div class="wysiwyg__editor" ref="editor"></div>
    </div>
  </a17-inputframe>
</template>

<script>
  import 'quill/dist/quill.snow.css'
  import 'quill/dist/quill.bubble.css'
  import 'quill/dist/quill.core.css'

  import Quill from 'quill'

  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17Wysiwyg',
    mixins: [InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
    props: {
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
      rows: {
        type: Number,
        default: 5
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
      textfieldClasses: function () {
        return {
          's--disabled': this.disabled,
          's--focus': this.focused
        }
      }
    },
    data: function () {
      return {
        value: this.initialValue,
        focused: false,
        defaultModules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'link']
          ]
          // Complete Toolbar example :
          //
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
      updateInput: function () {
        this.$refs.input.value = this.value

        // see formStore mixin
        this.saveIntoStore()
      }
    },
    mounted: function () {
      var self = this

      self.options.theme = self.options.theme || 'snow'
      self.options.boundary = self.options.boundary || document.body
      self.options.modules = self.options.modules || self.defaultModules
      self.options.modules.toolbar = self.options.modules.toolbar !== undefined
                                      ? self.options.modules.toolbar
                                      : self.defaultModules.toolbar
      self.options.placeholder = self.options.placeholder || self.placeholder
      self.options.readOnly = self.options.readOnly !== undefined ? self.options.readOnly : self.readonly

      // init Quill
      this.quill = new Quill(self.$refs.editor, self.options)

    // set editor content
      if (self.value) {
        self.quill.pasteHTML(self.value)
      }

      // update model if text changes
      self.quill.on('text-change', (delta, oldDelta, source) => {
        var html = self.$refs.editor.children[0].innerHTML
        var text = self.quill.getText()

        if (html === '<p><br></p>') html = ''
        self.value = html

        self.$emit('input', self.value)
        self.$emit('change', {
          editor: self.quill,
          html: html,
          text: text
        })
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

      // emit ready
      self.$emit('ready', self.quill)
    },
    beforeDestroy () {
      this.quill = null
    }
  }
</script>

<style lang="scss"> /* Not scoped style here because we want to overwrite default style of the wysiwig */
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  .a17 {
    .ql-toolbar.ql-snow {
      border-top-left-radius:2px;
      border-top-right-radius:2px;
      background-color: $color__f--bg;
    }

    .ql-container.ql-snow {
      border-bottom-left-radius:2px;
      border-bottom-right-radius:2px;
    }

    .ql-editor {
      background-color: $color__f--bg;
      min-height:15px * 5;
      caret-color: $color__action;

      &:hover,
      &:focus {
        background:$color__background;
      }
    }

    .ql-toolbar.ql-snow {
      border-color:$color__fborder;
      border-bottom-color:$color__border--light;
    }

    .ql-container.ql-snow {
      border-color:$color__fborder;
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
  }
</style>
