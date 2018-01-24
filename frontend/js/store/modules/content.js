import Vue from 'vue'
import * as types from '../mutation-types'
import { getFormData } from '@/utils/getFormData.js'

const state = {
  editor: window.STORE.form.editor || false,
  available: window.STORE.form.content || {},
  blocks: window.STORE.form.blocks || [],
  previews: window.STORE.form.previews || {},
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
  [types.ADD_BLOCK] (state, blockInfos) {
    let block = blockInfos.block
    block.id = setBlockID()

    if (blockInfos.index > -1) {
      state.blocks.splice(blockInfos.index, 0, block) // add after a certain position
    } else {
      state.blocks.push(block) // or add a new block at the end of the list
    }
  },
  [types.MOVE_BLOCK] (state, fromTo) {
    if (fromTo.newIndex >= state.blocks.length) {
      var k = fromTo.newIndex - state.blocks.length
      while ((k--) + 1) {
        state.blocks.push(undefined)
      }
    }
    state.blocks.splice(fromTo.newIndex, 0, state.blocks.splice(fromTo.oldIndex, 1)[0])
  },
  [types.DELETE_BLOCK] (state, index) {
    const id = state.blocks[index].id
    if (id) Vue.delete(state.previews, id)
    state.blocks.splice(index, 1)
  },
  [types.DUPLICATE_BLOCK] (state, index) {
    let clone = Object.assign({}, state.blocks[index])
    clone.id = setBlockID()

    state.blocks.splice(index + 1, 0, clone)
  },
  [types.REORDER_BLOCKS] (state, newBlocks) {
    state.blocks = newBlocks
  },
  [types.ACTIVATE_BLOCK] (state, index) {
    if (state.blocks[index]) state.active = state.blocks[index]
    else state.active = {}
  },
  [types.ADD_BLOCK_PREVIEW] (state, data) {
    Vue.set(state.previews, data.id, data.html)
  }
}

const previewHTML = function (block, data) {
  return '<div style="font-family:Arial"><div style="background-color:#EFEFEF; padding:20px;">' + block.title + ' - Preview<br />' + new Date() + '<br />Block ID : ' + block.id + '<br /><br />' + JSON.stringify(data.blocks, null, 4) + '</div></div>'
}

const actions = {
  getPreview ({ commit, state, getters, rootState }, index = -1) {
    let block = index >= 0 ? state.blocks[index] : {}

    const data = getFormData(rootState)

    // refresh preview of the active block
    if (state.active.hasOwnProperty('id') && index === -1) block = state.active

    if (block.hasOwnProperty('id')) {
      console.log('Actions - getPreview HTML : ' + block.id)

      // AJAX goes here to retrieve the html output
      commit(types.ADD_BLOCK_PREVIEW, {
        id: block.id,
        html: previewHTML(block, data)
      })
    }
  },
  getAllPreviews ({ commit, state, getters }) {

    const data = getFormData(rootState)

    if (state.blocks.length) {
      state.blocks.forEach(function (block) {
        console.log('Actions - getPreview HTML : ' + block.id)

        // AJAX goes here to retrieve the html output
        commit(types.ADD_BLOCK_PREVIEW, {
          id: block.id,
          html: previewHTML(block, data)
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
