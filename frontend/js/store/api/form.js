import axios from 'axios'
import { globalError } from '@/utils/errors'

const component = 'FORM'

export default {
  get (endpoint, callback, errorCallback) {
    axios.get(endpoint).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('get request error.')
    })
  },
  post (endpoint, data, callback, errorCallback) {
    axios.post(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('post request error.')
    })
  },
  put (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('save request error.')
    })
  }
}
