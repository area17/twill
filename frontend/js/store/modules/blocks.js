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
  blocks: state => editorName => state.blocks[editorName] || [],
  availableBlocks: state => editorName => state.available[editorName] || [],
  blockIndex: (state, getters) => (block, editorName) => getters.blocks(editorName).findIndex(b => b.id === block.id),
  editorNames: state => Object.keys(state.available).reduce((acc, editorName) => {
    acc.push({
      label: editorName.charAt(0).toUpperCase() + editorName.slice(1),
      value: editorName
    })
    return acc
  }, [])
}

const mutations = {
  [BLOCKS.ADD_BLOCK] (state, { block, index, editorName }) {
    const updated = state.blocks[editorName] || []

    if (index > -1) {
      updated.splice(index, 0, block) // add after a certain position
    } else {
      updated.push(block) // or add a new blocks at the end of the list
    }

    Vue.set(state.blocks, editorName, updated)
  },
  [BLOCKS.MOVE_BLOCK] (state, { editorName, newIndex, oldIndex }) {
    const updated = state.blocks[editorName] || []

    if (newIndex >= updated.length) {
      let k = newIndex - updated.length
      while ((k--) + 1) {
        updated.push(undefined)
      }
    }

    updated.splice(newIndex, 0, updated.splice(oldIndex, 1)[0])

    Vue.set(state.blocks, editorName, updated)
  },
  [BLOCKS.DELETE_BLOCK] (state, { editorName, index }) {
    const id = state.blocks[editorName][index].id
    const updated = state.blocks[editorName] || []

    if (id) {
      Vue.delete(state.previews, id)
    }

    updated.splice(index, 1)

    Vue.set(state.blocks, editorName, updated)
  },
  [BLOCKS.DUPLICATE_BLOCK] (state, { editorName, index, block }) {
    const updated = state.blocks[editorName] || []

    updated.splice(index + 1, 0, block)

    Vue.set(state.blocks, editorName, updated)
  },
  [BLOCKS.REORDER_BLOCKS] (state, { editorName, value }) {
    Vue.set(state.blocks, editorName, value)
  },
  [BLOCKS.ACTIVATE_BLOCK] (state, { editorName, index }) {
    if (state.blocks[editorName] && state.blocks[editorName][index]) {
      state.active = { ...state.blocks[editorName][index] }
    } else {
      state.active = {}
    }
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
  [ACTIONS.GET_PREVIEW] ({ commit, state, rootState }, { editorName, index = -1 }) {
    let block = state.blocks[editorName] && index >= 0 ? { ...state.blocks[editorName][index] } : {}

    // refresh preview of the active block
    if (state.active && state.active.hasOwnProperty('id') && index === -1) {
      block = { ...state.active }
    }

    getBlockPreview(block, commit, rootState)
  },
  [ACTIONS.GET_ALL_PREVIEWS] ({ commit, state, rootState }, { editorName }) {
    if (state.blocks[editorName] && state.blocks[editorName].length > 0 && !state.loading) {
      commit(BLOCKS.UPDATE_PREVIEW_LOADING, true)
      let loadedPreview = 0
      const previewToload = state.blocks[editorName].length

      Object.values(state.blocks[editorName]).forEach((block) => {
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
