import axios from 'axios'
import { globalError } from '@/utils/errors'

export default {
  getBlockPreview (endpoint, data, callback, errorCallback) {
    axios.post(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      const error = {
        message: 'Block preview request error.',
        value: resp
      }
      globalError('CONTENT', error)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  }
}
