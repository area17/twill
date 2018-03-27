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
  editor: window.STORE.form.editor || false,
  /**
   * An object with all the blocks available to add
   * @type {Object}
   */
  available: window.STORE.form.content || {},
  /**
   * An array with all the blocks created
   * @type {Array.Object}
   */
  blocks: window.STORE.form.blocks || [],
  /**
   * An object with all the Html for the previews of the blocks
   * @type {Object.string}
   */
  previews: window.STORE.form.previews || {},
  /**
   * Block that is currently being edited in the visual Editor
   * @type {Object}
   */
  active: {}
}

// getters
const getters = {
  previewsById (state) {
    return id => state.previews[id] ? state.previews[id] : ''
  }
}

function setBlockID () {
  return Date.now()
}

const mutations = {
  [CONTENT.ADD_BLOCK] (state, blockInfos) {
    let block = blockInfos.block
    block.id = setBlockID()

    if (blockInfos.index > -1) {
      state.blocks.splice(blockInfos.index, 0, block) // add after a certain position
    } else {
      state.blocks.push(block) // or add a new block at the end of the list
    }
  },
  [CONTENT.MOVE_BLOCK] (state, fromTo) {
    if (fromTo.newIndex >= state.blocks.length) {
      let k = fromTo.newIndex - state.blocks.length
      while ((k--) + 1) {
        state.blocks.push(undefined)
      }
    }
    state.blocks.splice(fromTo.newIndex, 0, state.blocks.splice(fromTo.oldIndex, 1)[0])
  },
  [CONTENT.DELETE_BLOCK] (state, index) {
    const id = state.blocks[index].id
    if (id) Vue.delete(state.previews, id)
    state.blocks.splice(index, 1)
  },
  [CONTENT.DUPLICATE_BLOCK] (state, index) {
    let clone = Object.assign({}, state.blocks[index])
    clone.id = setBlockID()

    state.blocks.splice(index + 1, 0, clone)
  },
  [CONTENT.REORDER_BLOCKS] (state, newBlocks) {
    state.blocks = newBlocks
  },
  [CONTENT.ACTIVATE_BLOCK] (state, index) {
    if (state.blocks[index]) state.active = state.blocks[index]
    else state.active = {}
  },
  [CONTENT.ADD_BLOCK_PREVIEW] (state, data) {
    Vue.set(state.previews, data.id, data.html)
  },
  [CONTENT.UPDATE_PREVIEW_LOADING] (state, loading) {
    state.loading = !state.loading
  }
}

function getBlockPreview (block, commit, rootState, callback) {
  if (block.hasOwnProperty('id')) {
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
        errorResponse => {}
      )
    }
  }
}

const actions = {
  [ACTIONS.GET_PREVIEW] ({ commit, state, rootState }, index = -1) {
    let block = index >= 0 ? state.blocks[index] : {}

    // refresh preview of the active block
    if (state.active.hasOwnProperty('id') && index === -1) block = state.active

    getBlockPreview(block, commit, rootState)
  },
  [ACTIONS.GET_ALL_PREVIEWS] ({ commit, state, rootState }) {
    if (state.blocks.length && !state.loading) {
      commit(CONTENT.UPDATE_PREVIEW_LOADING, true)
      let loadedPreview = 0

      state.blocks.forEach(function (block) {
        getBlockPreview(block, commit, rootState, function () {
          loadedPreview++
          if (loadedPreview === state.blocks.length) commit(CONTENT.UPDATE_PREVIEW_LOADING, true)
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
