import api from '../api/datatable'
import { DATATABLE, NOTIFICATION } from '../mutations'
import ACTIONS from '@/store/actions'

import { setStorage } from '@/utils/localeStorage.js'
/* NESTED functions */
const getObject = (container, id, callback) => {
  container.forEach((item) => {
    if (item.id === id) callback(item)
    if (item.children) getObject(item.children, id, callback)
  })
}

const deepRemoveFromObj = (items, keys = ['id', 'children'], deep = 'children') => {
  let deepItems = JSON.parse(JSON.stringify(items))
  deepItems.forEach((obj) => {
    for (const prop in obj) {
      if (!keys.includes(prop)) {
        delete obj[prop]
      }

      if (prop === deep) {
        obj[prop] = deepRemoveFromObj(obj[prop])
      }
    }
  })
  return deepItems
}

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
  localStorageKey: window.STORE.datatable.localStorageKey || window.location.pathname,
  loading: false,
  updateTracker: 0
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
  [DATATABLE.UPDATE_DATATABLE_DATA] (state, data) {
    // Each time the data is changing, we reset the bulk ids
    state.bulk = []

    state.data = data
  },
  [DATATABLE.UPDATE_DATATABLE_BULK] (state, id) {
    if (state.bulk.indexOf(id) > -1) {
      state.bulk = state.bulk.filter(function (item) {
        return item !== id
      })
    } else {
      state.bulk.push(id)
    }
  },
  [DATATABLE.REPLACE_DATATABLE_BULK] (state, ids) {
    state.bulk = ids
  },
  [DATATABLE.ADD_DATATABLE_COLUMN] (state, column) {
    state.columns.splice(column.index, 0, column.data)
  },
  [DATATABLE.REMOVE_DATATABLE_COLUMN] (state, columnName) {
    state.columns.forEach(function (column, index) {
      if (column.name === columnName) state.columns.splice(index, 1)
    })
  },
  [DATATABLE.UPDATE_DATATABLE_FILTER] (state, filter) {
    state.filter = Object.assign({}, state.filter, filter)
  },
  [DATATABLE.CLEAR_DATATABLE_FILTER] (state) {
    state.filter = Object.assign({}, {
      search: '',
      status: state.filter.status
    })
  },
  [DATATABLE.UPDATE_DATATABLE_FILTER_STATUS] (state, slug) {
    state.filter.status = slug
  },
  [DATATABLE.UPDATE_DATATABLE_OFFSET] (state, offsetNumber) {
    state.offset = offsetNumber
    setStorage(state.localStorageKey + '_page-offset', state.offset)
  },
  [DATATABLE.UPDATE_DATATABLE_PAGE] (state, pageNumber) {
    state.page = pageNumber
  },
  [DATATABLE.UPDATE_DATATABLE_MAXPAGE] (state, maxPage) {
    if (state.page > maxPage) state.page = maxPage
    state.maxPage = maxPage
  },
  [DATATABLE.UPDATE_DATATABLE_VISIBLITY] (state, columnNames) {
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
  [DATATABLE.UPDATE_DATATABLE_SORT] (state, column) {
    const defaultSortDirection = 'asc'

    if (state.sortKey === column.name) {
      state.sortDir = state.sortDir === defaultSortDirection ? 'desc' : defaultSortDirection
    } else {
      state.sortDir = defaultSortDirection
    }

    state.sortKey = column.name
  },
  [DATATABLE.UPDATE_DATATABLE_NAV] (state, navigation) {
    navigation.forEach(function (navItem) {
      state.filtersNav.forEach(function (filterItem) {
        if (filterItem.name === navItem.name) filterItem.number = navItem.number
      })
    })
  },
  [DATATABLE.PUBLISH_DATATABLE] (state, data) {
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
  [DATATABLE.FEATURE_DATATABLE] (state, data) {
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
  [DATATABLE.UPDATE_DATATABLE_LOADING] (state, loading) {
    state.loading = !state.loading
  },
  [DATATABLE.UPDATE_DATATABLE_NESTED] (state, data) {
    getObject(state.data, data.parentId, (item) => {
      item.children = data.val
    })
  },
  [DATATABLE.UPDATE_DATATABLE_TRACKER] (state, newTracker) {
    state.updateTracker = newTracker ? state.updateTracker + 1 : 0
  }
}

const actions = {
  [ACTIONS.GET_DATATABLE] ({ commit, state, getters }) {
    if (!state.loading) {
      commit(DATATABLE.UPDATE_DATATABLE_LOADING, true)
      const params = {
        sortKey: state.sortKey,
        sortDir: state.sortDir,
        page: state.page,
        offset: state.offset,
        columns: getters.visibleColumnsNames,
        filter: state.filter
      }

      api.get(params, function (resp) {
        commit(DATATABLE.UPDATE_DATATABLE_DATA, resp.data)
        commit(DATATABLE.UPDATE_DATATABLE_MAXPAGE, resp.maxPage)
        commit(DATATABLE.UPDATE_DATATABLE_NAV, resp.nav)
        commit(DATATABLE.UPDATE_DATATABLE_LOADING, false)
      })
    }
  },
  [ACTIONS.SET_DATATABLE_NESTED] ({commit, state, dispatch}) {
    // Get all ids and children ids if any
    const ids = deepRemoveFromObj(state.data)
    api.reorder(ids, function (resp) {
      commit(NOTIFICATION.SET_NOTIF, {message: resp.data.message, variant: resp.data.variant})
    })
  },
  [ACTIONS.SET_DATATABLE] ({commit, state, dispatch}) {
    const ids = state.data.map((row) => row.id)

    api.reorder(ids, function (resp) {
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
    })
  },
  [ACTIONS.TOGGLE_PUBLISH] ({ commit, state, dispatch }, row) {
    api.togglePublished(row, function (resp) {
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
      dispatch(ACTIONS.GET_DATATABLE)
    }, function (errorResp) {
      commit(NOTIFICATION.SET_NOTIF, { message: errorResp.data.error.message, variant: 'error' })
    })
  },
  [ACTIONS.DELETE_ROW] ({ commit, state, dispatch }, row) {
    api.delete(row, function (resp) {
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
      dispatch(ACTIONS.GET_DATATABLE)
    })
  },
  [ACTIONS.RESTORE_ROW] ({ commit, state, dispatch }, row) {
    api.restore(row, function (resp) {
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
      dispatch(ACTIONS.GET_DATATABLE)
    })
  },
  [ACTIONS.BULK_PUBLISH] ({ commit, state, dispatch }, payload) {
    api.bulkPublish(
      {
        ids: state.bulk.join(),
        toPublish: payload.toPublish
      },
      function (resp) {
        commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
        dispatch(ACTIONS.GET_DATATABLE)
      }
    )
  },
  [ACTIONS.TOGGLE_FEATURE] ({ commit, state }, row) {
    api.toggleFeatured(row, resp => {
      commit(DATATABLE.FEATURE_DATATABLE, {
        id: row.id,
        value: 'toggle'
      })
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
    })
  },
  [ACTIONS.BULK_FEATURE] ({ commit, state }, payload) {
    api.bulkFeature(
      {
        ids: state.bulk.join(),
        toFeature: payload.toFeature
      },
      function (resp) {
        commit(DATATABLE.FEATURE_DATATABLE, {
          id: state.bulk,
          value: true
        })
        commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
      }
    )
  },
  [ACTIONS.BULK_DELETE] ({ commit, state, dispatch }) {
    api.bulkDelete(state.bulk.join(), function (resp) {
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
      dispatch(ACTIONS.GET_DATATABLE)
    })
  },
  [ACTIONS.BULK_RESTORE] ({ commit, state, dispatch }) {
    api.bulkRestore(state.bulk.join(), function (resp) {
      commit(NOTIFICATION.SET_NOTIF, { message: resp.data.message, variant: resp.data.variant })
      dispatch(ACTIONS.GET_DATATABLE)
    })
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
