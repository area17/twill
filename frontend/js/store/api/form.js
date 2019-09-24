import axios from 'axios'
import { globalError } from '@/utils/errors'

const component = 'FORM'

export default {
  get (endpoint, callback, errorCallback) {
    axios.get(endpoint).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = `Get request error.\n${resp}`
      globalError(component, error)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  },
  post (endpoint, data, callback, errorCallback) {
    axios.post(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = `Post request error.\n${resp}`
      globalError(component, error)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  },
  put (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = `Save request error.\n${resp}`
      globalError(component, error)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  }
}
