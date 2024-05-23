import { createStore } from 'vuex'

import mediaLibrary from './modules/media-library'
import notification from './modules/notification'

const debug = process.env.NODE_ENV !== 'production'

export default createStore({
  modules: {
    notification,
    mediaLibrary
  },
  strict: debug
})
