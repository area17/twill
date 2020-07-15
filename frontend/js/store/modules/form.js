/**
 * Form
 *
 * Save all the fields of the form. Submit the form. Display errors.
 */

import api from '../api/form'
import { getFormData, getFormFields, getModalFormFields } from '@/utils/getFormData.js'
import { FORM, NOTIFICATION, LANGUAGE, ATTRIBUTES, PUBLICATION, REVISION } from '../mutations'
import ACTIONS from '@/store/actions'

const getFieldIndex = (stateKey, field) => {
  return stateKey.findIndex(f => f.name === field.name)
}

const state = {
  /**
   * Loading state of the form when submitting
   * @type {Boolean}
   */
  loading: false,
  /**
   * Type of submit : can be any value from state.publication.submitOptions
   * @type {String}
   */
  type: 'save',
  /**
   * Form errors after submitting
   * @type {Object}
   */
  baseUrl: window[process.env.VUE_APP_NAME].STORE.form.baseUrl || '',
  /**
   * All the fields that need to be saved
   * @type {Array}
   */
  fields: window[process.env.VUE_APP_NAME].STORE.form.fields || [],
  /**
   * All the fields that are in the create/edit modals (so these are not mixed with the form)
   * @type {Array}
   */
  modalFields: [],
  /**
   * Url to save/update the form
   * @type {String}
   */
  saveUrl: window[process.env.VUE_APP_NAME].STORE.form.saveUrl || '',
  /**
   * Url to get a full preview of the form datas
   * @type {String}
   */
  previewUrl: window[process.env.VUE_APP_NAME].STORE.form.previewUrl || '',
  /**
   * Url to restore previous form datas
   * @type {String}
   */
  restoreUrl: window[process.env.VUE_APP_NAME].STORE.form.restoreUrl || '',
  /**
   * Url to get only the preview of a block
   * @type {String}
   */
  blockPreviewUrl: window[process.env.VUE_APP_NAME].STORE.form.blockPreviewUrl || '',
  /**
   * Form errors after submitting
   * @type {Object}
   */
  errors: {},
  /**
   * Is this a custom form (that will let the browser submit the form instead of hooking up the submit event)
   * @type {Bookean}
   */
  isCustom: window[process.env.VUE_APP_NAME].STORE.form.isCustom || false,
  /**
   * Force reload on successful submit
   * @type {Bookean}
   */
  reloadOnSuccess: window[process.env.VUE_APP_NAME].STORE.form.reloadOnSuccess || false

}

// getters
const getters = {
  fieldsByName (state) {
    return name => state.fields.filter(function (field) {
      return field.name === name
    })
  },
  fieldValueByName: (state, getters) => name => { // want to use getters
    return getters.fieldsByName(name).length ? getters.fieldsByName(name)[0].value : ''
  },
  modalFieldsByName (state) {
    return name => state.modalFields.filter(function (field) {
      return field.name === name
    })
  },
  modalFieldValueByName: (state, getters) => name => { // want to use getters
    return getters.modalFieldsByName(name).length ? getters.modalFieldsByName(name)[0].value : ''
  }
}

const mutations = {
  [FORM.UPDATE_FORM_PERMALINK] (state, newValue) {
    if (newValue && newValue !== '') {
      state.permalink = newValue
    }
  },
  // ----------- Form fields ----------- //
  [FORM.EMPTY_FORM_FIELDS] (state, status) {
    state.fields = []
  },
  [FORM.REPLACE_FORM_FIELDS] (state, fields) {
    state.fields = fields
  },
  [FORM.UPDATE_FORM_FIELD] (state, field) {
    let fieldValue = field.locale ? {} : null
    const fieldIndex = getFieldIndex(state.fields, field)
    // Update existing form field
    if (fieldIndex !== -1) {
      if (field.locale) fieldValue = state.fields[fieldIndex].value || {}
      // remove existing field
      state.fields.splice(fieldIndex, 1)
    }

    if (field.locale) fieldValue[field.locale] = field.value
    else fieldValue = field.value

    state.fields.push({
      name: field.name,
      value: fieldValue
    })
  },
  [FORM.REMOVE_FORM_FIELD] (state, fieldName) {
    state.fields.forEach(function (field, index) {
      if (field.name === fieldName) state.fields.splice(index, 1)
    })
  },
  // ----------- Modal fields ----------- //
  [FORM.EMPTY_MODAL_FIELDS] (state, status) {
    state.modalFields = []
  },
  [FORM.REPLACE_MODAL_FIELDS] (state, fields) {
    state.modalFields = fields
  },
  [FORM.UPDATE_MODAL_FIELD] (state, field) {
    let fieldValue = field.locale ? {} : null
    const fieldIndex = getFieldIndex(state.modalFields, field)

    // Update existing form field
    if (fieldIndex !== -1) {
      if (field.locale) fieldValue = state.modalFields[fieldIndex].value
      // remove existing field
      state.modalFields.splice(fieldIndex, 1)
    }

    if (field.locale) fieldValue[field.locale] = field.value
    else fieldValue = field.value

    state.modalFields.push({
      name: field.name,
      value: fieldValue
    })
  },
  [FORM.REMOVE_MODAL_FIELD] (state, fieldName) {
    state.modalFields.forEach(function (field, index) {
      if (field.name === fieldName) state.modalFields.splice(index, 1)
    })
  },
  // ----------- Form errors and Loading ----------- //
  [FORM.UPDATE_FORM_LOADING] (state, loading) {
    state.loading = loading || !state.loading
  },
  [FORM.SET_FORM_ERRORS] (state, errors) {
    state.errors = errors
  },
  [FORM.CLEAR_FORM_ERRORS] (state) {
    state.errors = []
  },
  [FORM.UPDATE_FORM_SAVE_TYPE] (state, type) {
    state.type = type
  }
}

