import api from '../api/datatable'
import * as types from '../mutation-types'

const state = {
  data: window.STORE.datatable.data || '[]',
  columns: window.STORE.datatable.columns || '[]',
  filter: {
    status: 'all'
  },
  filtersNav: [
    {
      name: 'All items',
      slug: 'all',
      number: 1253
    },
    {
      name: 'Mine',
      slug: 'mine',
      number: 3
    },
    {
      name: 'Published',
      slug: 'published',
      number: 6
    },
    {
      name: 'Draft',
      slug: 'draft',
      number: 1
    },
    {
      name: 'Trash',
      slug: 'trash',
      number: 1
    }
  ],
  page: 1,
  maxPage: 10,
  offset: 60,
  sortKey: 'name',
  sortDir: 'desc',
  bulk: []
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

    state.data = Object.assign([], state.data, data)
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
  [types.UPDATE_DATATABLE_FILTER] (state, filter) {
    state.filter = Object.assign({}, state.filter, filter)
  },
  [types.UPDATE_DATATABLE_FILTER_STATUS] (state, slug) {
    state.filter.status = slug
  },
  [types.UPDATE_DATATABLE_OFFSET] (state, offsetNumber) {
    state.offset = offsetNumber
    // todo : save offset settings in localStorage too
  },
  [types.UPDATE_DATATABLE_PAGE] (state, pageNumber) {
    state.page = pageNumber
  },
  [types.UPDATE_DATATABLE_MAXPAGE] (state, maxPage) {
    if (state.page > maxPage) state.page = maxPage
    state.maxPage = maxPage
  },
  [types.UPDATE_DATATABLE_VISIBLITY] (state, columnNames) {
    state.columns.forEach(function (column) {
      for (var i = 0; i < columnNames.length; i++) {
        if (columnNames[i] === column.name) {
          column.visible = true

          break
        }

        column.visible = false
      }
    })
  },
  [types.UPDATE_DATATABLE_SORT] (state, column) {
    const defaultSortDirection = 'desc'

    if (state.sortKey === column.name) {
      state.sortDir = state.sortDir === defaultSortDirection ? 'asc' : defaultSortDirection
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
  }
}

const actions = {
  getDatatableDatas ({ commit, state, getters }) {
    const params = {
      sortKey: state.sortKey,
      sortDir: state.sortDir,
      page: state.page,
      offset: state.offset,
      columns: getters.visibleColumnsNames,
      filter: state.filter
    }

    api.get(
      params,
      function (resp) {
        // success callback
        commit(types.UPDATE_DATATABLE_DATA, resp.data)
        commit(types.UPDATE_DATATABLE_MAXPAGE, resp.maxPage)
      }
    )
  },
  togglePublishedData ({ commit, state, dispatch }, id) {
    api.togglePublished(
      id,
      function (id, navigation) {
        // success callback

        if (state.filter.status !== 'all' && state.filter.status !== 'mine') {
          dispatch('getDatatableDatas') // we need to get the new datas from the api
        } else {
          // here we can simply update the store
          commit(types.PUBLISH_DATATABLE, {
            id: id,
            value: 'toggle'
          })
          commit(types.UPDATE_DATATABLE_NAV, navigation)
        }
      }
    )
  },
  bulkPublishData ({ commit, state, dispatch }, payload) {
    api.bulkPublish(
      {
        ids: state.bulk.join(),
        toPublish: payload.toPublish
      },
      function (ids, navigation) {
        // success callback

        if (state.filter.status !== 'all' && state.filter.status !== 'mine') {
          dispatch('getDatatableDatas') // we need to get the new datas from the api
        } else {
          // here we can simply update the store
          commit(types.PUBLISH_DATATABLE, {
            id: state.bulk,
            value: payload.toPublish
          })
          commit(types.UPDATE_DATATABLE_NAV, navigation)
        }
      }
    )
  },
  toggleFeaturedData ({ commit, state }, id) {
    api.toggleFeatured(
      id,
      id => {
        commit(types.FEATURE_DATATABLE, {
          id: id,
          value: 'toggle'
        })
      }
    )
  },
  bulkFeatureData ({ commit, state }) {
    api.bulkFeature(
      state.bulk.join(),
      ids => {
        commit(types.FEATURE_DATATABLE, {
          id: state.bulk,
          value: true
        })
      }
    )
  },
  deleteData ({ commit, state }, id) {
    const params = {
      sortKey: state.sortKey,
      sortDir: state.sortDir,
      page: state.page,
      offset: state.offset,
      columns: getters.visibleColumnsNames,
      filter: state.filter
    }

    params.id = id

    api.delete(
      params,
      function (resp) {
        // success callback
        commit(types.UPDATE_DATATABLE_DATA, resp.data)
        commit(types.UPDATE_DATATABLE_MAXPAGE, resp.maxPage)
      }
    )
  },
  bulkDeleteData ({ commit, state }) {
    const params = {
      sortKey: state.sortKey,
      sortDir: state.sortDir,
      page: state.page,
      offset: state.offset,
      columns: getters.visibleColumnsNames,
      filter: state.filter
    }

    params.ids = state.bulk.join()

    api.bulkDelete(
      params,
      function (resp) {
        // success callback
        commit(types.UPDATE_DATATABLE_DATA, resp.data)
        commit(types.UPDATE_DATATABLE_MAXPAGE, resp.maxPage)
      }
    )
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
