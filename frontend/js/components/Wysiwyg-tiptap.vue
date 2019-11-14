<template>
  <a17-inputframe :error="error" :note="note" :label="label" :locale="locale" @localize="updateLocale" :size="size"
                  :name="name" :required="required">
    <div class="wysiwyg__outer" :class="textfieldClasses">
      <input :name="name" type="hidden" v-model="value"/>
      <div class="wysiwyg"
           :class="textfieldClasses"
           v-show="!activeSource">
        <div class="wysiwyg__editor" ref="editor">
          <editor-menu-bar :editor="editor"
                           v-slot="{ commands, isActive }">
            <div class="wysiwyg__menubar">
              <div class="toolbar">
                <button
                  class="wysiwyg__button"
                  @click="commands.undo"
                >
                  <span v-svg symbol="undo"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  @click="commands.redo"
                >
                  <span v-svg symbol="redo"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.bold() }"
                  @click="commands.bold"
                >
                  <span v-svg symbol="bold"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.italic() }"
                  @click="commands.italic"
                >
                  <span v-svg symbol="italic"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.strike() }"
                  @click="commands.strike"
                >
                  <span v-svg symbol="strike"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.underline() }"
                  @click="commands.underline"
                >
                  <span v-svg symbol="underline"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.code() }"
                  @click="commands.code"
                >
                  <span v-svg symbol="code"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.paragraph() }"
                  @click="commands.paragraph"
                >
                  <span v-svg symbol="paragraph"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.heading({ level: 1 }) }"
                  @click="commands.heading({ level: 1 })"
                >
                  H1
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.heading({ level: 2 }) }"
                  @click="commands.heading({ level: 2 })"
                >
                  H2
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.heading({ level: 3 }) }"
                  @click="commands.heading({ level: 3 })"
                >
                  H3
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.bullet_list() }"
                  @click="commands.bullet_list"
                >
                  <span v-svg symbol="ul"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.ordered_list() }"
                  @click="commands.ordered_list"
                >
                  <span v-svg symbol="ol"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.blockquote() }"
                  @click="commands.blockquote"
                >
                  <span v-svg symbol="quote"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  :class="{ 'is-active': isActive.code_block() }"
                  @click="commands.code_block"
                >
                  <span v-svg symbol="code"></span>
                </button>

                <button
                  class="wysiwyg__button"
                  @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })"
                >
                  <span v-svg symbol="table"></span>
                </button>

                <span v-if="isActive.table()">
                  <button
                    class="wysiwyg__button"
                    @click="commands.deleteTable"
                  >
                   <span v-svg symbol="delete_table"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.addColumnBefore"
                  >
                   <span v-svg symbol="add_col_before"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.addColumnAfter"
                  >
                   <span v-svg symbol="add_col_after"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.deleteColumn"
                  >
                   <span v-svg symbol="delete_col"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.addRowBefore"
                  >
                   <span v-svg symbol="add_row_before"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.addRowAfter"
                  >
                   <span v-svg symbol="add_row_after"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.deleteRow"
                  >
                   <span v-svg symbol="delete_row"></span>
                  </button>
                  <button
                    class="wysiwyg__button"
                    @click="commands.toggleCellMerge"
                  >
                   <span v-svg symbol="combine_cells"></span>
                  </button>
                 </span>
              </div>
            </div>
          </editor-menu-bar>
          <editor-content class="editor__content" :editor="editor"/>
        </div>
        <span v-if="shouldShowCounter" class="input__limit f--tiny" :class="limitClasses">{{ counter }}</span>
      </div>
      <template v-if="editSource">
        <div class="form__field form__field--textarea" v-show="activeSource">
          <textarea :placeholder="placeholder" :autofocus="autofocus" v-model="value"
                    :style="textareaHeight"></textarea>
          <a17-button variant="ghost" @click="toggleSourcecode" class="wysiwyg__button">Source code</a17-button>
        </div>
      </template>
      <template v-else>
        <span v-if="shouldShowCounter" class="input__limit f--tiny" :class="limitClasses">{{ counter }}</span>
      </template>
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
    TodoItem,
    TodoList,
    Bold,
    Code,
    Italic,
    Link,
    Table,
    TableHeader,
    TableCell,
    TableRow,
    Strike,
    Underline,
    History
  } from 'tiptap-extensions'

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
      hasMaxlength: function () {
        return this.maxlength > 0
      },
      shouldShowCounter: function () {
        return this.hasMaxlength && this.showCounter
      },
      limitClasses: function () {
        return {
          'input__limit--red': this.counter < 10
        }
      },
      ...mapState({
        baseUrl: state => state.form.baseUrl
      })
    },
    components: {
      EditorContent,
      EditorMenuBar
    },
    data () {
      return {
        value: this.initialValue,
        editorHeight: 50,
        toolbarHeight: 52,
        focused: false,
        activeSource: false,
        counter: 0,
        editor: new Editor({
          extensions: [
            new Blockquote(),
            new BulletList(),
            new CodeBlock(),
            new HardBreak(),
            new Heading({ levels: [1, 2, 3] }),
            new ListItem(),
            new OrderedList(),
            new TodoItem(),
            new TodoList(),
            new Link(),
            new Bold(),
            new Code(),
            new Italic(),
            new Strike(),
            new Underline(),
            new History(),
            new Table({
              resizable: true
            }),
            new TableHeader(),
            new TableCell(),
            new TableRow()
          ],
          content: 'This class is a central building block of tiptap. It does most of the heavy lifting of creating a working ProseMirror editor such as creating the EditorView, setting the initial EditorState and so on.\n' +
            '\n' +
            'Although tiptap tries to hide most of the complexity of ProseMirror, tiptap is built on top of its APIs and we strongly recommend you to read through the ProseMirror Guide. You\'ll have a better understanding of how everything works under the hood and get familiar with many terms and jargon used by tiptap.\n' +
            '\n' +
            'You must create an instance of Editor class and pass it to the EditorContent component. The Editor constructor accepts an object of editor options.'
        })
      }
    },
    methods: {
      updateEditor: function (newValue) {
        console.log('update editor', newValue)
        // convert string to HTML and update the content silently
        // const htmlData = this.quill.clipboard.convert(newValue)
        this.content = newValue
      },
      updateFromStore: function (newValue) {
        if (typeof newValue === 'undefined') newValue = ''

        if (this.value !== newValue) {
          console.warn('updateFromStore - Update UI value : ' + this.name + ' -> ' + newValue)
          this.value = newValue
          this.updateEditor(newValue)
        }
      },
      textUpdate: debounce(function () {
        this.saveIntoStore() // see formStore mixin
      }, 600),
      toggleSourcecode: function () {
        this.editorHeight = (Math.max(50, this.$refs.editor.clientHeight) + this.toolbarHeight - 1) + 'px'
        this.activeSource = !this.activeSource

        // set editor content
        this.updateEditor(this.value)
        this.saveIntoStore() // see formStore mixin
      },
      updateCounter: function (newValue) {
        if (this.showCounter && this.hasMaxlength) {
          this.counter = this.maxlength - newValue
        }
      },
      getTextLength: function () {
        // see https://quilljs.com/docs/api/#getlength
        return 0
        // fixme: calcul right length from tiptap
        // return this.quill.getLength() - (this.value.length === 0 ? 2 : 1)
      }
    },
    mounted () {
      console.log('wysiwyg mounted', this.initialValue, this.options)
    },
    beforeDestroy () {
      this.editor.destroy()
    }
  }
</script>

<style scoped lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';

  .wysiwyg__editor {
  }

  .wysiwyg__button {
    @include btn-reset;
    width: 20px;
    height: 20px;
    border: 1px solid black;
  }
</style>
