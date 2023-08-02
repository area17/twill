<template>
  <a17-inputframe :error="error"
                  :note="note"
                  :label="label"
                  :locale="locale"
                  @localize="updateLocale"
                  :size="size"
                  :name="name"
                  :required="required">
    <div class="wysiwyg__outer" v-if="editor">
      <div class="wysiwyg"
           :class="textfieldClasses"
           v-show="!activeSource"
           :dir="dirLocale">
        <input :name="name"
               type="hidden"
               v-model="value"/>
        <div class="wysiwyg__editor"
             ref="editor">
          <div class="wysiwyg__menubar">

            <template v-if="toolbar.header">
              <wysiwyg-menu-bar-btn icon="paragraph"
                                    :disabled="editor.isActive('paragraph')"
                                    v-if="toolbar.header"
                                    :isActive="editor.isActive('paragraph')"
                                    @btn:click="editor.chain().focus().setParagraph().run()"/>
              <wysiwyg-menu-bar-btn v-for="headingLevel in headingOptions"
                                    :key="headingLevel"
                                    :icon="headingLevel > 1 ? `header-${headingLevel}` : 'header'"
                                    :isActive="editor.isActive('heading', { level: headingLevel })"
                                    @btn:click="editor.chain().focus().toggleHeading({ level: headingLevel }).run()"/>
            </template>

            <wysiwyg-menu-bar-btn icon="bold"
                                  v-if="toolbar.bold"
                                  :isActive="editor.isActive('bold')"
                                  @btn:click="editor.chain().focus().toggleBold().run()"/>
            <wysiwyg-menu-bar-btn icon="italic"
                                  v-if="toolbar.italic"
                                  :isActive="editor.isActive('italic')"
                                  @btn:click="editor.chain().focus().toggleItalic().run()"/>
            <wysiwyg-menu-bar-btn icon="strike"
                                  v-if="toolbar.strike"
                                  :isActive="editor.isActive('strike')"
                                  @btn:click="editor.chain().focus().toggleStrike().run()"/>
            <wysiwyg-menu-bar-btn icon="underline"
                                  v-if="toolbar.underline"
                                  :isActive="editor.isActive('underline')"
                                  @btn:click="editor.chain().focus().toggleUnderline().run()"/>

            <wysiwyg-menu-bar-btn icon="hr"
                                  v-if="toolbar.hr"
                                  @btn:click="editor.chain().focus().setHorizontalRule().run()"/>

            <wysiwyg-menu-bar-btn icon="link"
                                  v-if="toolbar.link"
                                  :isActive="editor.isActive('link')"
                                  @btn:click="openLinkWindow()"
            />

            <wysiwyg-menu-bar-btn icon="unlink"
                                  v-if="toolbar.link"
                                  :disabled="!editor.isActive('link')"
                                  :isActive="editor.isActive('link')"
                                  @btn:click="removeLink()"/>

            <wysiwyg-menu-bar-btn icon="ul"
                                  v-if="toolbar.bullet"
                                  :isActive="editor.isActive('bulletList')"
                                  @btn:click="editor.chain().focus().toggleBulletList().run()"/>

            <wysiwyg-menu-bar-btn icon="ol"
                                  v-if="toolbar.ordered"
                                  :isActive="editor.isActive('orderedList')"
                                  @btn:click="editor.chain().focus().toggleOrderedList().run()"/>

            <wysiwyg-menu-bar-btn icon="quote"
                                  v-if="toolbar.blockquote"
                                  :isActive="editor.isActive('blockquote')"
                                  @btn:click="editor.chain().focus().toggleBlockquote().run()"/>

            <wysiwyg-menu-bar-btn icon="code"
                                  v-if="toolbar.codeBlock"
                                  :isActive="editor.isActive('codeBlock')"
                                  @btn:click="editor.chain().focus().toggleCodeBlock().run()"/>

            <wysiwyg-menu-bar-btn icon="code"
                                  v-if="toolbar.code"
                                  :isActive="editor.isActive('code')"
                                  @btn:click="editor.chain().focus().setCode().run()"/>

            <wysiwyg-menu-bar-btn icon="table"
                                  v-if="toolbar.table"
                                  @btn:click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()"/>
            <wysiwyg-menu-bar-btn icon="undo"
                                  :disabled="!editor.can().undo()"
                                  @btn:click="editor.chain().focus().undo().run()"/>
            <wysiwyg-menu-bar-btn icon="redo"
                                  :disabled="!editor.can().redo()"
                                  @btn:click="editor.chain().focus().redo().run()"/>

            <template v-if="toolbar.table">
              <div class="wysiwyg__menubar-table-buttons"
                   v-if="editor.isActive('table')">
                <br/>

                <wysiwyg-menu-bar-btn icon="delete_table"
                                      @btn:click="editor.chain().focus().deleteTable().run()"/>

                <wysiwyg-menu-bar-btn icon="add_col_before"
                                      @btn:click="editor.chain().focus().addColumnBefore().run()"/>

                <wysiwyg-menu-bar-btn icon="add_col_after"
                                      @btn:click="editor.chain().focus().addColumnAfter().run()"/>

                <wysiwyg-menu-bar-btn icon="delete_col"
                                      @btn:click="editor.chain().focus().deleteColumn().run()"/>

                <wysiwyg-menu-bar-btn icon="add_row_before"
                                      @btn:click="editor.chain().focus().addRowBefore().run()"/>

                <wysiwyg-menu-bar-btn icon="add_row_after"
                                      @btn:click="editor.chain().focus().addRowAfter().run()"/>

                <wysiwyg-menu-bar-btn icon="delete_row"
                                      @btn:click="editor.chain().focus().deleteRow().run()"/>

                <wysiwyg-menu-bar-btn icon="combine_cells"
                                      @btn:click="editor.chain().focus().mergeCells().run()"/>
              </div>
            </template>

            <template v-if="this.toolbar.wrappers">
              <br/>
              <template v-for="wrapper in this.toolbar.wrappers">
                <wysiwyg-menu-bar-btn :icon-url="wrapper.icon"
                                      :key="wrapper.id"
                                      :isActive="editor.isActive(wrapper.class)"
                                      :label="wrapper.label"
                                      @btn:click="editor.commands['set' + wrapper.id]()"/>
              </template>
            </template>

          </div>
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
    <standalone-browser
        v-if="browserIsOpen && browserEndpoints"
        ref="localbrowser"
        :endpoint-multiple="browserEndpoints"
        @selected="setLinkFromBrowser"
        @close="browserIsOpen = false"
        :max="1"
    />
    <a17-modal :title="$trans('wysiwyg.link_window.title', 'Edit link')" ref="link-modal"
               class="modal--form modal--link">
      <template v-if="linkWindow">
        <a17-textfield name="link_text"
                       :initial-value="linkWindow.text"
                       v-model="linkWindow.text"
                       :label="$trans('wysiwyg.link_window.text', 'Text to display')"/>
        <a17-textfield name="link_link"
                       :initial-value="linkWindow.href"
                       v-model="linkWindow.href"
                       :label="$trans('wysiwyg.link_window.link', 'Link')"
                       :placeholder="$trans('wysiwyg.link_window.link_placeholder', 'Link to URL address')"
        />
        <div>
          <a href="#" class="link-browser-link" v-if="browserEndpoints" @click="browserIsOpen = true">
            {{$trans('wysiwyg.link_window.internal_browser_link', 'Select internal content')}}
          </a>
        </div>
        <a17-inputframe>
          <a17-checkbox name="link_target"
                        :initial-value="linkWindow.target"
                        @change="linkWindow.target = $event ? '_blank' : null"
                        value="_blank"
                        :label="$trans('wysiwyg.link_window.open_in_new_window', 'Open in a new window')"/>
        </a17-inputframe>

        <div class="modalValidation">
          <a17-button variant="validate" class="dialog-confirm" @click="saveLink" tabindex="4">
            {{ $trans('wysiwyg.link_window.save', 'Save') }}
          </a17-button>
          <a17-button variant="aslink-grey" class="dialog-cancel" @click="$refs['link-modal'].close()" tabindex="5">
            {{ $trans('wysiwyg.link_window.save', 'Cancel') }}
          </a17-button>
        </div>
      </template>
    </a17-modal>
  </a17-inputframe>
