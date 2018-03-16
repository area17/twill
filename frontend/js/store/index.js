import Vue from 'vue'
import Vuex from 'vuex'

import mediaLibrary from './modules/media-library'
import notification from './modules/notification'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  modules: {
    notification,
    mediaLibrary
  },
  strict: debug
})
