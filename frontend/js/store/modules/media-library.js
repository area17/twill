/**
 * Media Library
 *
 * Set options for the Media Library and all the medias attached into the form
 */

import Vue from 'vue'
import cloneDeep from 'lodash/cloneDeep'
import { MEDIA_LIBRARY } from '../mutations'
import ACTIONS from '@/store/actions'

const state = {
  /**
   * An object of all crops available for cropper component configuration
   * @type {Object}
   */
  crops: window[process.env.VUE_APP_NAME].STORE.medias.crops || {},
  /**
   * Display the file name of images
   * @type {Object}
   */
  showFileName: window[process.env.VUE_APP_NAME].STORE.medias.showFileName || false,
  /**
   * Define types available in medias library
   * @type {Array.<string>}
   */
  types: window[process.env.VUE_APP_NAME].STORE.medias.types || [],
  /**
   * Current type of media library
   * @type {string}
   */
  type: 'image',
  /**
   * Connector is used to save media by usage (eg. cover, image, profile...)
   * @type {string}
   */
  connector: null,
  /**
   * Define the max of medias that can be select from the media libray
   * @type {number}
   */
  max: 0,
  /**
   * Define the maximum filesize allowed to attach in a field from the media library
   * @type {number}
   */
  filesizeMax: 0,
  /**
   * Define the min image width allowed to attach in a field from the media library
   * @type {number}
   */
  widthMin: 0,
  /**
   * Define the min image height allowed to attach in a field from the media library
   * @type {number}
   */
  heightMin: 0,
  /**
   * Restrict the media library navigation between type
   * @type {Boolean}
   */
  strict: true,
  /**
   * An object of selected medias by usage (connector)
   * @type {Object.<string,Object>}
   */
  selected: window[process.env.VUE_APP_NAME].STORE.medias.selected || {},
  /**
   * An array of current uploading medias. When upload is ended, array is reset
   * @type {Array}
   */
  loading: [],
  /**
   * The progress value of an upload. When upload is ended, this value is reset to 0.
   * @type {number}
   */
  uploadProgress: 0,
  /**
   * An index used when mediaLibrary is open to replace a file
   * @type {number}
   */
  indexToReplace: -1
}

// getters
const getters = {
  mediasByBlockId: (state) => (id) => {
    const ids = Object.keys(state.selected).filter(key => key.startsWith(`blocks[${id}]`))
    const medias = {}
    ids.forEach(id => (medias[id] = state.selected[id]))
    return medias
  }
}

