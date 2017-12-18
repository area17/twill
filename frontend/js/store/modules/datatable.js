import api from '../api/datatable'
import * as types from '../mutation-types'
import { setStorage } from '@/utils/localeStorage.js'

const state = {
  baseUrl: window.STORE.datatable.baseUrl || '',
  data: window.STORE.datatable.data || [],
  columns: window.STORE.datatable.columns || [],
  filter: window.STORE.datatable.filter || {},
  filtersNav: window.STORE.datatable.navigation || [],
  page: window.STORE.datatable.page || 1,
  maxPage: window.STORE.datatable.maxPage || 1,
  defaultMaxPage: window.STORE.datatable.defaultMaxPage || 1,
  offset: window.STORE.datatable.offset || 60,
  defaultOffset: window.STORE.datatable.defaultOffset || 60,
  sortKey: window.STORE.datatable.sortKey || '',
  sortDir: window.STORE.datatable.sortDir || 'asc',
  bulk: [],
  localStorageKey: window.STORE.datatable.localStorageKey || location.pathname,
  loading: false
}

// getters
const getters = {
  dataIds: state => {
    return state.data.map(item => item.id)
  },
  hideableColumns: state => {
    return state.columns.filter(column => column.optional)
  },
  visibleColumns: state => {
    return state.columns.filter(column => column.visible)
  },
  visibleColumnsNames: state => {
    let onlyActiveColumnsNames = []

    if (state.columns.length) {
      state.columns.forEach(function (column) {
        if (column.visible) onlyActiveColumnsNames.push(column.name)
      })
    }

    return onlyActiveColumnsNames
  }
}

const mutations = {
  [types.UPDATE_DATATABLE_DATA] (state, data) {
    // Each time the data is changing, we reset the bulk ids
    state.bulk = []

    state.data = data
  },
  [types.UPDATE_DATATABLE_BULK] (state, id) {
    if (state.bulk.indexOf(id) > -1) {
      state.bulk = state.bulk.filter(function (item) {
        return item !== id
      })
    } else {
      state.bulk.push(id)
    }
  },
  [types.REPLACE_DATATABLE_BULK] (state, ids) {
    state.bulk = ids
  },
  [types.ADD_DATATABLE_COLUMN] (state, column) {
    state.columns.splice(column.index, 0, column.data)
  },
  [types.REMOVE_DATATABLE_COLUMN] (state, columnName) {
    state.columns.forEach(function (column, index) {
      if (column.name === columnName) state.columns.splice(index, 1)
    })
  },
  [types.UPDATE_DATATABLE_FILTER] (state, filter) {
    state.filter = Object.assign({}, state.filter, filter)
  },
  [types.CLEAR_DATATABLE_FILTER] (state) {
    state.filter = {search: ''}
  },
  [types.UPDATE_DATATABLE_FILTER_STATUS] (state, slug) {
    state.filter.status = slug
  },
  [types.UPDATE_DATATABLE_OFFSET] (state, offsetNumber) {
    state.offset = offsetNumber
    setStorage(state.localStorageKey + '_page-offset', state.offset)
  },
  [types.UPDATE_DATATABLE_PAGE] (state, pageNumber) {
    state.page = pageNumber
  },
  [types.UPDATE_DATATABLE_MAXPAGE] (state, maxPage) {
    if (state.page > maxPage) state.page = maxPage
    state.maxPage = maxPage
  },
  [types.UPDATE_DATATABLE_VISIBLITY] (state, columnNames) {
    setStorage(state.localStorageKey + '_columns-visible', JSON.stringify(columnNames))
    state.columns.forEach(function (column) {
      for (let i = 0; i < columnNames.length; i++) {
        if (columnNames[i] === column.name) {
          column.visible = true

          break
        }

        column.visible = false
      }
    })
  },
  [types.UPDATE_DATATABLE_SORT] (state, column) {
    const defaultSortDirection = 'asc'

    if (state.sortKey === column.name) {
      state.sortDir = state.sortDir === defaultSortDirection ? 'desc' : defaultSortDirection
    } else {
      state.sortDir = defaultSortDirection
    }

    state.sortKey = column.name
  },
  [types.UPDATE_DATATABLE_NAV] (state, navigation) {
    navigation.forEach(function (navItem) {
      state.filtersNav.forEach(function (filterItem) {
        if (filterItem.name === navItem.name) filterItem.number = navItem.number
      })
    })
  },
  [types.PUBLISH_DATATABLE] (state, data) {
    const id = data.id
    const value = data.value

    function updateState (index) {
      if (index >= 0) {
        if (value === 'toggle') state.data[index].published = !state.data[index].published
        else state.data[index].published = value
      }
    }

    function getIndex (id) {
      return state.data.findIndex(function (item, index) { return (item.id === id) })
    }

    // bulk
    if (Array.isArray(id)) {
      id.forEach(function (itemId) {
        const index = getIndex(itemId)
        updateState(index)
      })

      state.bulk = []
    } else {
      const index = getIndex(id)
      updateState(index)
    }
  },
  [types.FEATURE_DATATABLE] (state, data) {
    const id = data.id
    const value = data.value

    function updateState (index) {
      if (index >= 0) {
        if (value === 'toggle') state.data[index].featured = !state.data[index].featured
        else state.data[index].featured = value
      }
    }

    function getIndex (id) {
      return state.data.findIndex(function (item, index) { return (item.id === id) })
    }

    // bulk
    if (Array.isArray(id)) {
      id.forEach(function (itemId) {
        const index = getIndex(itemId)
        updateState(index)
      })

      state.bulk = []
    } else {
      const index = getIndex(id)
      updateState(index)
    }
  },
  [types.UPDATE_DATATABLE_LOADING] (state, loading) {
    state.loading = !state.loading
  }
}

