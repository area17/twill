import Vue from 'vue'
import * as types from '../mutation-types'

const state = {
  connector: null,
  title: 'Attach related resources',
  endpoint: '',
  max: 0,
  selected: {}
}

// getters
const getters = {
  selectedItemsByIds: state => {
    let arrayOfIds = []

    for (var name in state.selected) {
      const ids = state.selected[name].map((item) => item.id)
      arrayOfIds[name] = ids
    }

    return arrayOfIds
  }
}

const mutations = {
  [types.SAVE_ITEMS] (state, items) {
    if (state.connector) {
      if (state.selected[state.connector] && state.selected[state.connector].length) {
        items.forEach(function (item) {
          state.selected[state.connector].push(item)
        })
      } else {
        const newItems = {}
        newItems[state.connector] = items
        state.selected = Object.assign({}, state.selected, newItems)
      }
    }
  },
  [types.DESTROY_ITEMS] (state, itemToDestroy) {
    if (state.selected[itemToDestroy.name]) {
      Vue.delete(state.selected, itemToDestroy.name)
    }
  },
  [types.DESTROY_ITEM] (state, itemToDestroy) {
    if (state.selected[itemToDestroy.name]) {
      state.selected[itemToDestroy.name].splice(itemToDestroy.index, 1)

      if (state.selected[itemToDestroy.name].length === 0) Vue.delete(state.selected, itemToDestroy.name)

      state.connector = null
    }
  },
  [types.REORDER_ITEMS] (state, newValues) {
    const newItems = {}
    newItems[newValues.name] = newValues.items
    state.selected = Object.assign({}, state.selected, newItems)
  },
  [types.UPDATE_BROWSER_MAX] (state, newValue) {
    state.max = Math.max(0, newValue)
  },
  [types.UPDATE_BROWSER_CONNECTOR] (state, newValue) {
    if (newValue && newValue !== '') state.connector = newValue
  },
  [types.UPDATE_BROWSER_TITLE] (state, newValue) {
    if (newValue && newValue !== '') state.title = newValue
  },
  [types.DESTROY_BROWSER_CONNECTOR] (state) {
    state.connector = null
  },
  [types.UPDATE_BROWSER_ENDPOINT] (state, newValue) {
    if (newValue && newValue !== '') state.endpoint = newValue
  },
  [types.DESTROY_BROWSER_ENDPOINT] (state) {
    state.endpoint = ''
  }
}

export default {
  state,
  getters,
  mutations
}
