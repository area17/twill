import ACTIONS from '@/store/actions'

import bucketsAPI from '../api/buckets'
import { BUCKETS, NOTIFICATION } from '../mutations'

const state = {
  saveUrl: window[process.env.VUE_APP_NAME].STORE.buckets.saveUrl || '',
  dataSources: window[process.env.VUE_APP_NAME].STORE.buckets.dataSources || {},
  source: window[process.env.VUE_APP_NAME].STORE.buckets.source || {},
  /**
   * An array of Object. Each representing a bucket with his data types and current children
   * Buckets action ui is based on buckets length.
   * If buckets.length === 1 an 'add' icon instead of buckets number
   */
  buckets: window[process.env.VUE_APP_NAME].STORE.buckets.items || [],
  filter: window[process.env.VUE_APP_NAME].STORE.buckets.filter || {},
  page: window[process.env.VUE_APP_NAME].STORE.buckets.page || 1,
  maxPage: window[process.env.VUE_APP_NAME].STORE.buckets.maxPage || 10,
  offset: window[process.env.VUE_APP_NAME].STORE.buckets.offset || 10,
  availableOffsets: window[process.env.VUE_APP_NAME].STORE.buckets.availableOffsets || [10, 20, 30]
}

const getters = {
  currentSource: state => state.dataSources.selected
}

const mutations = {
  [BUCKETS.ADD_TO_BUCKET] (state, payload) {
    state.buckets[payload.index].children.push(payload.item)
  },
  [BUCKETS.DELETE_FROM_BUCKET] (state, payload) {
    state.buckets[payload.index].children.splice(payload.itemIndex, 1)
  },
  [BUCKETS.TOGGLE_FEATURED_IN_BUCKET] (state, payload) {
    const item = state.buckets[payload.index].children.splice(payload.itemIndex, 1)
    item[0].starred = !item[0].starred
    state.buckets[payload.index].children.splice(payload.itemIndex, 0, item[0])
  },
  [BUCKETS.UPDATE_BUCKETS_DATASOURCE] (state, dataSource) {
    if (state.dataSources.selected.value !== dataSource.value) state.dataSources.selected = dataSource
  },
  [BUCKETS.UPDATE_BUCKETS_DATA] (state, data) {
    state.source = Object.assign({}, state.source, data)
  },
  [BUCKETS.UPDATE_BUCKETS_FILTER] (state, filter) {
    state.filter = Object.assign({}, state.filter, filter)
  },
  [BUCKETS.REORDER_BUCKET_LIST] (state, payload) {
    const item = state.buckets[payload.bucketIndex].children.splice(payload.oldIndex, 1)
    state.buckets[payload.bucketIndex].children.splice(payload.newIndex, 0, item[0])
  },
  [BUCKETS.UPDATE_BUCKETS_DATA_OFFSET] (state, offsetNumber) {
    state.offset = offsetNumber
  },
  [BUCKETS.UPDATE_BUCKETS_DATA_PAGE] (state, pageNumber) {
    state.page = pageNumber
  },
  [BUCKETS.UPDATE_BUCKETS_MAX_PAGE] (state, maxPage) {
    state.maxPage = maxPage
  }
}

const actions = {
  [ACTIONS.GET_BUCKETS] ({ commit, state }) {
    bucketsAPI.get({
      content_type: state.dataSources.selected.value,
      page: state.page,
      offset: state.offset,
      filter: JSON.stringify(state.filter)
    }, resp => {
      commit(BUCKETS.UPDATE_BUCKETS_DATA, resp.source)
      commit(BUCKETS.UPDATE_BUCKETS_MAX_PAGE, resp.maxPage)
    })
  },
  [ACTIONS.SAVE_BUCKETS] ({ commit, state }) {
    const buckets = {}

    state.buckets.forEach((bucket) => {
      const children = []
      bucket.children.forEach((child) => {
        children.push({
          id: child.id,
          type: child.type,
          starred: child.starred
        })
      })
      buckets[bucket.id] = children
    })

    bucketsAPI.save(state.saveUrl, { buckets }, (successResponse) => {
      commit(NOTIFICATION.SET_NOTIF, {
        message: 'Features saved. All good!',
        variant: 'success'
      })
    }, (errorResponse) => {
      commit(NOTIFICATION.SET_NOTIF, {
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
