import dateFormat from 'date-fns/format'

const filters = {
  slugify: function (value) {
    const a = 'àáäâèéëêìíïîòóöôùúüûñçßÿœæŕśńṕẃǵǹḿǘẍźḧ·/_,:;' + 'ąàáäâãåæćęęèéëêìíïîłńòóöôõøśùúüûñçżź'
    const b = 'aaaaeeeeiiiioooouuuuncsyoarsnpwgnmuxzh------' + 'aaaaaaaaceeeeeeiiiilnoooooosuuuunczz'
    const ea = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŐØÙÚÛÜŰÝÞßàáâãäåæçèéêëìíîïðñòóôõöőøùúûüűýþÿ©ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩΆΈΊΌΎΉΏΪΫαβγδεζηθικλμνξοπρστυφχψωάέίόύήώςϊΰϋΐŞİĞşığАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюяЄІЇҐєіїґČĎĚŇŘŠŤŮŽčďěňřšťůžĄĆĘŁŃŚŹŻąćęłńśźżĀĒĢĪĶĻŅŪāēģīķļņū'
    const eb = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'o', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'Y', 'TH', 'ss', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'y', 'th', 'y', '(c)', 'A', 'B', 'G', 'D', 'E', 'Z', 'H', '8', 'I', 'K', 'L', 'M', 'N', '3', 'O', 'P', 'R', 'S', 'T', 'Y', 'F', 'X', 'PS', 'W', 'A', 'E', 'I', 'O', 'Y', 'H', 'W', 'I', 'Y', 'a', 'b', 'g', 'd', 'e', 'z', 'h', '8', 'i', 'k', 'l', 'm', 'n', '3', 'o', 'p', 'r', 's', 't', 'y', 'f', 'x', 'ps', 'w', 'a', 'e', 'i', 'o', 'y', 'h', 'w', 's', 'i', 'y', 'y', 'i', 'S', 'I', 'G', 's', 'i', 'g', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sh', '', 'Y', '', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sh', '', 'y', '', 'e', 'yu', 'ya', 'Ye', 'I', 'Yi', 'G', 'ye', 'i', 'yi', 'g', 'C', 'D', 'E', 'N', 'R', 'S', 'T', 'U', 'Z', 'c', 'd', 'e', 'n', 'r', 's', 't', 'u', 'z', 'A', 'C', 'e', 'L', 'N', 'S', 'Z', 'Z', 'a', 'c', 'e', 'l', 'n', 's', 'z', 'z', 'A', 'E', 'G', 'i', 'k', 'L', 'N', 'u', 'a', 'e', 'g', 'i', 'k', 'l', 'n', 'u']
    const p = new RegExp(a.split('').join('|'), 'g')
    const ep = new RegExp(ea.split('').join('|'), 'g')
    return value.toString().toLowerCase().trim()
      .replace(/\s+/g, '-') // Replace spaces with -
      .replace(p, c =>
        b.charAt(a.indexOf(c))) // Replace special chars
      .replace(ep, c =>
        eb[ea.indexOf(c)]) // Replace special chars extended
      .replace(/&/g, '-and-') // Replace & with 'and'
      .replace(/[^\w-]+/g, '') // Remove all non-word chars
      .replace(/--+/g, '-') // Replace multiple - with single -
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

    return dateFormat(value, 'MMM, DD, YYYY, hh:mm A')
  },
  formatDatatableDate: function (value) {
    const datepickerFormat = 'MMM DD, YYYY'
    if (!value) value = new Date()
    return dateFormat(value, datepickerFormat)
  },
  formatCalendarDate: function (value) {
    const datepickerFormat = 'MMM, DD, YYYY, hh:mm A'
    if (!value) value = new Date()
    return dateFormat(value, datepickerFormat)
  }
}

export default filters
