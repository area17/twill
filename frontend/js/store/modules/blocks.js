/**
 * Content
 *
 * Manages the block editor / visual editor :
 * create, delete reoder blocks of different types of content to create complex pages
 */

import Vue from 'vue'

import ACTIONS from '@/store/actions'
import { buildBlock, isBlockEmpty } from '@/utils/getFormData.js'

import api from '../api/blocks'
import { BLOCKS, BROWSER, FORM, MEDIA_LIBRARY } from '../mutations'

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
   * Array of Object editor names avaialble in this form in value/label pairs
   * @type {Array}
   */
  editorNames: window[process.env.VUE_APP_NAME].STORE.form.editorNames || [],
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
  blockIndex: (state, getters) => (block, editorName) => getters.blocks(editorName).findIndex(b => b.id === block.id)
}

const setBlockID = () => Date.now() + Math.floor(Math.random() * 1000)

const mutations = {
  [BLOCKS.ADD_BLOCK] (state, { block, index, editorName }) {
    const updated = state.blocks[editorName] || []
    const newBlock = { ...block, id: setBlockID(), name: editorName }

    // Metadata for rendering
    newBlock.twillUi = {}
    newBlock.twillUi.isNew = true

    if (index > -1) {
      updated.splice(index, 0, newBlock) // add after a certain position
    } else {
      updated.push(newBlock) // or add a new blocks at the end of the list
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
  [BLOCKS.DUPLICATE_BLOCK] (state, { editorName, index, block, id }) {
    const updated = state.blocks[editorName] || []

    updated.splice(index, 0, { ...JSON.parse(JSON.stringify(block)), id, name: editorName })

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
  },
  async [ACTIONS.DUPLICATE_BLOCK] ({ commit, state, rootState }, { editorName, futureIndex, block, id }) {
    const clone = (v) => v == null ? v : JSON.parse(JSON.stringify(v))

    const repeaters = (rootState.repeaters && rootState.repeaters.repeaters) || {}
    const nestedBlocks = (rootState.blocks && rootState.blocks.blocks) || {}
    const fields = (rootState.form && rootState.form.fields) || []
    const mediaSelected = (rootState.mediaLibrary && rootState.mediaLibrary.selected) || {}
    const browserSelected = (rootState.browser && rootState.browser.selected) || {}

    const idMap = { [block.id]: id }
    const queue = [block.id]
    while (queue.length) {
      const currentId = queue.shift()
      const prefix = `blocks-${currentId}|`
      ;[repeaters, nestedBlocks].forEach(bucket => {
        Object.keys(bucket).forEach(key => {
          if (!key.startsWith(prefix)) return
          ;(bucket[key] || []).forEach(item => {
            if (!item || item.id == null || idMap[item.id] != null) return
            idMap[item.id] = setBlockID()
            queue.push(item.id)
          })
        })
      })
    }

    const rewrite = (str, before, after) => Object.keys(idMap).reduce(
      (acc, oldId) => acc.split(`${before}${oldId}${after}`).join(`${before}${idMap[oldId]}${after}`), str
    )
    const inSubtree = (key) => Object.keys(idMap).some(oldId => key.startsWith(`blocks-${oldId}|`))

    commit(BLOCKS.DUPLICATE_BLOCK, { editorName, index: futureIndex, block, id })

    Object.keys(nestedBlocks).forEach(key => {
      if (!inSubtree(key)) return
      const newKey = rewrite(key, 'blocks-', '|')
      const cloned = (nestedBlocks[key] || []).map(nested => {
        if (!nested || nested.id == null || idMap[nested.id] == null) return null
        return { ...clone(nested), id: idMap[nested.id], name: newKey }
      }).filter(Boolean)
      if (!cloned.length) return
      const existing = state.blocks[newKey] || []
      commit(BLOCKS.REORDER_BLOCKS, { editorName: newKey, value: [...existing, ...cloned] })
    })

    const clonedRepeaters = {}
    Object.keys(repeaters).forEach(key => {
      if (!inSubtree(key)) return
      clonedRepeaters[rewrite(key, 'blocks-', '|')] = (repeaters[key] || []).map(item => {
        const c = clone(item)
        if (c && c.id != null && idMap[c.id] != null) c.id = idMap[c.id]
        if (c) c.twillUi = { isNew: true }
        return c
      })
    })
    if (Object.keys(clonedRepeaters).length) commit(FORM.ADD_REPEATERS, { repeaters: clonedRepeaters })

    const fieldCopies = []
    const clonedMedias = {}
    const clonedBrowsers = {}
    Object.keys(idMap).forEach(oldId => {
      const fp = `blocks[${oldId}]`
      fields.forEach(f => {
        if (typeof f.name !== 'string' || !f.name.startsWith(fp)) return
        fieldCopies.push({ name: rewrite(f.name, 'blocks[', ']'), value: clone(f.value) })
      })
      Object.keys(mediaSelected).forEach(k => {
        if (k.startsWith(fp)) clonedMedias[rewrite(k, 'blocks[', ']')] = clone(mediaSelected[k])
      })
      Object.keys(browserSelected).forEach(k => {
        if (k.startsWith(fp)) clonedBrowsers[rewrite(k, 'blocks[', ']')] = clone(browserSelected[k])
      })
    })
    if (fieldCopies.length) commit(FORM.ADD_FORM_FIELDS, fieldCopies)
    if (Object.keys(clonedMedias).length) commit(MEDIA_LIBRARY.ADD_MEDIAS, { medias: clonedMedias })
    if (Object.keys(clonedBrowsers).length) commit(BROWSER.ADD_BROWSERS, { browsers: clonedBrowsers })
  },
  async [ACTIONS.MOVE_BLOCK_TO_EDITOR] ({ commit, dispatch }, { editorName, index, block, futureIndex, id }) {
    await dispatch(ACTIONS.DUPLICATE_BLOCK, {
      editorName,
      futureIndex,
      block,
      id
    })
    commit(BLOCKS.DELETE_BLOCK, {
      editorName: block.name,
      index
    })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
