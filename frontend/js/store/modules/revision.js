import revisionAPI from '../api/revision'
import * as types from '../mutation-types'

const state = {
  loading: false,
  active: {},
  activeContent: '',
  currentContent: '',
  all: [
    {
      id: 1,
      author: 'George',
      datetime: '2017-09-11 16:30:10'
    },
    {
      id: 2,
      author: 'Martin',
      datetime: '2017-09-11 15:41:01'
    },
    {
      id: 3,
      author: 'George',
      datetime: '2017-09-11 11:16:45'
    },
    {
      id: 4,
      author: 'Admin',
      datetime: '2017-09-11 10:22:10'
    },
    {
      id: 5,
      author: 'Martin',
      datetime: '2017-09-11 09:30:53'
    },
    {
      id: 6,
      author: 'Martin',
      datetime: '2017-09-10 15:41:01'
    },
    {
      id: 7,
      author: 'George',
      datetime: '2017-09-09 11:16:45'
    },
    {
      id: 8,
      author: 'Admin',
      datetime: '2017-09-08 10:22:10'
    },
    {
      id: 9,
      author: 'Martin',
      datetime: '2017-09-07 09:30:53'
    }
  ]
}

// getters
const getters = { }

const mutations = {
  [types.LOADING_REV] (state) {
    state.loading = true
  },
  [types.UPDATE_REV] (state, newValue) {
    function isMatchingRev (revision) {
      return revision.id === newValue
    }

    const index = state.all.findIndex(isMatchingRev)

    if (index !== -1) state.active = state.all[index]
    else state.active = {}
  },
  [types.UPDATE_REV_CONTENT] (state, fullHTML) {
    state.loading = false
    state.activeContent = fullHTML
  },
  [types.UPDATE_REV_CURRENT_CONTENT] (state, fullHTML) {
    state.loading = false
    state.currentContent = fullHTML
  }
}

const actions = {
  getRevisionContent ({ commit, state, getters }) {
    commit(types.LOADING_REV)

    let id = 0

    if (Object.keys(state.active).length === 0) id = state.all[0].id
    else id = state.active.id

    revisionAPI.getRevisionContent(
      id,
      data => {
        commit(types.UPDATE_REV_CONTENT, data)
      }
    )
  },
  getCurrentContent ({ commit, state, getters }) {
    commit(types.LOADING_REV)

    revisionAPI.getCurrentContent(
      'some params',
      data => {
        commit(types.UPDATE_REV_CURRENT_CONTENT, data)
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