</template>

<script>
  import debounce from 'lodash/debounce'
  import {Editor, EditorContent, getMarkAttributes, mergeAttributes, Node} from '@tiptap/vue-2'
  import StarterKit from '@tiptap/starter-kit'
  import Underline from '@tiptap/extension-underline'
  import Table from '@tiptap/extension-table'
  import TableRow from '@tiptap/extension-table-row'
  import TableCell from '@tiptap/extension-table-cell'
  import TableHeader from '@tiptap/extension-table-header'
  import {mapState} from 'vuex'

  import StandaloneBrowser from "@/components/StandaloneBrowser.vue";
  import WysiwygMenuBarBtn from '@/components/WysiwygMenuBarButton'
  import FormStoreMixin from '@/mixins/formStore'
  import InputMixin from '@/mixins/input'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'
  import {Link} from "@tiptap/extension-link";
  import {Placeholder} from "@tiptap/extension-placeholder";
  import {HardBreak} from "@tiptap/extension-hard-break";
  import {HorizontalRule} from "@tiptap/extension-horizontal-rule";

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
      browserEndpoints: {
        required: false,
        default: null
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
      StandaloneBrowser,
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
        linkWindow: null,
        browserIsOpen: false,
      }
    },
    methods: {
      updateEditor: function (newValue) {
        if (this.editor) {
          this.editor.commands.setContent(newValue)
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
      openLinkWindow: function () {
        this.editor.commands.extendMarkRange('link')
        const {ranges} = this.editor.state.selection

        const markAttributes = getMarkAttributes(this.editor.state, "link")

        let newLink = true;

        let from = ranges[0].$from.pos;
        let to = ranges[0].$to.pos;

        if (markAttributes.href) {
          newLink = false;
        }

        let startPos = null;
        let endPos = null;

        const doc = this.editor.state.tr.doc;
        const htmlLen = this.editor.getHTML().length

        if (from === to) {
          let foundStart = false;

          let foundEnd = false;

          while (!foundStart && from > 0) {
            foundStart = from === 0 || doc.textBetween(from - 1, from) === ' '
            if (doc.textBetween(from - 1, from) === ' ') {
              startPos = from
            } else if (from === 0) {
              startPos = 0;
            }
            from = from - 1
          }

          while (!foundEnd && to < htmlLen) {
            foundEnd = to === htmlLen ||
              doc.textBetween(to, to + 1) === '' ||
              doc.textBetween(to, to + 1) === ' '
            endPos = to
            to = to + 1
          }
        } else {
          startPos = from
          endPos = to
        }

        this.editor.commands.setTextSelection({from: startPos, to: endPos})

        this.linkWindow = {
          newLink,
          from: startPos ?? 0,
          to: endPos,
          textOriginal: this.editor.state.tr.doc.textBetween(startPos, endPos),
          text: this.editor.state.tr.doc.textBetween(startPos, endPos),
          href: markAttributes.href,
          target: markAttributes.target ?? ''
        }

        this.$nextTick(() => {
          this.$refs['link-modal'].open()
        })
      },
      removeLink () {
        this.editor.chain()
          .focus()
          .extendMarkRange('link')
          .unsetLink()
          .run()
      },
      setLinkFromBrowser (item) {
        this.linkWindow.href = '#twillInternalLink::' + item[0].endpointType + '#' + item[0].id
      },
      saveLink () {
        if (this.linkWindow.text !== this.linkWindow.textOriginal) {
          this.editor.commands.insertContentAt({
            from: this.linkWindow.from,
            to: this.linkWindow.to
          }, this.linkWindow.text);
          this.editor.commands.setTextSelection({
            from: this.linkWindow.from,
            to: this.linkWindow.from + this.linkWindow.text.length
          });
        }

        if (this.linkWindow.newLink) {
          this.editor.commands.setLink({href: this.linkWindow.href, target: this.linkWindow.target})
        } else {
          this.editor.commands.updateAttributes("link", {href: this.linkWindow.href, target: this.linkWindow.target})
        }

        this.$refs['link-modal'].close()
        this.linkWindow = null
      }
    },
    beforeMount () {
      if (this.toolbar.header) {
        this.headingOptions = this.toolbar.header.filter((header) => {
          return (typeof header === 'number')
        })
      }

      const content = this.value || ''
      const extensions = [
        HardBreak
      ]

      if (this.placeholder && this.placeholder.length > 0) {
        extensions.push(Placeholder.configure({
          emptyNodeClass: 'is-empty',
          emptyNodeText: this.placeHolder,
          showOnlyWhenEditable: true
        }))
      }

      if (this.toolbar.wrappers) {
        this.toolbar.wrappers.forEach((wrapper) => {
          extensions.push(Node.create({
            name: wrapper.id,
            group: 'block',
            marks: '_',
            atom: true,
            content: 'block+',
            addOptions () {
              return {
                HTMLAttributes: {
                  class: wrapper.className,
                  'data-customwrapper': wrapper.id,
                  'data-customwrapper-label': wrapper.label
                },
              }
            },
            parseHTML () {
              return [
                {
                  tag: 'div',
                  getAttrs: element => {
                    element.getAttribute('data-customwrapper', wrapper.id)
                  }
                },
              ]
            },
            renderHTML ({HTMLAttributes}) {
              return ['div', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
            },
            addCommands () {
              const commandName = 'set' + this.name
              const commandsList = {}

              commandsList[commandName] = () => ({chain}) => {
                if (wrapper.createElement) {
                  switch (wrapper.createElement) {
                    case 'ol':
                      return chain().toggleWrap(this.name).toggleOrderedList().run()
                    case 'ul':
                      return chain().toggleWrap(this.name).toggleBulletList().run()
                  }
                }
                return chain().toggleWrap(this.name).run()
              }

              return commandsList;
            },
          }))
        })
      }

      Object.keys(this.toolbar).forEach(tool => {
        switch (tool) {
          case 'link': {
            extensions.push(Link.configure({openOnClick: false}))
            break;
          }
          case 'underline': {
            extensions.push(Underline)
            break;
          }
          case 'table': {
            extensions.push(Table.configure({
              resizable: false
            }))
            extensions.push(TableHeader)
            extensions.push(TableCell)
            extensions.push(TableRow)
            break
          }
          case 'hr': {
            extensions.push(HorizontalRule)
          }
        }
      })

      extensions.push(StarterKit.configure({
        orderedList: this.toolbar.ordered ?? false,
        bulletList: this.toolbar.bullet ?? false,
        listItem: this.toolbar.ordered || this.toolbar.bullet || false,
        code: this.toolbar.code ?? false,
        codeBlock: this.toolbar.codeBlock ?? false,
      }))

      this.editor = new Editor({
        content,
        extensions,
        onUpdate: ({editor}) => {
          this.value = editor.getHTML()
          this.textUpdate()
          this.updateCounter()
        }
      });

      this.updateCounter()
    },
    beforeUnmount () {
      this.editor.destroy()
    },
    beforeDestroy () {
      this.editor.destroy()
    }
  }
</script>

<style scoped lang="scss">
  $height_input: 45px;

  .modal {
    &--link {
      z-index: $zindex__modal__lower;
    }

    .link-browser-link {
      color: $color__text--light;
      margin-top: 10px;
      display: block;
    }
  }

  .modalValidation {
    display: flex;
    align-items: center;
    margin-top: 35px;
  }

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

      [data-customwrapper] {
        position: relative;
        width: 100%;
        padding: 3px;
        border: 1px dashed hsl(0, 0%, 66.7%);
        margin-top: 10px;
      }

      [data-customwrapper]::before {
        content: attr(data-customwrapper-label);
        position: relative;
        background-color: white;
        top: -15px;
      }

      h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
      }

      b, p b, p strong, strong {
        font-weight: 700;
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
</style>