const mutations = {
  [MEDIA_LIBRARY.UPDATE_MEDIA_TYPE_TOTAL] (state, type) {
    state.types = state.types.map(t => {
      if (t.value === type.type) t.total = type.total
      return t
    })
  },
  [MEDIA_LIBRARY.UPDATE_REPLACE_INDEX] (state, index) {
    state.indexToReplace = index
  },
  [MEDIA_LIBRARY.INCREMENT_MEDIA_TYPE_TOTAL] (state, type) {
    state.types = state.types.map(t => {
      if (t.value === type) t.total = t.total + 1
      return t
    })
  },
  [MEDIA_LIBRARY.DECREMENT_MEDIA_TYPE_TOTAL] (state, type) {
    state.types = state.types.map(t => {
      if (t.value === type) t.total = t.total - 1
      return t
    })
  },
  [MEDIA_LIBRARY.UPDATE_MEDIAS] (state, { mediaRole, index, media }) {
    Vue.set(
      state.selected[mediaRole],
      index,
      media
    )
  },
  [MEDIA_LIBRARY.SAVE_MEDIAS] (state, medias) {
    if (state.connector) {
      const key = state.connector
      const existedSelectedConnector = state.selected[key] && state.selected[key].length
      if (existedSelectedConnector && state.indexToReplace > -1) {
        // Replace mode
        state.selected[key].splice(state.indexToReplace, 1, cloneDeep(medias[0]))
      } else if (existedSelectedConnector) {
        // Add mode
        medias.forEach(function (media) {
          state.selected[key].push(cloneDeep(media))
        })
      } else {
        // Create mode
        const newMedias = {}
        newMedias[key] = medias
        state.selected = Object.assign({}, state.selected, newMedias)
      }

      state.indexToReplace = -1
    }
  },
  [MEDIA_LIBRARY.DESTROY_SPECIFIC_MEDIA] (state, media) {
    if (state.selected[media.name]) {
      state.selected[media.name].splice(media.index, 1)
      if (state.selected[media.name].length === 0) Vue.delete(state.selected, media.name)
    }

    state.connector = null
  },
  [MEDIA_LIBRARY.DESTROY_MEDIAS] (state, connector) {
    if (state.selected[connector]) Vue.delete(state.selected, connector)

    state.connector = null
  },
  [MEDIA_LIBRARY.REORDER_MEDIAS] (state, newValues) {
    const newMedias = {}
    newMedias[newValues.name] = newValues.medias
    state.selected = Object.assign({}, state.selected, newMedias)
  },
  [MEDIA_LIBRARY.PROGRESS_UPLOAD_MEDIA] (state, media) {
    const mediaToUpdate = state.loading.filter((m) => m.id === media.id)
    // Update existing form field
    if (mediaToUpdate.length) {
      mediaToUpdate[0].error = false
      mediaToUpdate[0].progress = media.progress
    } else {
      state.loading.unshift({
        id: media.id,
        name: media.name,
        progress: media.progress,
        replacementId: media.replacementId,
        isReplacement: media.isReplacement
      })
    }
  },
  [MEDIA_LIBRARY.PROGRESS_UPLOAD] (state, uploadProgress) {
    state.uploadProgress = uploadProgress
  },
  [MEDIA_LIBRARY.DONE_UPLOAD_MEDIA] (state, media) {
    state.loading.forEach(function (m, index) {
      if (m.id === media.id) state.loading.splice(index, 1)
    })
  },
  [MEDIA_LIBRARY.ERROR_UPLOAD_MEDIA] (state, media) {
    state.loading.forEach(function (m, index) {
      if (m.id === media.id) {
        Vue.set(state.loading[index], 'progress', 0)
        Vue.set(state.loading[index], 'error', true)
        Vue.set(state.loading[index], 'errorMessage', media.errorMessage)
      }
    })
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_CONNECTOR] (state, newValue) {
    if (newValue && newValue !== '') state.connector = newValue
    else state.connector = null
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_MODE] (state, newValue) {
    state.strict = newValue
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_TYPE] (state, newValue) {
    if (newValue && newValue !== '') state.type = newValue
  },
  [MEDIA_LIBRARY.RESET_MEDIA_TYPE] (state) {
    state.type = state.types[0].value
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_MAX] (state, newValue) {
    state.max = Math.max(0, newValue)
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_FILESIZE_MAX] (state, newValue) {
    state.filesizeMax = Math.max(0, newValue)
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_WIDTH_MIN] (state, newValue) {
    state.widthMin = Math.max(0, newValue)
  },
  [MEDIA_LIBRARY.UPDATE_MEDIA_HEIGHT_MIN] (state, newValue) {
    state.heightMin = Math.max(0, newValue)
  },
  [MEDIA_LIBRARY.SET_MEDIA_METADATAS] (state, metadatas) {
    const connector = metadatas.media.context
    const medias = state.selected[connector]
    const newValue = metadatas.value

    // Save all the custom metadatas here (with or wthout localization)
    function setMetatadas (mediaToModify) {
      if (newValue.locale) {
        // if multi language we will fill an object
        if (!mediaToModify.metadatas.custom[newValue.id]) {
          mediaToModify.metadatas.custom[newValue.id] = {}
        }

        mediaToModify.metadatas.custom[newValue.id][newValue.locale] = newValue.value
      } else {
        mediaToModify.metadatas.custom[newValue.id] = newValue.value
      }

      return mediaToModify
    }

    if (metadatas.media.hasOwnProperty('index')) {
      const media = setMetatadas(cloneDeep(medias[metadatas.media.index]))
      Vue.set(medias, metadatas.media.index, media)
    }
  },
  [MEDIA_LIBRARY.DESTROY_MEDIA_CONNECTOR] (state) {
    state.connector = null
  },
  [MEDIA_LIBRARY.SET_MEDIA_CROP] (state, crop) {
    const key = crop.key
    const index = crop.index
    const media = state.selected[key][index]

    function addCrop (mediaToModify) {
      if (!mediaToModify.crops) mediaToModify.crops = {}

      // save all the crop variants to the media
      for (const variant in crop.values) {
        const newValues = {}
        newValues.name = crop.values[variant].name || variant
        newValues.x = crop.values[variant].x
        newValues.y = crop.values[variant].y
        newValues.width = crop.values[variant].width
        newValues.height = crop.values[variant].height

        mediaToModify.crops[variant] = newValues
      }

      return mediaToModify
    }

    const newMedia = addCrop(cloneDeep(media))
    Vue.set(state.selected[key], index, newMedia)
  },
  [MEDIA_LIBRARY.ADD_MEDIAS] (state, { medias }) {
    state.selected = Object.assign({}, state.selected, medias)
  }
}

const actions = {
  async [ACTIONS.DUPLICATE_BLOCK] ({ commit, getters }, { block, id }) {
    // copy medias and update with the provided id
    const medias = { ...getters.mediasByBlockId(block.id) }
    const mediaIds = Object.keys(medias)
    const duplicates = {}
    mediaIds.forEach(mediaId => (duplicates[mediaId.replace(block.id, id)] = [...medias[mediaId]]))

    commit(MEDIA_LIBRARY.ADD_MEDIAS, { medias: duplicates })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
