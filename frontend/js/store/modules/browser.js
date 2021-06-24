import Vue from 'vue'
import { BROWSER } from '../mutations'
import ACTIONS from '@/store/actions'

const state = {
  connector: null,
  title: 'Attach related resources',
  note: '',
  endpoint: '',
  endpointName: '',
  endpoints: [],
  max: 0,
  selected: window[process.env.VUE_APP_NAME].STORE.browser.selected || {}
}

// getters
const getters = {
  selectedItemsByIds: state => {
    const arrayOfIds = []

    for (const name in state.selected) {
      arrayOfIds[name] = state.selected[name].map((item) => `${item.endpointType}_${item.id}`)
    }

    return arrayOfIds
  },
  browsersByBlockId: (state) => (id) => {
    const ids = Object.keys(state.selected).filter(key => key.startsWith(`blocks[${id}]`))
    const browsers = {}
    ids.forEach(id => (browsers[id] = state.selected[id]))
    return browsers
  }
}

const mutations = {
  [BROWSER.SAVE_ITEMS] (state, items) {
    if (state.connector) {
      if (state.selected[state.connector] && state.selected[state.connector].length) {
        // items.forEach(function (item) {
        //   state.selected[state.connector].push(item)
        // })
        state.selected[state.connector] = items
      } else {
        const newItems = {}
        newItems[state.connector] = items
        state.selected = Object.assign({}, state.selected, newItems)
      }
    }
  },
  [BROWSER.DESTROY_ITEMS] (state, itemToDestroy) {
    if (state.selected[itemToDestroy.name]) {
      Vue.delete(state.selected, itemToDestroy.name)
    }
  },
  [BROWSER.DESTROY_ITEM] (state, itemToDestroy) {
    if (state.selected[itemToDestroy.name]) {
      state.selected[itemToDestroy.name].splice(itemToDestroy.index, 1)

      if (state.selected[itemToDestroy.name].length === 0) Vue.delete(state.selected, itemToDestroy.name)

      state.connector = null
    }
  },
  [BROWSER.REORDER_ITEMS] (state, newValues) {
    const newItems = {}
    newItems[newValues.name] = newValues.items
    state.selected = Object.assign({}, state.selected, newItems)
  },
  [BROWSER.UPDATE_BROWSER_MAX] (state, newValue) {
    state.max = Math.max(0, newValue)
  },
  [BROWSER.UPDATE_BROWSER_CONNECTOR] (state, newValue) {
    if (newValue && newValue !== '') state.connector = newValue
  },
  [BROWSER.UPDATE_BROWSER_TITLE] (state, newValue) {
    if (newValue && newValue !== '') state.title = newValue
  },
  [BROWSER.UPDATE_BROWSER_NOTE] (state, newValue) {
    state.note = newValue
  },
  [BROWSER.DESTROY_BROWSER_CONNECTOR] (state) {
    state.connector = null
  },
  [BROWSER.UPDATE_BROWSER_ENDPOINT] (state, newValue) {
    if (newValue && newValue !== '') {
      state.endpoint = newValue.value
      state.endpointName = newValue.label || ''
    }
  },
  [BROWSER.DESTROY_BROWSER_ENDPOINT] (state) {
    state.endpoint = ''
    state.endpointName = ''
  },
  [BROWSER.UPDATE_BROWSER_ENDPOINTS] (state, endpoints) {
    if (!endpoints && !endpoints.length > 0) return
    state.endpoints = endpoints
    state.endpoint = endpoints[0].value
    state.endpointName = endpoints[0].label
  },
  [BROWSER.DESTROY_BROWSER_ENDPOINTS] (state) {
    state.endpoints = []
  },
  [BROWSER.ADD_BROWSERS] (state, { browsers }) {
    state.selected = Object.assign({}, state.selected, browsers)
  }
}

const actions = {
  async [ACTIONS.DUPLICATE_BLOCK] ({ commit, getters }, { block, id }) {
    // copy browsers and update with the provided id
    const browsers = { ...getters.browsersByBlockId(block.id) }
    const browserIds = Object.keys(browsers)
    const duplicates = {}
    browserIds.forEach(browserId => (duplicates[browserId.replace(block.id, id)] = [...browsers[browserId]]))

    commit(BROWSER.ADD_BROWSERS, { browsers: duplicates })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
