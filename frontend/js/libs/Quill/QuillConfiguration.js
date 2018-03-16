import Quill from 'quill'
let Delta = Quill.import('delta')
let Break = Quill.import('blots/break')
let Embed = Quill.import('blots/embed')
let Link = Quill.import('formats/link')

/*
* Support for shift enter
* @see https://github.com/quilljs/quill/issues/252
* @see https://codepen.io/mackermedia/pen/gmNwZP
*/
const lineBreak = {
  blotName: 'break',
  tagName: 'BR'
}

class SmartBreak extends Break {
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

SmartBreak.blotName = lineBreak.blotName
SmartBreak.tagName = lineBreak.tagName

const lineBreakHandle = {
  key: 13,
  shiftKey: true,
  handler:
    function (range) {
      let currentLeaf = this.quill.getLeaf(range.index)[0]
      let nextLeaf = this.quill.getLeaf(range.index + 1)[0]

      this.quill.insertEmbed(range.index, lineBreak.blotName, true, 'user')

      // Insert a second break if:
      // At the end of the editor, OR next leaf has a different parent (<p>)
      if (nextLeaf === null || (currentLeaf.parent !== nextLeaf.parent)) {
        this.quill.insertEmbed(range.index, lineBreak.blotName, true, 'user')
      }

      // Now that we've inserted a line break, move the cursor forward
      this.quill.setSelection(range.index + 1, Quill.sources.SILENT)
    }
}

function lineBreakMatcher () {
  const newDelta = new Delta()
  newDelta.insert({'break': ''})
  return newDelta
}

Quill.register(SmartBreak)

/* Customize Link */
class MyLink extends Link {
  static create (value) {
    let node = super.create(value)
    value = this.sanitize(value)
    node.setAttribute('href', value)

    // relative urls wont have target blank
    const urlPattern = /^((http|https|ftp):\/\/)/
    if (!urlPattern.test(value)) {
      node.removeAttribute('target')
    }

    // url starting with the front-end base url wont have target blank
    if (window.STORE.form.baseUrl) {
      if (value.startsWith(window.STORE.form.baseUrl)) {
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
    if (window.STORE.form.baseUrl) {
      if (value.startsWith(window.STORE.form.baseUrl)) {
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
  return '<span class="icon icon--wysiwyg_' + shape + '" aria-hidden="true"><svg><title>' + shape + '</title><use xlink:href="#wysiwyg_' + shape + '"></use></svg></span>'
}

const icons = Quill.import('ui/icons') // custom icons
icons['bold'] = getIcon('bold')
icons['italic'] = getIcon('italic')
icons['underline'] = getIcon('underline')
icons['link'] = getIcon('link')
icons['header']['1'] = getIcon('header')
icons['header']['2'] = getIcon('header-2')
icons['header']['3'] = getIcon('header-3')
icons['header']['4'] = getIcon('header-4')
icons['header']['5'] = getIcon('header-5')
icons['header']['6'] = getIcon('header-6')

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
  'video'
]

function getQuillFormats (toolbarEls) {
  const formats = [lineBreak.blotName] // Allow linebreak

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
  Quill: Quill,
  lineBreak: {
    handle: lineBreakHandle,
    clipboard: [lineBreak.tagName, lineBreakMatcher]
  },
  getFormats: getQuillFormats
}
