import Vue from 'vue'
import * as types from '../mutation-types'

const state = {
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

const actions = {
  getPreviews ({ commit, state, getters }) {
    // refresh preview of the active block
    if (state.active.hasOwnProperty('id')) {
      const block = state.active
      // AJAX goes here to retrieve the html
      commit(types.ADD_BLOCK_PREVIEW, {
        id: block.id,
        html: block.title + ' - Get preview HTML <br /> <div style="background-color:yellow; padding:20px; height:' + (30 + Math.floor(Math.random() * 350)) + 'px">Variable height div to test resize</div> Block ID : ' + block.id
      })
    } else {
      // AJAX goes here to retrieve the html of all the state.blocks
    }
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
