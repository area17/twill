import { Mandarin } from 'flatpickr/dist/l10n/zh.js'
import { Russian } from 'flatpickr/dist/l10n/ru.js'
import { French } from 'flatpickr/dist/l10n/fr.js'
import { Polish } from 'flatpickr/dist/l10n/pl.js'

export const locales = {
  en: {
    'date-fns': require('date-fns/locale/en')
  },
  'zh-Hans': {
    'date-fns': require('date-fns/locale/zh_cn'),
    flatpickr: Mandarin
  },
  ru: {
    'date-fns': require('date-fns/locale/ru'),
    flatpickr: Russian
  },
  fr: {
    'date-fns': require('date-fns/locale/fr'),
    flatpickr: French
  },
  pl: {
    'date-fns': require('date-fns/locale/pl'),
    flatpickr: Polish
  }
}

const twelveHourFormatLocales = ['en']

export function getCurrentLocale () {
  return window[process.env.VUE_APP_NAME].twillLocalization.locale
}

export function isCurrentLocale24HrFormatted () {
  return !twelveHourFormatLocales.includes(getCurrentLocale())
}

export function getTimeFormatForCurrentLocale () {
  if (isCurrentLocale24HrFormatted()) {
    return 'HH:mm'
  } else {
    return 'hh:mm A'
  }
}
