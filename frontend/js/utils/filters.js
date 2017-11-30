import dateFormat from 'date-fns/format'

const filters = {
  slugify: function (value) {
    const a = 'àáäâèéëêìíïîòóöôùúüûñçßÿœæŕśńṕẃǵǹḿǘẍźḧ·/_,:;' + 'ąàáäâãåæćęęèéëêìíïîłńòóöôõøśùúüûñçżź'
    const b = 'aaaaeeeeiiiioooouuuuncsyoarsnpwgnmuxzh------' + 'aaaaaaaaceeeeeeiiiilnoooooosuuuunczz'
    const p = new RegExp(a.split('').join('|'), 'g')

    return value.toString().toLowerCase().trim()
      .replace(/\s+/g, '-')           // Replace spaces with -
      .replace(p, c =>
          b.charAt(a.indexOf(c)))     // Replace special chars
      .replace(/&/g, '-and-')         // Replace & with 'and'
      .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
      .replace(/\-\-+/g, '-')         // Replace multiple - with single -
  },
  prettierUrl: function (value) {
    return value.replace(/^\/\/|^.*?:(\/\/)?/, '')
  },
  uppercase: function (value) {
    return (value || value === 0)
      ? value.toString().toUpperCase()
      : ''
  },
  lowercase: function (value) {
    return (value || value === 0)
      ? value.toString().toLowerCase()
      : ''
  },
  capitalize: function (value) {
    if (!value) return ''

    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
  },
  formatDate: function (value) {
    if (!value) return ''

    return dateFormat(value, 'MMM, DD, YYYY hh:mm A')
  },
  formatDatatableDate: function (value) {
    const datepickerFormat = 'MMM DD, YYYY'
    if (!value) value = new Date()
    return dateFormat(value, datepickerFormat)
  },
  formatCalendarDate: function (value) {
    const datepickerFormat = 'MMM, DD, YYYY hh:mm A'
    if (!value) value = new Date()
    return dateFormat(value, datepickerFormat)
  }
}

export default filters
