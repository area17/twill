import { Arabic } from 'flatpickr/dist/l10n/ar.js'
import { Bosnian } from 'flatpickr/dist/l10n/bs.js'
import { Czech } from 'flatpickr/dist/l10n/cs.js'
import { Dutch } from 'flatpickr/dist/l10n/nl.js'
import { French } from 'flatpickr/dist/l10n/fr.js'
import { German } from 'flatpickr/dist/l10n/de.js'
import { Italian } from 'flatpickr/dist/l10n/it.js'
import { Mandarin } from 'flatpickr/dist/l10n/zh.js'
import { Norwegian } from 'flatpickr/dist/l10n/no.js'
import { Polish } from 'flatpickr/dist/l10n/pl.js'
import { Portuguese } from 'flatpickr/dist/l10n/pt.js'
import { Russian } from 'flatpickr/dist/l10n/ru.js'
import { Slovenian } from 'flatpickr/dist/l10n/sl.js'
import { Spanish } from 'flatpickr/dist/l10n/es.js'
import { Turkish } from 'flatpickr/dist/l10n/tr.js'
import { Ukrainian } from 'flatpickr/dist/l10n/uk.js'

export const locales = {
  en: {
    'date-fns': require('date-fns/locale/en')
  },
  ar: {
    'date-fns': require('date-fns/locale/ar'),
    flatpickr: Arabic
  },
  bs: {
    'date-fns': require('date-fns/locale/en'), // TODO: update date-fns to add support for bs
    flatpickr: Bosnian
  },
  cs: {
    'date-fns': require('date-fns/locale/cs'),
    flatpickr: Czech
  },
  de: {
    'date-fns': require('date-fns/locale/de'),
    flatpickr: German
  },
  es: {
    'date-fns': require('date-fns/locale/es'),
    flatpickr: Spanish
  },
  fr: {
    'date-fns': require('date-fns/locale/fr'),
    flatpickr: French
  },
  it: {
    'date-fns': require('date-fns/locale/it'),
    flatpickr: Italian
  },
  nl: {
    'date-fns': require('date-fns/locale/nl'),
    flatpickr: Dutch
  },
  no: {
    'date-fns': require('date-fns/locale/en'), // TODO: update date-fns to add support for no
    flatpickr: Norwegian
  },
  pl: {
    'date-fns': require('date-fns/locale/pl'),
    flatpickr: Polish
  },
  pt: {
    'date-fns': require('date-fns/locale/pt'),
    flatpickr: Portuguese
  },
  ru: {
    'date-fns': require('date-fns/locale/ru'),
    flatpickr: Russian
  },
  sl: {
    'date-fns': require('date-fns/locale/sl'),
    flatpickr: Slovenian
  },
  tr: {
    'date-fns': require('date-fns/locale/tr'),
    flatpickr: Turkish
  },
  uk: {
    'date-fns': require('date-fns/locale/en'), // TODO: update date-fns to add support for uk
    flatpickr: Ukrainian
  },
  'zh-Hans': {
    'date-fns': require('date-fns/locale/zh_cn'),
    flatpickr: Mandarin
  }
}

export function getCurrentLocale () {
  return window[process.env.VUE_APP_NAME].twillLocalization.locale
}

export function isCurrentLocale24HrFormatted () {
  return new Intl.DateTimeFormat(getCurrentLocale(), {
    hour: 'numeric'
  }).formatToParts(
    new Date(2020, 0, 1, 13)
  ).find(part => part.type === 'hour').value.length === 2
}

export function getTimeFormatForCurrentLocale (force24h = false) {
  if (isCurrentLocale24HrFormatted() || force24h) {
    return 'HH:mm'
  } else {
    return 'hh:mm A'
  }
}
