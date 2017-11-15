import Vue from 'vue'
import Vuex from 'vuex'
import form from './modules/form'
import publication from './modules/publication'
import datatable from './modules/datatable'
import content from './modules/content'
import language from './modules/language'
import mediaLibrary from './modules/media-library'
import browser from './modules/browser'
import revision from './modules/revision'
import buckets from './modules/buckets'
import notif from './modules/notif'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  modules: {
    form,
    datatable,
    publication,
    content,
    language,
    mediaLibrary,
    browser,
    revision,
    buckets,
    notif
  },
  strict: debug
})
