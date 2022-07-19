<template>
  <a17-inputframe :error="error"
                  :note="note"
                  :label="label"
                  :locale="locale"
                  @localize="updateLocale"
                  :size="size"
                  :name="name"
                  :required="required">
    <div class="wysiwyg__outer">
      <div class="wysiwyg"
          :class="textfieldClasses"
          v-show="!activeSource"
          :dir="dirLocale">
        <input :name="name"
               type="hidden"
               v-model="value"/>
        <div class="wysiwyg__editor"
             ref="editor">
          <editor-menu-bar :editor="editor"
                           v-slot="{ commands, isActive, getMarkAttrs }">
            <div class="wysiwyg__menubar">

              <template v-if="toolbar.header">
                <wysiwyg-menu-bar-btn icon="paragraph"
                                      v-if="toolbar.header"
                                      :isActive="isActive.paragraph()"
                                      @btn:click="commands.paragraph"/>
                <wysiwyg-menu-bar-btn v-for="headingLevel in headingOptions"
                                      :key="headingLevel"
                                      :icon="headingLevel > 1 ? `header-${headingLevel}` : 'header'"
                                      :isActive="isActive.heading({ level: headingLevel })"
                                      @btn:click="commands.heading({ level: headingLevel })"/>
              </template>

              <wysiwyg-menu-bar-btn icon="bold"
                                    v-if="toolbar.bold"
                                    :isActive="isActive.bold()"
                                    @btn:click="commands.bold"/>
              <wysiwyg-menu-bar-btn icon="italic"
                                    v-if="toolbar.italic"
                                    :isActive="isActive.italic()"
                                    @btn:click="commands.italic"/>
              <wysiwyg-menu-bar-btn icon="strike"
                                    v-if="toolbar.strike"
                                    :isActive="isActive.strike()"
                                    @btn:click="commands.strike"/>
              <wysiwyg-menu-bar-btn icon="underline"
                                    v-if="toolbar.underline"
                                    :isActive="isActive.underline()"
                                    @btn:click="commands.underline"/>

              <a href="javascript://"
                 @click="openLinkWindow(getMarkAttrs('link'))">
                <wysiwyg-menu-bar-btn icon="link"
                                      v-if="toolbar.link"
                                      :isActive="isActive.link()"/>
              </a>

              <wysiwyg-menu-bar-btn icon="ul"
                                    v-if="toolbar.bullet"
                                    :isActive="isActive.bullet_list()"
                                    @btn:click="commands.bullet_list"/>

              <wysiwyg-menu-bar-btn icon="ol"
                                    v-if="toolbar.ordered"
                                    :isActive="isActive.ordered_list()"
                                    @btn:click="commands.ordered_list"/>

              <wysiwyg-menu-bar-btn icon="quote"
                                    v-if="toolbar.blockquote"
                                    :isActive="isActive.blockquote()"
                                    @btn:click="commands.blockquote"/>

              <wysiwyg-menu-bar-btn icon="code"
                                    v-if="toolbar['code-block']"
                                    :isActive="isActive.code_block()"
                                    @btn:click="commands.code_block"/>

              <wysiwyg-menu-bar-btn icon="code"
                                    v-if="toolbar['code']"
                                    :isActive="isActive.code()"
                                    @btn:click="commands.code"/>

              <template v-if="toolbar.table">
                <wysiwyg-menu-bar-btn icon="table"
                                      @btn:click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: true })"/>

                <div class="wysiwyg__menubar-table-buttons"
                     v-if="isActive.table()">

                  <wysiwyg-menu-bar-btn icon="delete_table"
                                        @btn:click="commands.deleteTable"/>

                  <wysiwyg-menu-bar-btn icon="add_col_before"
                                        @btn:click="commands.addColumnBefore"/>

                  <wysiwyg-menu-bar-btn icon="add_col_after"
                                        @btn:click="commands.addColumnAfter"/>

                  <wysiwyg-menu-bar-btn icon="delete_col"
                                        @btn:click="commands.deleteColumn"/>

                  <wysiwyg-menu-bar-btn icon="add_row_before"
                                        @btn:click="commands.addRowBefore"/>

                  <wysiwyg-menu-bar-btn icon="add_row_after"
                                        @btn:click="commands.addRowAfter"/>

                  <wysiwyg-menu-bar-btn icon="delete_row"
                                        @btn:click="commands.deleteRow"/>

                  <wysiwyg-menu-bar-btn icon="combine_cells"
                                        @btn:click="commands.toggleCellMerge"/>
                </div>
              </template>
              <wysiwyg-menu-bar-btn icon="undo"
                                    @btn:click="commands.undo"/>
              <wysiwyg-menu-bar-btn icon="redo"
                                    @btn:click="commands.redo"/>
            </div>
          </editor-menu-bar>
          <div class="wysiwyg__contentWrapper" :class="{ 'wysiwyg__contentWrapper--limitHeight' : limitHeight }">
            <editor-content class="wysiwyg__content"
                            :editor="editor"/>
          </div>
        </div>
        <span v-if="shouldShowCounter"
              class="input__limit f--tiny"
              :class="limitClasses">{{ counter }}</span>
      </div>
      <template v-if="editSource">
        <div class="form__field form__field--textarea"
             v-show="activeSource">
          <textarea :placeholder="placeholder"
                    :autofocus="autofocus"
                    v-model="value"
                    @change="updateSourcecode"
                    :style="textareaHeight"></textarea>
        </div>
        <a17-button variant="ghost"
                    @click="toggleSourcecode"
                    class="wysiwyg__button">Source code
        </a17-button>
      </template>
    </div>
    <div class="link-window" v-if="linkWindow">
      <div class="link-window-inner">
        <label>
          Link:
        </label>
        <input type="text" v-model="linkWindow.href" @keyup:enter="saveLink">

        <label>
          <input type="checkbox" v-model="linkWindow.target" true-value="_blank" false-value="">
          Open in a new window
        </label>

        <br>

        <button @click="saveLink">
          Save
        </button>
      </div>
    </div>
  </a17-inputframe>
