import axios from 'axios'
import { getURLWithoutQuery } from '@/utils/pushState.js'

export default {

  get: function (params, callback) {
    axios.get(getURLWithoutQuery(), {params: params}).then(function (resp) {
      callback(resp.data)
    }, function (resp) {
      // error callback
    })
  },

  add: function (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  },

  reorder (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  },

  delete (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  },

  replace (endpoint, params, callback) {
    axios.get(endpoint, params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  }
}
