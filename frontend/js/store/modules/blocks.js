/**
 * Content
 *
 * Manages the block editor / visual editor :
 * create, delete reoder blocks of different types of content to create complex pages
 */

import Vue from 'vue'
import api from '../api/blocks'
import { BLOCKS } from '../mutations'
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
  available: window[process.env.VUE_APP_NAME].STORE.form.availableBlocks || {},
  /**
   * An array with all the blocks created
   * @type {Object.Array}
   */
  blocks: window[process.env.VUE_APP_NAME].STORE.form.blocks || {},
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
  blocksByName: state => section => state.blocks[section],
  availableBlocksByName: state => section => state.available[section],
  blockIndexBySection: (state, getters) => (block, section) => getters.blocksByName(section).findIndex(b => b.id === block.id),
  sections: state => Object.keys(state.available).reduce((acc, section) => {
    acc.push({
      label: section.charAt(0).toUpperCase() + section.slice(1),
      value: section
    })
    return acc
  }, [])
}

const mutations = {
  [BLOCKS.ADD_BLOCK] (state, { block, index, section }) {
    // init used section
    if (!state.blocks[section]) state.blocks[section] = []

    if (index > -1) {
      state.blocks[section].splice(index, 0, block) // add after a certain position
    } else {
      state.blocks[section].push(block) // or add a new blocks at the end of the list
    }
  },
  [BLOCKS.MOVE_BLOCK] (state, { section, newIndex, oldIndex }) {
    if (newIndex >= state.blocks[section].length) {
      let k = newIndex - state.blocks[section].length
      while ((k--) + 1) {
        state.blocks[section].push(undefined)
      }
    }
    state.blocks[section].splice(newIndex, 0, state.blocks[section].splice(oldIndex, 1)[0])
  },
  [BLOCKS.DELETE_BLOCK] (state, { section, index }) {
    const id = state.blocks[section][index].id
    if (id) Vue.delete(state.previews, id)
    state.blocks[section].splice(index, 1)
  },
  [BLOCKS.DUPLICATE_BLOCK] (state, { section, index, block }) {
    state.blocks[section].splice(index + 1, 0, block)
  },
  [BLOCKS.REORDER_BLOCKS] (state, { section, value }) {
    state.blocks[section] = value
  },
  [BLOCKS.ACTIVATE_BLOCK] (state, { section, index }) {
    if (state.blocks[section] && state.blocks[section][index]) state.active = state.blocks[section][index]
    else state.active = {}
  },
  [BLOCKS.ADD_BLOCK_PREVIEW] (state, data) {
    Vue.set(state.previews, data.id, data.html)
  },
  [BLOCKS.UPDATE_PREVIEW_LOADING] (state, loading) {
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
      commit(BLOCKS.ADD_BLOCK_PREVIEW, {
        id: block.id,
        html: ''
      })

      if (callback && typeof callback === 'function') callback()
    } else {
      api.getBlockPreview(
        rootState.form.blockPreviewUrl,
        blockData,
        data => {
          commit(BLOCKS.ADD_BLOCK_PREVIEW, {
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
    let block = state.blocks[section] && index >= 0 ? state.blocks[section][index] : {}

    // refresh preview of the active block
    if (state.active && state.active.hasOwnProperty('id') && index === -1) block = state.active

    getBlockPreview(block, commit, rootState)
  },
  [ACTIONS.GET_ALL_PREVIEWS] ({ commit, state, rootState }, { section }) {
    if (state.blocks[section] && state.blocks[section].length > 0 && !state.loading) {
      commit(BLOCKS.UPDATE_PREVIEW_LOADING, true)
      let loadedPreview = 0
      const previewToload = state.blocks[section].length

      Object.values(state.blocks[section]).forEach((block) => {
        getBlockPreview(block, commit, rootState, () => {
          loadedPreview++
          if (loadedPreview === previewToload) commit(BLOCKS.UPDATE_PREVIEW_LOADING, true)
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
