import Vue from 'vue'
import { BROWSER } from '../mutations'

const state = {
  connector: null,
  title: 'Attach related resources',
  endpoint: '',
  endpointName: '',
  endpoints: [],
  max: 0,
  selected: window.STORE.browser.selected || {}
}

// getters
const getters = {
  selectedItemsByIds: state => {
    let arrayOfIds = []

    for (let name in state.selected) {
      arrayOfIds[name] = state.selected[name].map((item) => `${item.endpointType}_${item.id}`)
    }

    return arrayOfIds
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
  }
}

export default {
  state,
  getters,
  mutations
}
