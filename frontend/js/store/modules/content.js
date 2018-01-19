import Vue from 'vue'
import * as types from '../mutation-types'

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

const previewHTML = function (block) {
  return block.title + ' - Get preview HTML ' + new Date() + '<br /> <div style="background-color:yellow; padding:20px; min-height:' + Math.floor(Math.random() * 300) + 'px">Variable height div to test resize.<br />Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div> Block ID : ' + block.id
}

const actions = {
  getPreview ({ commit, state, getters }, index = -1) {
    let block = index >= 0 ? state.blocks[index] : {}

    // refresh preview of the active block
    if (state.active.hasOwnProperty('id') && index === -1) block = state.active

    if (block.hasOwnProperty('id')) {
      console.log('Actions - getPreview HTML : ' + block.id)

      // AJAX goes here to retrieve the html output
      commit(types.ADD_BLOCK_PREVIEW, {
        id: block.id,
        html: previewHTML(block)
      })
    }
  },
  getAllPreviews ({ commit, state, getters }) {
    if (state.blocks.length) {
      state.blocks.forEach(function (block) {
        console.log('Actions - getPreview HTML : ' + block.id)

        // AJAX goes here to retrieve the html output
        commit(types.ADD_BLOCK_PREVIEW, {
          id: block.id,
          html: previewHTML(block)
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