</template>

<script>
  import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
  import {
    Blockquote,
    CodeBlock,
    HardBreak,
    Heading,
    OrderedList,
    BulletList,
    ListItem,
    Bold,
    Code,
    Italic,
    Link,
    Placeholder,
    Table,
    TableHeader,
    TableCell,
    TableRow,
    Strike,
    Underline,
    History
  } from 'tiptap-extensions'
  import WysiwygMenuBarBtn from '@/components/WysiwygMenuBarButton'

  import { mapState } from 'vuex'
  import debounce from 'lodash/debounce'

  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  // Todo: load highligth depending of needs
  // import { loadScript } from '@/utils/loader'
  //
  // const HIGHLIGHT = '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.12.0/build/highlight.min.js'

  export default {
    name: 'A17Wysiwyg',
    mixins: [InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
    props: {
      editSource: {
        type: Boolean,
        default: false
      },
      showCounter: {
        type: Boolean,
        default: true
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
      limitHeight: {
        type: Boolean,
        default: false
      },
      options: {
        type: Object,
        required: false,
        default: function () {
          return {
            modules: {}
          }
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
      hasMaxlength: function () {
        return this.maxlength > 0
      },
      shouldShowCounter: function () {
        return this.hasMaxlength && this.showCounter
      },
      limitClasses: function () {
        return {
          'input__limit--red': this.counter < (this.maxlength * 0.1)
        }
      },
      ...mapState({
        baseUrl: state => state.form.baseUrl
      })
    },
    components: {
      EditorContent,
      EditorMenuBar,
      'wysiwyg-menu-bar-btn': WysiwygMenuBarBtn
    },
    data () {
      return {
        value: this.initialValue,
        editorHeight: 50,
        toolbarHeight: 52,
        toolbar: this.options.modules.toolbar
          ? this.options.modules.toolbar
            .reduce((obj, item) => {
              if (item.list) {
                obj[item.list] = true
                return obj
              } else if (typeof item === 'object') {
                return {
                  ...obj,
                  ...item
                }
              } else {
                obj[item] = true
                return obj
              }
            }, {})
          : {
            bold: true,
            italic: true,
            underline: true,
            link: true
          },
        headingOptions: [],
        focused: false,
        activeSource: false,
        counter: 0,
        editor: null,
        linkWindow: null
      }
    },
    methods: {
      updateEditor: function (newValue) {
        if (this.editor) {
          this.editor.setContent(newValue)
        }
      },
      updateFromStore: function (newValue) {
        if (typeof newValue === 'undefined') newValue = ''

        if (this.value !== newValue) {
          this.value = newValue
          this.updateEditor(newValue)
        }
      },
      textUpdate: function () {
        this.preventSubmit()
        this._textUpdateInternal()
      },
      _textUpdateInternal: debounce(function () {
        this.saveIntoStore()
        this.allowSubmit()
      }, 600),
      toggleSourcecode: function () {
        this.editorHeight = (Math.max(50, this.$refs.editor.clientHeight) + this.toolbarHeight - 1) + 'px'
        this.activeSource = !this.activeSource

        this.updateSourcecode()
      },
      updateSourcecode: function () {
        // set editor content
        this.updateEditor(this.value)
        this.saveIntoStore() // see formStore mixin
      },
      updateCounter () {
        if (this.showCounter && this.hasMaxlength) {
          this.counter = this.maxlength - this.getTextLength()
        }
      },
      getTextLength () {
        return this.editor.getHTML().replace(/<[^>]+>/g, '').length
      },
      openLinkWindow: function (markAttributes) {
        this.linkWindow = {
          href: markAttributes.href,
          target: markAttributes.target
        }
      },
      saveLink () {
        this.editor.commands.link(this.linkWindow)
        this.linkWindow = null
      }
    },
    beforeMount () {
      const content = this.value || ''
      const extensions = [
        new History(),
        new HardBreak()
      ]

      if (this.placeholder && this.placeholder.length > 0) {
        extensions.push(new Placeholder({
          emptyNodeClass: 'is-empty',
          emptyNodeText: this.placeHolder,
          showOnlyWhenEditable: true
        }))
      }

      if (this.toolbar.ordered || this.toolbar.bullet) {
        extensions.push(new ListItem())
      }

      Object.keys(this.toolbar).forEach(tool => {
        switch (tool) {
          case 'header': {
            const levels = this.toolbar[tool].filter(level => typeof level === 'number')
            levels.forEach(level => {
              this.headingOptions.push(level)
            })
            extensions.push(new Heading({
              levels: levels
            }))
            break
          }
          case 'bold': {
            extensions.push(new Bold())
            break
          }
          case 'italic': {
            extensions.push(new Italic())
            break
          }
          case 'strike': {
            extensions.push(new Strike())
            break
          }
          case 'underline': {
            extensions.push(new Underline())
            break
          }
          case 'link': {
            extensions.push(new Link({
              HTMLAttributes: {
                target: null,
                rel: null
              },
              openOnClick: false
            }))
            break
          }
          case 'blockquote': {
            extensions.push(new Blockquote())
            break
          }
          case 'bullet': {
            extensions.push(new BulletList())
            break
          }
          case 'ordered': {
            extensions.push(new OrderedList())
            break
          }
          case 'code': {
            extensions.push(new Code())
            break
          }
          case 'code-block': {
            extensions.push(new CodeBlock())
            break
          }
          case 'table': {
            extensions.push(new Table({
              resizable: false
            }))
            extensions.push(new TableHeader())
            extensions.push(new TableCell())
            extensions.push(new TableRow())
            break
          }
        }
      })

      this.editor = new Editor({
        extensions: extensions,
        content: content,
        onUpdate: ({ getHTML }) => {
          this.value = getHTML()
          this.textUpdate()
          this.updateCounter()
        }
      })

      this.updateCounter()
    },
    beforeDestroy () {
      this.editor.destroy()
    }
  }
</script>

<style scoped lang="scss">
  $height_input: 45px;

  .wysiwyg {
    position: relative;

    .input__limit {
      color: $color__text--light;
      user-select: none;
      pointer-events: none;
      position: absolute;
      right: 15px;
      bottom: 15px;

      &.input__limit--red {
        color: $color__error;
      }
    }
  }

  .wysiwyg__editor {
    @include textfield;
    @include defaultState;
    position: relative;

    .input--error & {
      border-color: $color__error;
    }

    &.s--focus {
      @include focusState;
    }

    &:hover {
      @include focusState;
    }

    &.s--disabled {
      @include disabledState;
    }
  }

  .wysiwyg[dir='rtl'] .wysiwyg__editor {
    direction: rtl;
    text-align: right;
  }

  .wysiwyg__menubar {
    padding: 5px 8px;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
    background-color: $color__f--bg;
    border-bottom: 1px solid $color__border--light;

    .s--focus & {
      border-color: $color__fborder--hover;
      border-bottom-color: $color__border--light;
    }
  }

  .wysiwyg__button {
    margin-top: 20px;
  }

  .wysiwyg__contentWrapper {
    padding: 15px;
    min-height: 90px;
  }

  .wysiwyg__contentWrapper--limitHeight {
    max-height: calc(100vh - 250px);
    overflow-y: scroll;
  }

  .wysiwyg__menubar-table-buttons {
    display: inline;
  }

  .wysiwyg__menubar-heading {
    display: inline-block;
    max-width: 150px;
    margin-right: 10px;
  }
</style>

<style lang="scss">
.wysiwyg__content {
  .ProseMirror {
    color: $color__text;

    h1, h2, h3, h4, h5, h6 {
      font-weight: 700;
    }

    b, p b, p strong, strong {
      font-weight:700;
    }

    p, ul, ol, h1, h2, h3, h4, h5 {
      margin-bottom: 1em;
    }

    ol {
      padding-left: 1em;

      li {
        list-style-type: decimal;
      }
    }

    ul {
      padding-left: 1em;

      li {
        list-style-type: disc;
      }
    }

    h1 {
      font-size: 2em;
      line-height: 1.25em;
    }

    h2 {
      font-size: 1.66em;
      line-height: 1.25em;
    }

    h3 {
      font-size: 1.33em;
      line-height: 1.25em;
    }

    h4 {
      font-size: 1.25em;
      line-height: 1.25em;
    }

    h5 {
      font-size: 1em;
      line-height: 1.25em;
    }

    // default code syntax hightlighting is github
    pre {
      color: $color__wysiwyg-codeText;
      padding: 15px;
      overflow: auto;
      background-color: $color__wysiwyg-codeBg;
      border-radius: 3px;
      font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
    }

    a {
      color: $color__link;
    }

    sup {
      vertical-align: super;
      font-size: smaller;
    }

    sub {
      vertical-align: sub;
      font-size: smaller;
    }

    .tableWrapper {
      margin: 1em 0;
      overflow-x: auto;
    }

    table {
      border-collapse: collapse;
      table-layout: fixed;
      width: 100%;
      margin: 0;
      overflow: hidden;

      .selectedCell:after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background: rgba(234, 244, 250, .8);
        pointer-events: none;
        z-index: 2;
      }
    }

    table td,
    table th {
      min-width: 1em;
      border: 2px solid $color__border;
      padding: 3px 5px;
      vertical-align: top;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      position: relative;
      width: 80px;
    }

    blockquote {
      border-left: 3px solid $color__border;
      color: rgba(0, 0, 0, .8);
      padding-left: .8rem;
    }

    p.is-empty:first-child:before {
      content: attr(data-empty-text);
      float: left;
      color: $color__text--light;
      pointer-events: none;
      height: 0;
      font-style: italic;
    }
  }
}

.link-window {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 9999;
  background-color: rgba(0, 0, 0, .5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.link-window-inner {
  width: 100%;
  max-width: 500px;
  background-color: #fff;
  border-radius: 3px;
  padding: 20px;
  box-shadow: 0 0 10px rgba(0, 0, 0, .2);
}

.link-window input[type=text] {
  margin-bottom: 15px;
  padding: 14px;
  border: 1px solid #cbcbcb;
  width: 100%;
}

.link-window label {
  display: block;
  padding: 8px 0;
}

.link-window button {
  border: 0;
  background: #4b4bd8;
  color: white;
  padding: 10px 30px;
  cursor: pointer;
}

</style>
