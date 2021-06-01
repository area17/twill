/**
 * Content
 *
 * Manages the block editor / visual editor :
 * create, delete reoder blocks of different types of content to create complex pages
 */

import Vue from 'vue'
import api from '../api/content'
import { CONTENT } from '../mutations'
import ACTIONS from '@/store/actions'
import { buildBlock, isBlockEmpty } from '@/utils/getFormData.js'

const state = {
  /**
   * Loading previews state
   * @type {Boolean}
   */
  loading: false,
  /**
   * Define if we want to have a visual editor
   * @type {Boolean}
   */
  editor: window[process.env.VUE_APP_NAME].STORE.form.editor || false,
  /**
   * An object with all the blocks available to add
   * @type {Object}
   */
  available: window[process.env.VUE_APP_NAME].STORE.form.blocks.available || {},
  /**
   * An array with all the blocks created
   * @type {Object.Array}
   */
  used: window[process.env.VUE_APP_NAME].STORE.form.blocks.used || {},
  /**
   * An object with all the Html for the previews of the blocks
   * @type {Object.string}
   */
  previews: window[process.env.VUE_APP_NAME].STORE.form.previews || {},
  /**
   * Block that is currently being edited in the visual Editor
   * @type {Object}
   */
  active: {}
}

// getters
const getters = {
  previewsById: state => (id) => state.previews[id] ? state.previews[id] : '',
  savedBlocksBySection: state => section => state.used[section],
  availableBlocksBySection: state => section => state.available[section],
  allSavedBlocks: state => Object.keys(state.used).reduce((acc, section) => acc.concat(state.used[section]), []),
  allAvailableBlocks: state => Array.from(new Set(Object.keys(state.available).reduce((acc, section) => acc.concat(state.available[section]), []))),
  blockIndexBySection: (state, getters) => (block, section) => getters.savedBlocksBySection(section).findIndex(b => b.id === block.id),
  sections: state => Object.keys(state.available).reduce((acc, section) => {
    acc.push({
      label: section.charAt(0).toUpperCase() + section.slice(1),
      value: section
    })
    return acc
  }, [])
}

const mutations = {
  [CONTENT.ADD_BLOCK] (state, { block, index, section }) {
    // init used section
    if (!state.used[section]) state.used[section] = []

    if (index > -1) {
      state.used[section].splice(index, 0, block) // add after a certain position
    } else {
      state.used[section].push(block) // or add a new blocks at the end of the list
    }
  },
  [CONTENT.MOVE_BLOCK] (state, { section, newIndex, oldIndex }) {
    if (newIndex >= state.used[section].length) {
      let k = newIndex - state.used[section].length
      while ((k--) + 1) {
        state.used[section].push(undefined)
      }
    }
    state.used[section].splice(newIndex, 0, state.used[section].splice(oldIndex, 1)[0])
  },
  [CONTENT.DELETE_BLOCK] (state, { section, index }) {
    const id = state.used[section][index].id
    if (id) Vue.delete(state.previews, id)
    state.used[section].splice(index, 1)
  },
  [CONTENT.DUPLICATE_BLOCK] (state, { section, index, block }) {
    state.used[section].splice(index + 1, 0, block)
  },
  [CONTENT.REORDER_BLOCKS] (state, { section, value }) {
    state.used[section] = value
  },
  [CONTENT.ACTIVATE_BLOCK] (state, { section, index }) {
    if (state.used[section] && state.used[section][index]) state.active = state.used[section][index]
    else state.active = {}
  },
  [CONTENT.ADD_BLOCK_PREVIEW] (state, data) {
    Vue.set(state.previews, data.id, data.html)
  },
  [CONTENT.UPDATE_PREVIEW_LOADING] (state, loading) {
    state.loading = !state.loading
  }
}

const getBlockPreview = (block, commit, rootState, callback) => {
  if (block && block.hasOwnProperty('id')) {
    const blockData = buildBlock(block, rootState)

    if (rootState.language.all.length > 1) {
      blockData.activeLanguage = rootState.language.active.value
    }

    if (isBlockEmpty(blockData)) {
      commit(CONTENT.ADD_BLOCK_PREVIEW, {
        id: block.id,
        html: ''
      })

      if (callback && typeof callback === 'function') callback()
    } else {
      api.getBlockPreview(
        rootState.form.blockPreviewUrl,
        blockData,
        data => {
          commit(CONTENT.ADD_BLOCK_PREVIEW, {
            id: block.id,
            html: data
          })

          if (callback && typeof callback === 'function') callback()
        },
        errorResponse => { }
      )
    }
  }
}

const actions = {
  [ACTIONS.GET_PREVIEW] ({ commit, state, rootState }, { section, index = -1 }) {
    let block = state.used[section] && index >= 0 ? state.used[section][index] : {}

    // refresh preview of the active block
    if (state.active && state.active.hasOwnProperty('id') && index === -1) block = state.active

    getBlockPreview(block, commit, rootState)
  },
  [ACTIONS.GET_ALL_PREVIEWS] ({ commit, state, rootState }, { section }) {
    if (state.used[section] && state.used[section].length > 0 && !state.loading) {
      commit(CONTENT.UPDATE_PREVIEW_LOADING, true)
      let loadedPreview = 0
      const previewToload = state.used[section].length

      Object.values(state.used[section]).forEach((block) => {
        getBlockPreview(block, commit, rootState, () => {
          loadedPreview++
          if (loadedPreview === previewToload) commit(CONTENT.UPDATE_PREVIEW_LOADING, true)
        })
      })
    }
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
