import axios from 'axios'
import { globalError } from '@/utils/errors'

export default {
  getBlockPreview (endpoint, data, callback, errorCallback) {
    axios.post(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      globalError('CONTENT', resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('block preview request error.')
    })
  }
}
