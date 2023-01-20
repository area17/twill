import Quill from 'quill'

Quill.debug('error')

const Delta = Quill.import('delta')
const Embed = Quill.import('blots/embed')
const Inline = Quill.import('blots/inline')
const Link = Quill.import('formats/link')
const BlockEmbed = Quill.import('blots/block/embed')

/**
 * Support for horizontal line
 * https://quilljs.com/guides/cloning-medium-with-parchment/#dividers
 */
class DividerBlot extends BlockEmbed {}

DividerBlot.blotName = 'divider'
DividerBlot.tagName = 'hr'

Quill.register(DividerBlot)

/*
* Support for shift enter
* @see https://github.com/quilljs/quill/issues/252
* @see https://codepen.io/mackermedia/pen/gmNwZP
*/
class SoftLineBreakBlot extends Embed {
  static blotName = 'softbreak'
  static tagName = 'br'
  static className = 'softbreak'
  length () {
    return 1
  }

  value () {
    return '\n'
  }

  insertInto (parent, ref) {
    Embed.prototype.insertInto.call(this, parent, ref)
  }
}

Quill.register(SoftLineBreakBlot)

const lineBreakHandle = {
  key: 13,
  shiftKey: true,
  handler:
    function (range) {
      const currentLeaf = this.quill.getLeaf(range.index)[0]
      const nextLeaf = this.quill.getLeaf(range.index + 1)[0]

      this.quill.insertEmbed(range.index, 'softbreak', true, 'user')

      // Insert a second break if:
      // At the end of the editor, OR next leaf has a different parent (<p>)
      if (nextLeaf === null || (currentLeaf.parent !== nextLeaf.parent)) {
        this.quill.insertEmbed(range.index, 'softbreak', true, 'user')
      }

      // Now that we've inserted a line break, move the cursor forward
      this.quill.setSelection(range.index + 1, Quill.sources.SILENT)
    }
}

function lineBreakMatcher () {
  const newDelta = new Delta()
  newDelta.insert({ softbreak: '' })
  return newDelta
}

const anchor = {
  blotName: 'anchor',
  tagName: 'SPAN'
}

class Anchor extends Inline {
  static create (value) {
    const node = super.create(value)
    value = this.sanitize(value)
    node.setAttribute('id', value)
    node.className = 'ql-anchor'
    return node
  }

  static sanitize (id) {
    return id.replace(/\s+/g, '-').toLowerCase()
  }

  static formats (domNode) {
    return domNode.getAttribute('id')
  }

  format (name, value) {
    if (name !== this.statics.blotName || !value) return super.format(name, value)
    value = this.constructor.sanitize(value)
    this.domNode.setAttribute('id', value)
  }
}

Anchor.blotName = anchor.blotName
Anchor.tagName = anchor.tagName

Quill.register(Anchor)

/* Customize Link */
class MyLink extends Link {
  static create (value) {
    const node = super.create(value)
    value = this.sanitize(value)
    node.setAttribute('href', value)

    // relative urls wont have target blank
    const urlPattern = /^((http|https|ftp):\/\/)/
    if (!urlPattern.test(value)) {
      node.removeAttribute('target')
    }

    // url starting with the front-end base url wont have target blank
    if (window[process.env.VUE_APP_NAME].STORE.form.baseUrl) {
      const url = new URL(window[process.env.VUE_APP_NAME].STORE.form.baseUrl)
      if (value.startsWith(url.origin)) {
        node.removeAttribute('target')
      }
    }

    return node
  }

  format (name, value) {
    super.format(name, value)

    if (name !== this.statics.blotName || !value) {
      return
    }

    // relative urls wont have target blank
    const urlPattern = /^((http|https|ftp):\/\/)/
    if (!urlPattern.test(value)) {
      this.domNode.removeAttribute('target')
      return
    }

    // url starting with the front-end base url wont have target blank
    if (window[process.env.VUE_APP_NAME].STORE.form.baseUrl) {
      if (value.startsWith(window[process.env.VUE_APP_NAME].STORE.form.baseUrl)) {
        this.domNode.removeAttribute('target')
        return
      }
    }

    this.domNode.setAttribute('target', '_blank')
  }
}

Quill.register(MyLink)

/* Custom Icons */
function getIcon (shape) {
  return '<span class="icon icon--wysiwyg_' + shape + '" aria-hidden="true"><svg><title>' + shape + '</title><use xlink:href="#icon--wysiwyg_' + shape + '"></use></svg></span>'
}

const icons = Quill.import('ui/icons') // custom icons
icons.bold = getIcon('bold')
icons.italic = getIcon('italic')
icons.anchor = getIcon('anchor')
icons.link = getIcon('link')
icons.header['1'] = getIcon('header')
icons.header['2'] = getIcon('header-2')
icons.header['3'] = getIcon('header-3')
icons.header['4'] = getIcon('header-4')
icons.header['5'] = getIcon('header-5')
icons.header['6'] = getIcon('header-6')
icons.divider = getIcon('hr')

/*
* ClipBoard manager
* Use formats to authorize what user can paste
* Formats are based on toolbar configuration
*/

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
  'video',
  'divider'
]

function getQuillFormats (toolbarEls) {
  const formats = [SoftLineBreakBlot.blotName, anchor.blotName] // Allow linebreak and anchor

  function addFormat (format) {
    if (formats.indexOf(format) > -1 || QuillDefaultFormats.indexOf(format) === -1) return
    formats.push(format)
  }

  toolbarEls.forEach((el) => {
    if (typeof el === 'object') {
      for (const property in el) {
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
  Quill,
  lineBreak: {
    handle: lineBreakHandle,
    clipboard: [SoftLineBreakBlot.tagName, lineBreakMatcher]
  },
  getFormats: getQuillFormats
}
