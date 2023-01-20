import axios from 'axios'

import { globalError } from '@/utils/errors'

export default {
  getRevisionContent (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      const error = {
        message: 'Preview request error.',
        value: resp
      }
      globalError('REVISION', error)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
    })
  }
}
