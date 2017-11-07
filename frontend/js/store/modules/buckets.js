import bucketsAPI from '../api/buckets'
import * as types from '../mutation-types'

const state = {
  dataSources: {
    selected: {
      label: 'Projects',
      value: 'projects'
    },
    content_types: [
      // based on v-select options
      {
        label: 'Projects',
        value: 'projects'
      },
      {
        label: 'Users',
        value: 'users'
      },
      {
        label: 'Teams Members',
        value: 'teams-members'
      }
    ]
  },
  source: {
    content_type: {
      label: 'Projects',
      value: 'projects'
    },
    items: [
      {
        id: 1,
        name: 'The New School Website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=1',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 2,
        name: 'Barnes Foundation website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=2',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 3,
        name: 'Pentagram website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=3',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 4,
        name: 'Mai 36 Galerie website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=4',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 5,
        name: 'Mai 36 Galerie website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=5',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 6,
        name: 'Roto website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=6',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 7,
        name: 'THG Paris website',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=7',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 8,
        name: 'La Parqueterie Nouvelle strategie',
        edit: 'http://pentagram.com',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=8',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      }
    ]
  },
  /**
   * Buckets action ui is based on buckets length.
   * If buckets.length === 1 an 'add' icon instead of buckets number
   */
  buckets: [
    {
      id: 1,
      name: 'Main features',
      children: [],
      max: 1
    },
    {
      id: 2,
      name: 'Secondary features',
      children: [],
      max: 3
    },
    {
      id: 3,
      name: 'Tertiary features',
      children: [
        {
          id: 2,
          name: 'Barnes Foundation website',
          edit: 'http://pentagram.com',
          thumbnail: 'https://source.unsplash.com/random/80x80?sig=2',
          content_type: {
            label: 'Projects',
            value: 'projects'
          }
        }
      ],
      max: 5
    }
  ],
  // TBD: this properties are same as datable.js. maybe, they could be refactored and use only one datable global store
  filter: {},
  page: 1,
  maxPage: 10,
  offset: 10,
  availableOffsets: [10, 20, 30]
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
  }
}

const actions = {
  getBucketsData ({commit, state}) {
    bucketsAPI.get(
      {
        content_type: state.dataSources.selected.value,
        page: state.page,
        offset: state.offset,
        filter: state.filter
      },
      source => {
        commit(types.UPDATE_BUCKETS_DATA, source)
      },
      () => {
        console.log('An error is occurred')
      }
    )
  },
  addToBucket ({commit}, data) {
    bucketsAPI.add({
      bucket_index: data.index,
      item_id: data.item.id,
      item_content_type: data.item.content_type.value
    },
      () => {
        commit(types.ADD_TO_BUCKET, data)
      },
      () => {
        console.log('An error is occurred')
      })
  },
  deleteFromBucket ({commit}, data) {
    bucketsAPI.delete({
      buckets_index: data.index,
      item_index: data.itemIndex
    },
      () => {
        commit(types.DELETE_FROM_BUCKET, data)
      },
      () => {
        console.log('An error is occurred')
      })
  },
  reorderBucket ({ commit }, data) {
    bucketsAPI.reorder({
      buckets_index: data.index,
      item_old_index: data.oldIndex,
      item_new_index: data.newIndex
    },
      () => {
        commit(types.REORDER_BUCKET_LIST, data)
      },
      () => {
        console.log('An error is occurred')
      })
  },
  overrideBucket ({commit}, data) {
    bucketsAPI.replace(data,
      () => {
        commit(types.DELETE_FROM_BUCKET, data.del)
        commit(types.ADD_TO_BUCKET, data.add)
      },
      () => {
        console.log('An error is occurred')
      })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}

