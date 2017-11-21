import Vue from 'vue'
import * as types from '../mutation-types'

const state = {
  crops: window.STORE.medias.crops || {},
  types: window.STORE.medias.types || [],
  type: 'image',
  connector: null,
  max: 0,
  strict: true,
  selected: window.STORE.medias.selected || {},
  loading: [],
  tagsEndpoint: window.STORE.medias.tagsEndpoint || ''
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
  [types.SAVE_MEDIAS] (state, medias) {
    if (state.connector) {
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
      state.loading.push({
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

    // Save all the custom metadatas here
    function setMetatadas (mediaToModify) {
      for (let metadata in metadatas.values) {
        mediaToModify.metadatas.custom[metadata] = metadatas.values[metadata]
      }

      return mediaToModify
    }

    if (metadatas.hasOwnProperty('index')) {
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

    state.selected[key][index] = Object.assign({}, addCrop(media), media)
  }
}

export default {
  state,
  getters,
  mutations
}
