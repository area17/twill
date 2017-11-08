import * as types from '../mutation-types'

const state = {
  title: window.STORE.form.title,
  permalink: window.STORE.form.permalink,
  baseUrl: window.STORE.form.baseUrl,
  fields: window.STORE.form.fields || [],
  repeaters: {},
  availableRepeaters: {
    video: {
      title: 'Video',
      trigger: 'Add Videos',
      component: 'a17-video', // This will be project specific
      max: 4
    }
  }
}

// getters
const getters = {}

function setBlockID () {
  return Date.now()
}

const mutations = {
  [types.UPDATE_FORM_TITLE] (state, newValue) {
    if (newValue && newValue !== '') {
      state.title = newValue
    }
  },
  [types.UPDATE_FORM_PERMALINK] (state, newValue) {
    if (newValue && newValue !== '') {
      state.permalink = newValue
    }
  },
  [types.UPDATE_FORM_FIELD] (state, field) {
    const fieldToUpdate = state.fields.filter(function (f) {
      return f.name === field.name
    })

    // Update existing form field
    if (fieldToUpdate.length) {
      if (field.locale) {
        fieldToUpdate[0].value[field.locale] = field.value
      } else {
        fieldToUpdate[0].value = field.value
      }
    } else {
      // Or Create a new form field
      if (field.locale) {
        const localeValue = {}
        localeValue[field.locale] = field.value

        state.fields.push({
          name: field.name,
          value: localeValue
        })
      } else {
        state.fields.push({
          name: field.name,
          value: field.value
        })
      }
    }
  },
  [types.REMOVE_FORM_FIELD] (state, fieldName) {
    state.fields.forEach(function (field, index) {
      if (field.name === fieldName) state.fields.splice(index, 1)
    })
  },
  [types.ADD_FORM_BLOCK] (state, blockInfos) {
    const blockId = blockInfos.id
    const block = {}
    const blockModel = state.availableRepeaters[blockId]
    const isNew = (!state.repeaters[blockId])

    if (!blockModel) return

    block.id = setBlockID()
    block.type = blockModel.component
    block.title = blockModel.title

    // create new repeater object if required
    if (isNew) {
      const newBlocks = {}
      newBlocks[blockId] = []
      newBlocks[blockId].push(block)

      state.repeaters = Object.assign({}, state.repeaters, newBlocks)
    } else {
      state.repeaters[blockId].push(block) // or add a new block at the end of the list
    }
  },
  [types.DELETE_FORM_BLOCK] (state, blockInfos) {
    state.repeaters[blockInfos.id].splice(blockInfos.index, 1)
  },
  [types.DUPLICATE_FORM_BLOCK] (state, blockInfos) {
    let clone = Object.assign({}, state.repeaters[blockInfos.id][blockInfos.index])
    clone.id = setBlockID()
    state.repeaters[blockInfos.id].splice(blockInfos.index + 1, 0, clone)
  },
  [types.REORDER_FORM_BLOCKS] (state, newValues) {
    const newBlocks = {}
    newBlocks[newValues.id] = newValues.blocks
    state.repeaters = Object.assign({}, state.repeaters, newBlocks)
  }
}

export default {
  state,
  getters,
  mutations
}