const actions = {
  getDatatableDatas ({ commit, state, getters }) {
    if (!state.loading) {
      commit(types.UPDATE_DATATABLE_LOADING, true)
      const params = {
        sortKey: state.sortKey,
        sortDir: state.sortDir,
        page: state.page,
        offset: state.offset,
        columns: getters.visibleColumnsNames,
        filter: state.filter
      }

      api.get(params, function (resp) {
        commit(types.UPDATE_DATATABLE_DATA, resp.data)
        commit(types.UPDATE_DATATABLE_MAXPAGE, resp.maxPage)
        commit(types.UPDATE_DATATABLE_NAV, resp.nav)
        commit(types.UPDATE_DATATABLE_LOADING, false)
      })
    }
  },
  setDatatableDatas ({ commit, state, dispatch }, data) {
    // TBD: Maybe, we can keep and reset the old state if we have and error
    // reorder in store first
    commit(types.UPDATE_DATATABLE_DATA, data)

    const ids = data.map((row) => row.id)

    api.reorder(ids, function (resp) {
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
    })
  },
  togglePublishedData ({ commit, state, dispatch }, row) {
    api.togglePublished(row, function (resp) {
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
      dispatch('getDatatableDatas')
    })
  },
  deleteData ({ commit, state, dispatch }, row) {
    api.delete(row, function (resp) {
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
      dispatch('getDatatableDatas')
    })
  },
  restoreData ({ commit, state, dispatch }, row) {
    api.restore(row, function (resp) {
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
      dispatch('getDatatableDatas')
    })
  },
  bulkPublishData ({ commit, state, dispatch }, payload) {
    api.bulkPublish(
      {
        ids: state.bulk.join(),
        toPublish: payload.toPublish
      },
      function (resp) {
        commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
        dispatch('getDatatableDatas')
      }
    )
  },
  toggleFeaturedData ({ commit, state }, row) {
    api.toggleFeatured(row, resp => {
      commit(types.FEATURE_DATATABLE, {
        id: row.id,
        value: 'toggle'
      })
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
    })
  },
  bulkFeatureData ({ commit, state }, payload) {
    api.bulkFeature(
      {
        ids: state.bulk.join(),
        toFeature: payload.toFeature
      },
      function (resp) {
        commit(types.FEATURE_DATATABLE, {
          id: state.bulk,
          value: true
        })
        commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
      }
    )
  },
  bulkDeleteData ({ commit, state, dispatch }) {
    api.bulkDelete(state.bulk.join(), function (resp) {
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
      dispatch('getDatatableDatas')
    })
  },
  bulkRestoreData ({ commit, state, dispatch }) {
    api.bulkRestore(state.bulk.join(), function (resp) {
      commit('setNotification', { message: resp.data.message, variant: resp.data.variant })
      dispatch('getDatatableDatas')
    })
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
