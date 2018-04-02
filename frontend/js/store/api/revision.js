import axios from 'axios'
import { globalError } from '@/utils/errors'

export default {
  getRevisionContent (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      globalError('REVISION', resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('preview request error.')
    })
  }
}
