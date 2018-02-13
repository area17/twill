import Vue from 'vue'
import * as types from '../mutation-types'

const state = {
  /**
   * An object of all crops available for cropper component configuration
   * @type {Object}
   */
  crops: window.STORE.medias.crops || {},
  /**
   * Define types available in medias library
   * @type {Array.<string>}
   */
  types: window.STORE.medias.types || [],
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
   * Restrict the media library navigation between type
   * @type {Boolean}
   */
  strict: true,
  /**
   * An object of selected medias by usage (connector)
   * @type {Object.<string,Object>}
   */
  selected: window.STORE.medias.selected || {},
  /**
   * An array of current uploading medias. When upload is ended, array is reset
   * @type {Array}
   */
  loading: [],
  /**
   * The endpoint to get, add and validate tags on the fly (ajax)
   * @type {string}
   */
  tagsEndpoint: window.STORE.medias.tagsEndpoint || '',
  /**
   * An Object to configure FineUploader
   * @type {Object}
   * @see https://docs.fineuploader.com/
   */
  uploaderConfig: window.STORE.medias.uploaderConfig || {}
}

// getters
const getters = {}

const mutations = {
  [types.UPDATE_MEDIA_TYPE_TOTAL] (state, type) {
    state.types = state.types.map(t => {
      if (t.value === type.type) t.total = type.total
      return t
    })
  },
  [types.INCREMENT_MEDIA_TYPE_TOTAL] (state, type) {
    state.types = state.types.map(t => {
      if (t.value === type) t.total = t.total + 1
      return t
    })
  },
  [types.DECREMENT_MEDIA_TYPE_TOTAL] (state, type) {
    state.types = state.types.map(t => {
      if (t.value === type) t.total = t.total - 1
      return t
    })
  },
  [types.SAVE_MEDIAS] (state, medias) {
    if (state.connector) {
      // init crop values
      const crops = state.crops[state.connector]
      medias.forEach((media) => {
        if (media.hasOwnProperty('crops')) {
          return
        }

        media.crops = {}

        for (let crop in crops) {
          const ratio = crops[crop][0].ratio
          const width = media.width
          const height = media.height
          const center = {
            x: width / 2,
            y: height / 2
          }

          let cropWidth = 0
          let cropHeight = 0

          if (ratio < 1) { // "portrait" crop
            cropWidth = Math.floor(Math.min(height * ratio, width))
            cropHeight = Math.floor(cropWidth / ratio)
          } else { // "landscape" or square crop
            cropHeight = Math.floor(Math.min(width / ratio, height))
            cropWidth = Math.floor(cropHeight * ratio)
          }

          let x = Math.floor(center.x - cropWidth / 2)
          let y = Math.floor(center.y - cropHeight / 2)

          media.crops[crop] = {
            x: x,
            y: y,
            width: cropWidth,
            height: cropHeight,
            name: crops[crop][0].name
          }
        }
      })

      if (state.selected[state.connector] && state.selected[state.connector].length) {
        medias.forEach(function (media) {
          state.selected[state.connector].push(media)
        })
      } else {
        const newMedias = {}
        newMedias[state.connector] = medias
        state.selected = Object.assign({}, state.selected, newMedias)
      }
    }
  },
  [types.DESTROY_SPECIFIC_MEDIA] (state, media) {
    if (state.selected[media.name]) {
      state.selected[media.name].splice(media.index, 1)
      if (state.selected[media.name].length === 0) Vue.delete(state.selected, media.name)
    }

    state.connector = null
  },
  [types.DESTROY_MEDIAS] (state, connector) {
    if (state.selected[connector]) Vue.delete(state.selected, connector)

    state.connector = null
  },
  [types.REORDER_MEDIAS] (state, newValues) {
    const newMedias = {}
    newMedias[newValues.name] = newValues.medias
    state.selected = Object.assign({}, state.selected, newMedias)
  },
  [types.PROGRESS_UPLOAD_MEDIA] (state, media) {
    const mediaToUpdate = state.loading.filter(function (m) {
      return m.id === media.id
    })

    // Update existing form field
    if (mediaToUpdate.length) {
      mediaToUpdate[0].error = false
      mediaToUpdate[0].progress = media.progress
    } else {
      state.loading.unshift({
        id: media.id,
        name: media.name,
        progress: media.progress
      })
    }
  },
  [types.DONE_UPLOAD_MEDIA] (state, media) {
    state.loading.forEach(function (m, index) {
      if (m.id === media.id) state.loading.splice(index, 1)
    })
  },
  [types.ERROR_UPLOAD_MEDIA] (state, media) {
    state.loading.forEach(function (m, index) {
      if (m.id === media.id) {
        Vue.set(state.loading[index], 'progress', 0)
        Vue.set(state.loading[index], 'error', true)
        Vue.set(state.loading[index], 'errorMessage', media.errorMessage)
      }
    })
  },
  [types.UPDATE_MEDIA_CONNECTOR] (state, newValue) {
    if (newValue && newValue !== '') state.connector = newValue
    else state.connector = null
  },
  [types.UPDATE_MEDIA_MODE] (state, newValue) {
    state.strict = newValue
  },
  [types.UPDATE_MEDIA_TYPE] (state, newValue) {
    if (newValue && newValue !== '') state.type = newValue
  },
  [types.UPDATE_MEDIA_MAX] (state, newValue) {
    state.max = Math.max(0, newValue)
  },
  [types.SET_MEDIA_METADATAS] (state, metadatas) {
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
      const media = setMetatadas(medias[metadatas.media.index])
      medias[metadatas.index] = Object.assign({}, medias[metadatas.index], media)
    }
  },
  [types.DESTROY_MEDIA_CONNECTOR] (state) {
    state.connector = null
  },

  [types.SET_MEDIA_CROP] (state, crop) {
    const key = crop.key
    const index = crop.index
    const media = state.selected[key][index]

    function addCrop (mediaToModify) {
      if (!mediaToModify.crops) mediaToModify.crops = {}

      // save all the crop variants to the media
      for (let variant in crop.values) {
        let newValues = {}
        newValues.name = crop.values[variant].name || variant
        newValues.x = crop.values[variant].x
        newValues.y = crop.values[variant].y
        newValues.width = crop.values[variant].width
        newValues.height = crop.values[variant].height

        mediaToModify.crops[variant] = newValues
      }

      return mediaToModify
    }

    const newMedia = addCrop(media)
    state.selected[key].splice(index, 1)
    state.selected[key].splice(index, 0, Object.assign({}, newMedia, media))
  }
}

export default {
  state,
  getters,
  mutations
}
