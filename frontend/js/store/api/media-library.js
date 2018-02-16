import axios from 'axios'
import { globalError } from '@/utils/errors'

const component = 'MEDIA-LIBRARY'

export default {

  get (endpoint, params, callback, errorCallback) {
    // Params
    //
    // Form datas : query, page

    // Set endpoint in global config
    axios.get(endpoint, { params: params }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  },

  update (endpoint, params, callback, errorCallback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  },

  delete (endpoint, callback, errorCallback) {
    axios.delete(endpoint).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  },

  bulkDelete (endpoint, params, callback, errorCallback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  }
}
