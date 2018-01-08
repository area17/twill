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

  add: function (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  },

  toggleFeatured (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  },

  reorder (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  },

  delete (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  }
}
