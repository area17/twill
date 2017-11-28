import Vue from 'vue'
import Vuex from 'vuex'

import mediaLibrary from './modules/media-library'
import notif from './modules/notif'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  modules: {
    notif,
    mediaLibrary
  },
  strict: debug
})
