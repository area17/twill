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

  const QuillDefaultFormats = [
    'background',
    'bold',
    'color',
    'font',
    'code',
    'italic',
    'link',
    'size',
    'strike',
    'script',
    'underline',
    'blockquote',
    'header',
    'indent',
    'list',
    'align',
    'direction',
    'code-block',
    'formula',
    'image',
    'video'
  ]

  function getQuillFormats (toolbarEls) {
    const formats = []
    function addFormat (format) {
      if (formats.indexOf(format) > -1 || QuillDefaultFormats.indexOf(format) === -1) return
      formats.push(format)
    }

    toolbarEls.forEach((el) => {
      if (typeof el === 'object') {
        for (let property in el) {
          addFormat(property)
        }
      }

      if (typeof el === 'string') {
        addFormat(el)
      }
    })

    return formats
  }

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
          ],
          clipboard: {
            matchVisual: false
          }
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
      getIcon: function (shape) {
        return '<span class="icon icon--wysiwyg_' + shape + '" aria-hidden="true"><svg><title>' + shape + '</title><use xlink:href="#wysiwyg_' + shape + '"></use></svg></span>'
      },
      updateInput: function () {
        this.$refs.input.value = this.value

        // see formStore mixin
        this.saveIntoStore()
      }
    },
    watch: {
      submitting: function () {
        if (this.submitting) { // The form is about to submit so lets make sure we saved the wysiwyg
          this.updateInput()
        }
      }
    },
    mounted: function () {
      var self = this

      self.options.theme = self.options.theme || 'snow'
      self.options.boundary = self.options.boundary || document.body
      self.options.modules = self.options.modules || self.defaultModules
      self.options.modules.toolbar = self.options.modules.toolbar !== undefined ? self.options.modules.toolbar : self.defaultModules.toolbar
      self.options.placeholder = self.options.placeholder || self.placeholder
      self.options.readOnly = self.options.readOnly !== undefined ? self.options.readOnly : self.readonly
      self.options.formats = getQuillFormats(self.options.modules.toolbar) // Formats are based on current toolbar configuration

      const icons = Quill.import('ui/icons') // custom icons
      icons['bold'] = self.getIcon('bold')
      icons['italic'] = self.getIcon('italic')
      icons['underline'] = self.getIcon('underline')
      icons['link'] = self.getIcon('link')
      icons['header']['1'] = self.getIcon('header')
      icons['header']['2'] = self.getIcon('header-2')
      icons['header']['3'] = self.getIcon('header-3')
      icons['header']['4'] = self.getIcon('header-4')
      icons['header']['5'] = self.getIcon('header-5')
      icons['header']['6'] = self.getIcon('header-6')

      // init Quill
      this.quill = new Quill(self.$refs.editor, self.options)

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
        }
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

      p, ul, h1, h2, h3, h4, h5 {
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
