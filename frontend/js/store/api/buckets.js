import axios from 'axios'
import { getURLWithoutQuery } from '@/utils/pushState.js'

export default {

  get: function (params, callback) {
    axios.get(getURLWithoutQuery(), {params: params}).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      // error callback
    })
  },

  save (endpoint, params, callback, errorCallback) {
    axios.post(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('save request error.')
    })
  }
}
