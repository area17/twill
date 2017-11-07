import * as types from '../mutation-types'

const state = {
  available: [
    {
      title: 'Quote',
      icon: 'quote',
      component: 'a17-quote'
    },
    {
      title: 'Body text',
      icon: 'text',
      component: 'a17-bodytext'
    },
    {
      title: 'Full width Image',
      icon: 'image',
      component: 'a17-mediafield', // example of a basic image block
      attributes: {
        crop: 'cover'
      }
    },
    {
      title: 'Image Grid',
      icon: 'image',
      component: 'a17-slideshow', // example of a basic slideshow block
      attributes: {
        max: 6
      }
    },
    {
      title: 'Publication Grid',
      icon: 'text',
      component: 'a17-browserfield', // example of a basic browser block
      attributes: {
        max: 4,
        itemLabel: 'Publications',
        endpoint: 'https://www.mocky.io/v2/59d77e61120000ce04cb1c5b',
        modalTitle: 'Attach publications'
      }
    }
  ],
  blocks: []
}

// getters
const getters = { }

function setBlockID () {
  return Date.now()
}

const mutations = {
  [types.ADD_BLOCK] (state, blockInfos) {
    let block = blockInfos.block
    block.id = setBlockID()

    if (blockInfos.index > -1) {
      state.blocks.splice(blockInfos.index + 1, 0, block) // add after a certain position
    } else {
      state.blocks.push(block) // or add a new block at the end of the list
    }
  },
  [types.DELETE_BLOCK] (state, index) {
    state.blocks.splice(index, 1)
  },
  [types.DUPLICATE_BLOCK] (state, index) {
    let clone = Object.assign({}, state.blocks[index])
    clone.id = setBlockID()

    state.blocks.splice(index + 1, 0, clone)
  },
  [types.REORDER_BLOCKS] (state, newBlocks) {
    state.blocks = newBlocks
  }
}

export default {
  state,
  getters,
  mutations
}