const actions = {
  [ACTIONS.REPLACE_FORM] ({ commit, state, getters, rootState }, endpoint) {
    return new Promise((resolve, reject) => {
      commit(FORM.CLEAR_FORM_ERRORS)
      commit(NOTIFICATION.CLEAR_NOTIF, 'error')

      api.get(endpoint, function (successResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)

        const data = successResponse.data

        if (data.hasOwnProperty('languages')) {
          commit(LANGUAGE.REPLACE_LANGUAGES, data.languages)
          delete data.languages
        }

        if (data.hasOwnProperty('revisions')) {
          commit(REVISION.UPDATE_REV_ALL, data.revisions)
          delete data.revisions
        }

        commit(FORM.REPLACE_FORM_FIELDS, data.fields)
        resolve()
      }, function (errorResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)
        commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
        reject(errorResponse)
      })
    })
  },
  [ACTIONS.UPDATE_FORM_IN_LISTING] ({ commit, state, getters, rootState }, options) {
    return new Promise((resolve, reject) => {
      commit(FORM.CLEAR_FORM_ERRORS)
      commit(NOTIFICATION.CLEAR_NOTIF, 'error')

      const data = Object.assign(getFormFields(rootState), {
        languages: rootState.language.all
      })

      api[options.method](options.endpoint, data, function (successResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)

        if (successResponse.data.hasOwnProperty('redirect') && options.redirect) {
          window.location.replace(successResponse.data.redirect)
        }

        commit(NOTIFICATION.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
        resolve()
      }, function (errorResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)
        commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
        commit(NOTIFICATION.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
        reject(errorResponse)
      })
    })
  },
  [ACTIONS.CREATE_FORM_IN_MODAL] ({ commit, state, getters, rootState }, options) {
    return new Promise((resolve, reject) => {
      commit(FORM.CLEAR_FORM_ERRORS)
      commit(NOTIFICATION.CLEAR_NOTIF, 'error')

      // Get modal fields
      const data = Object.assign(getModalFormFields(rootState), {
        languages: rootState.language.all
      })

      api[options.method](options.endpoint, data, function (successResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)

        // SuccessResponse much the newly created attributes as json
        commit(ATTRIBUTES.UPDATE_OPTIONS, {
          name: options.name,
          options: successResponse.data
        })

        // commit(NOTIFICATION.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
        resolve()
      }, function (errorResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)
        commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
        commit(NOTIFICATION.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
        reject(errorResponse)
      })
    })
  },
  [ACTIONS.SAVE_FORM] ({ commit, state, getters, rootState }, saveType) {
    commit(FORM.CLEAR_FORM_ERRORS)
    commit(NOTIFICATION.CLEAR_NOTIF, 'error')

    // update or create etc...
    commit(FORM.UPDATE_FORM_SAVE_TYPE, saveType)

    // we can now create our submitted data object out of:
    // - our just created fields object,
    // - publication properties
    // - selected medias and browsers
    // - created blocks and repeaters
    const data = getFormData(rootState)

    const method = rootState.publication.createWithoutModal ? 'post' : 'put'

    api[method](state.saveUrl, data, function (successResponse) {
      commit(FORM.UPDATE_FORM_LOADING, false)

      if (successResponse.data.hasOwnProperty('redirect')) {
        window.location.replace(successResponse.data.redirect)
      }

      if (state.reloadOnSuccess) {
        window.location.reload()
      }

      commit(NOTIFICATION.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
      commit(PUBLICATION.UPDATE_PUBLISH_SUBMIT)
      if (successResponse.data.hasOwnProperty('revisions')) {
        commit(REVISION.UPDATE_REV_ALL, successResponse.data.revisions)
      }
    }, function (errorResponse) {
      commit(FORM.UPDATE_FORM_LOADING, false)

      if (errorResponse.response.data.hasOwnProperty('exception')) {
        commit(NOTIFICATION.SET_NOTIF, { message: 'Your submission could not be processed.', variant: 'error' })
      } else {
        commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
        commit(NOTIFICATION.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
      }
    })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
