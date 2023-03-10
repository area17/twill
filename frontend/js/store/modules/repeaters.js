import ACTIONS from '@/store/actions'

import { FORM } from '../mutations'

const state = {
  /**
   * An array with all the repeaters created
   * @type {Array.Object}
   */
  repeaters: window[process.env.VUE_APP_NAME].STORE.form.repeaters || {},
  /**
   * An object with all the repeaters available to add
   * @type {Object}
   */
  availableRepeaters: window[process.env.VUE_APP_NAME].STORE.form.availableRepeaters || {}
}

// getters
const getters = {
  repeatersByBlockId: (state) => (id) => {
    const ids = Object.keys(state.repeaters).filter(key => key.startsWith(`blocks-${id}`))
    const repeaters = {}
    ids.forEach(id => (repeaters[id] = state.repeaters[id]))
    return repeaters
  }
}

function setBlockID () {
  return Date.now() + Math.floor(Math.random() * 1000)
}

const mutations = {
  [FORM.ADD_FORM_BLOCK] (state, blockInfos) {
    const blockName = blockInfos.name
    const blockType = blockInfos.type
    const block = {}
    const blockModel = state.availableRepeaters[blockType]
    const isNew = (!state.repeaters[blockName])

    if (!blockModel) return

    block.id = setBlockID()
    block.type = blockModel.component
    block.title = blockModel.title

    // Metadata for rendering
    block.twillUi = {}
    block.twillUi.isNew = true

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
  [FORM.ADD_REPEATER_FROM_SELECTION] (state, blockInfos) {
    const blockName = blockInfos.name
    const blockType = blockInfos.type

    const blockModel = state.availableRepeaters[blockType]
    const isNew = (!state.repeaters[blockName])

    if (!blockModel) return

    const newBlocks = {}
    newBlocks[blockName] = []

    blockInfos.selection.forEach((item) => {

      const block = {}
      block.id = setBlockID()
      block.type = blockModel.component
      block.title = blockModel.title
      block.repeater_target_id = item.id

      const fieldData = [];

      for (const key in item.repeaterFields) {
        if (item.repeaterFields.hasOwnProperty(key)) {
          fieldData.push({
            name: `blocks[${block.id}][${key}]`,
            value: item.repeaterFields[key]
          })
        }
      }

      this.commit(FORM.ADD_FORM_FIELDS, fieldData)

      if (!isNew) {
        state.repeaters[blockName].push(block)
      }
      else {
        newBlocks[blockName].push(block)
      }
    })

    if (isNew) {
      state.repeaters = Object.assign({}, state.repeaters, newBlocks)
    }
  },
  [FORM.DELETE_FORM_BLOCK] (state, blockInfos) {
    state.repeaters[blockInfos.name].splice(blockInfos.index, 1)
  },
  [FORM.DUPLICATE_FORM_BLOCK] (state, blockInfos) {
    const clone = Object.assign({}, state.repeaters[blockInfos.name][blockInfos.index])
    clone.id = setBlockID()

    // Metadata for rendering
    clone.twillUi = {}
    clone.twillUi.isNew = true

    state.repeaters[blockInfos.name].splice(blockInfos.index + 1, 0, clone)

    // Clone the fields as well.
    const fields = [...getters.fieldsByBlockId(blockInfos.id)]
    const fieldCopies = []
    fields.forEach(field => {
      fieldCopies.push({
        name: field.name.replace(blockInfos.id, clone.id),
        value: field.value
      })
    })
    this.commit(FORM.ADD_FORM_FIELDS, fieldCopies)
  },
  [FORM.REORDER_FORM_BLOCKS] (state, newValues) {
    const newBlocks = {}
    newBlocks[newValues.name] = newValues.blocks
    state.repeaters = Object.assign({}, state.repeaters, newBlocks)
  },
  [FORM.ADD_REPEATERS] (state, { repeaters }) {
    state.repeaters = Object.assign({}, state.repeaters, repeaters)
  }
}

const actions = {
  async [ACTIONS.DUPLICATE_REPEATER] ({ state, commit, getters }, { editorName, block, index, id }) {
    const clone = Object.assign({}, state.repeaters[editorName][index])
    clone.id = id

    // Metadata for rendering
    clone.twillUi = {}
    clone.twillUi.isNew = true

    const duplicates = {}
    duplicates[editorName] = [...state.repeaters[editorName]];
    duplicates[editorName].splice(index + 1, 0, clone)


    // Clone the fields as well.
    const fields = [...getters.fieldsByBlockId(block.id)]
    const fieldCopies = []
    fields.forEach(field => {
      fieldCopies.push({
        name: field.name.replace(block.id, clone.id),
        value: JSON.parse(JSON.stringify(field.value))
      })
    })

    commit(FORM.ADD_FORM_FIELDS, fieldCopies)
    commit(FORM.ADD_REPEATERS, { repeaters: duplicates })
  },
  async [ACTIONS.DUPLICATE_BLOCK] ({ commit, getters }, { block, id }) {
    // copy repeaters and update with the provided id
    const repeaters = { ...getters.repeatersByBlockId(block.id) }
    const repeaterIds = Object.keys(repeaters)
    const duplicates = {}
    repeaterIds.forEach(repeaterId => (duplicates[repeaterId.replace(block.id, id)] = [...repeaters[repeaterId]]))

    // copy fields and give them a new id
    const fieldCopies = []
    Object.keys(duplicates).forEach(duplicateId => {
      duplicates[duplicateId].forEach((block, index) => {
        const id = Date.now() + Math.floor(Math.random() * 1000)
        const fields = [...getters.fieldsByBlockId(block.id)]
        duplicates[duplicateId][index] = { ...duplicates[duplicateId][index], id }

        fields.forEach(field => {
          fieldCopies.push({
            name: field.name.replace(block.id, id),
            value: JSON.parse(JSON.stringify(field.value))
          })
        })
      })
    })

    commit(FORM.ADD_REPEATERS, { repeaters: duplicates })
    commit(FORM.ADD_FORM_FIELDS, fieldCopies)
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
