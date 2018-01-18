import bucketsAPI from '../api/buckets'
import * as types from '../mutation-types'

const state = {
  dataSources: window.STORE.buckets.dataSources || {},
  source: window.STORE.buckets.source || {},
  /**
   * Buckets action ui is based on buckets length.
   * If buckets.length === 1 an 'add' icon instead of buckets number
   */
  buckets: window.STORE.buckets.items || [],
  // TBD: this properties are same as datable.js. maybe, they could be refactored and use only one datable global store
  filter: window.STORE.buckets.filter || {},
  page: window.STORE.buckets.page || 1,
  maxPage: window.STORE.buckets.maxPage || 10,
  offset: window.STORE.buckets.offset || 10,
  availableOffsets: window.STORE.buckets.availableOffsets || [10, 20, 30]
}

const getters = {
  currentSource: state => state.source.content_type
}

const mutations = {
  [types.ADD_TO_BUCKET] (state, payload) {
    state.buckets[payload.index].children.push(payload.item)
  },
  [types.DELETE_FROM_BUCKET] (state, payload) {
    state.buckets[payload.index].children.splice(payload.itemIndex, 1)
  },
  [types.TOGGLE_FEATURED_IN_BUCKET] (state, payload) {
    let item = state.buckets[payload.index].children.splice(payload.itemIndex, 1)
    item[0].starred = !item[0].starred
    state.buckets[payload.index].children.splice(payload.itemIndex, 0, item[0])
  },
  [types.UPDATE_BUCKETS_DATASOURCE] (state, dataSource) {
    if (state.dataSources.selected.value !== dataSource.value) state.dataSources.selected = dataSource
  },
  [types.UPDATE_BUCKETS_DATA] (state, data) {
    state.source = Object.assign({}, state.source, data)
  },
  [types.UPDATE_BUCKETS_FILTER] (state, filter) {
    state.filter = Object.assign({}, state.filter, filter)
  },
  [types.REORDER_BUCKET_LIST] (state, payload) {
    let item = state.buckets[payload.bucketIndex].children.splice(payload.oldIndex, 1)
    state.buckets[payload.bucketIndex].children.splice(payload.newIndex, 0, item[0])
  },
  [types.UPDATE_BUCKETS_DATA_OFFSET] (state, offsetNumber) {
    state.offset = offsetNumber
  },
  [types.UPDATE_BUCKETS_DATA_PAGE] (state, pageNumber) {
    state.page = pageNumber
  },
  [types.UPDATE_BUCKETS_MAX_PAGE] (state, maxPage) {
    state.maxPage = maxPage
  }
}

const actions = {
  getBucketsData ({commit, state}) {
    bucketsAPI.get({
      content_type: state.dataSources.selected.value,
      page: state.page,
      offset: state.offset,
      filter: state.filter
    }, resp => {
      commit(types.UPDATE_BUCKETS_DATA, resp.source)
      commit(types.UPDATE_BUCKETS_MAX_PAGE, resp.maxPage)
    })
  },
  saveBuckets ({commit, state}) {
    const buckets = {}

    state.buckets.forEach((bucket) => {
      const children = []
      bucket.children.forEach((child) => {
        children.push({
          id: child.id,
          type: child.content_type.value,
          starred: child.withToggleFeatured
        })
      })
      buckets[bucket.id] = children
    })

    bucketsAPI.save('', {buckets: buckets}, (successResponse) => {
      // TODO: Show notification success
      commit(types.SET_NOTIF, {
        message: 'all saved',
        variant: 'success'})
    }, (errorResponse) => {
      commit(types.SET_NOTIF, {
        message: 'Your submission could not be validated, please fix and retry',
        variant: 'error'
      })
    })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}

