import dateFormat from 'date-fns/format'

import { locales, getCurrentLocale, getTimeFormatForCurrentLocale } from '@/utils/locale'

function dateFormatLocale (date, format) {
  const locale = locales[getCurrentLocale()]

  return dateFormat(date, format, {
    locale: locale !== undefined && locale.hasOwnProperty('date-fns') ? locale['date-fns'] : require('date-fns/locale/en')
  })
}

function sanitizeTitle (title) {
  var slug = ''
  // Change to lower case
  var titleLower = title.toLowerCase()
  // Letter 'e'
  slug = titleLower.replace(/e|é|è|ẽ|ẻ|ẹ|ê|ế|ề|ễ|ể|ệ/gi, 'e')
  // Letter 'a'
  slug = slug.replace(/a|á|à|ã|ả|ạ|ă|ắ|ằ|ẵ|ẳ|ặ|â|ấ|ầ|ẫ|ẩ|ậ/gi, 'a')
  // Letter 'o'
  slug = slug.replace(/o|ó|ò|õ|ỏ|ọ|ô|ố|ồ|ỗ|ổ|ộ|ơ|ớ|ờ|ỡ|ở|ợ/gi, 'o')
  // Letter 'u'
  slug = slug.replace(/u|ú|ù|ũ|ủ|ụ|ư|ứ|ừ|ữ|ử|ự/gi, 'u')
  slug = slug.replace(/ì|í|ị|ỉ|ĩ/g, 'i')
  slug = slug.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
  slug = slug.replace(/đ/g, 'd')
  // Some system encode vietnamese combining accent as individual utf-8 characters
  slug = slug.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, '') // Huyền sắc hỏi ngã nặng
  slug = slug.replace(/\u02C6|\u0306|\u031B/g, '') // Â, Ê, Ă, Ơ, Ư
  // Letter 'd'
  // Trim the last whitespace
  slug = slug.replace(/\s*$/g, '')
  // Change whitespace to '-'
  slug = slug.replace(/\s+/g, '-')

  return slug
}

const filters = {
  slugify: function (value) {
    const charMap = { ',': '-', '/': '-', ':': '-', ';': '-', _: '-', '©': '(c)', '·': '-', ß: 'ss', à: 'a', á: 'a', â: 'a', ã: 'a', ä: 'a', å: 'a', æ: 'ae', ç: 'c', è: 'e', é: 'e', ê: 'e', ë: 'e', ì: 'i', í: 'i', î: 'i', ï: 'i', ð: 'd', ñ: 'n', ò: 'o', ó: 'o', ô: 'o', õ: 'o', ö: 'o', ø: 'o', ù: 'u', ú: 'u', û: 'u', ü: 'u', ý: 'y', þ: 'th', ÿ: 'y', ā: 'a', ă: 'a', ą: 'a', ć: 'c', č: 'c', ď: 'd', ē: 'e', ę: 'e', ě: 'e', ğ: 'g', ģ: 'g', ī: 'i', ı: 'i', ķ: 'k', ļ: 'l', ł: 'l', ń: 'n', ņ: 'n', ň: 'n', ő: 'o', œ: 'oe', ŕ: 'r', ř: 'r', ś: 's', ş: 's', š: 's', ť: 't', ū: 'u', ů: 'u', ű: 'u', ź: 'z', ż: 'z', ž: 'z', ǘ: 'u', ǵ: 'g', ǹ: 'n', ș: 's', ț: 't', ΐ: 'i', ά: 'a', έ: 'e', ή: 'h', ί: 'i', ΰ: 'y', α: 'a', β: 'b', γ: 'g', δ: 'd', ε: 'e', ζ: 'z', η: 'h', θ: '8', ι: 'i', κ: 'k', λ: 'l', μ: 'm', ν: 'n', ξ: '3', ο: 'o', π: 'p', ρ: 'r', ς: 's', σ: 's', τ: 't', υ: 'y', φ: 'f', χ: 'x', ψ: 'ps', ω: 'w', ϊ: 'i', ϋ: 'y', ό: 'o', ύ: 'y', ώ: 'w', а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ж: 'zh', з: 'z', и: 'i', й: 'j', к: 'k', л: 'l', м: 'm', н: 'n', о: 'o', п: 'p', р: 'r', с: 's', т: 't', у: 'u', ф: 'f', х: 'h', ц: 'c', ч: 'ch', ш: 'sh', щ: 'sh', ъ: '', ы: 'y', ь: '', э: 'e', ю: 'yu', я: 'ya', ё: 'yo', є: 'ye', і: 'i', ї: 'yi', ґ: 'g', ḧ: 'h', ḿ: 'm', ṕ: 'p', ẃ: 'w', ẍ: 'x', ә: 'a', ғ: 'g', қ: 'q', ң: 'n', ө: 'o', ұ: 'u' }
    const p = new RegExp(Object.keys(charMap).join('|'), 'g')
    return sanitizeTitle(value).toString().toLowerCase().trim()
      .replace(/\s+/g, '-') // Replace spaces with -
      .replace(p, c => charMap[c]) // Replace special chars
      .replace(/&/g, '-and-') // Replace & with 'and'
      .replace(/[^\w-]+/g, '-') // Replace all non-word chars with -
      .replace(/--+/g, '-') // Replace multiple - with single -
      .replace(/(^-+)|(-+$)/, '') // Remove leading and traling -
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

    return dateFormatLocale(value, 'MMM, DD, YYYY, ' + getTimeFormatForCurrentLocale())
  },
  formatDateWithFormat: function (value, format) {
    if (!value) value = new Date()
    return dateFormatLocale(value, format)
  },
  formatDatatableDate: function (value) {
    const datepickerFormat = 'MMM DD, YYYY'
    if (!value) value = new Date()
    return dateFormatLocale(value, datepickerFormat)
  },
  formatCalendarDate: function (value) {
    const datepickerFormat = 'MMM, DD, YYYY, ' + getTimeFormatForCurrentLocale()
    if (!value) value = new Date()
    return dateFormatLocale(value, datepickerFormat)
  }
}

export default filters
