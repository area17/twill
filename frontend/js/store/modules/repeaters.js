import * as types from '../mutation-types'

const state = {
  repeaters: window.STORE.form.repeaters || {},
  availableRepeaters: window.STORE.form.availableRepeaters || {}
}

// getters
const getters = {}

function setBlockID () {
  return Date.now()
}

const mutations = {
  [types.ADD_FORM_BLOCK] (state, blockInfos) {
    console.log('ADD_FORM_BLOCK')
    console.log(blockInfos)

    const blockName = blockInfos.name
    const blockType = blockInfos.type
    const block = {}
    const blockModel = state.availableRepeaters[blockType]
    const isNew = (!state.repeaters[blockName])

    if (!blockModel) return

    block.id = setBlockID()
    block.type = blockModel.component
    block.title = blockModel.title

    // create new repeater object if required
    if (isNew) {
      const newBlocks = {}
      newBlocks[blockName] = []
      newBlocks[blockName].push(block)

      state.repeaters = Object.assign({}, state.repeaters, newBlocks)
    } else {
      state.repeaters[blockName].push(block) // or add a new block at the end of the list
    }
  },
  [types.DELETE_FORM_BLOCK] (state, blockInfos) {
    state.repeaters[blockInfos.name].splice(blockInfos.index, 1)
  },
  [types.DUPLICATE_FORM_BLOCK] (state, blockInfos) {
    let clone = Object.assign({}, state.repeaters[blockInfos.name][blockInfos.index])
    clone.id = setBlockID()
    state.repeaters[blockInfos.name].splice(blockInfos.index + 1, 0, clone)
  },
  [types.REORDER_FORM_BLOCKS] (state, newValues) {
    const newBlocks = {}
    newBlocks[newValues.name] = newValues.blocks
    state.repeaters = Object.assign({}, state.repeaters, newBlocks)
  }
}

export default {
  state,
  getters,
  mutations
}
