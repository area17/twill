import axios from 'axios'

import { globalError } from '@/utils/errors'
import { getURLWithoutQuery } from '@/utils/pushState.js'

const component = 'BUCKETS'

export default {

  get: function (params, callback, errorCallback) {
    axios.get(getURLWithoutQuery(), {
      params
    })
      .then((resp) => {
        if (callback && typeof callback === 'function') callback(resp.data)
      })
      .catch((resp) => {
        const error = {
          message: 'Get Buckets error',
          value: resp
        }
        globalError(component, error)
        if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      })
  },

  save (endpoint, params, callback, errorCallback) {
    axios.post(endpoint, params)
      .then((resp) => {
        if (callback && typeof callback === 'function') callback(resp)
      })
      .catch((resp) => {
        // error callback
        const error = {
          message: 'Buckets save error.',
          value: resp
        }
        globalError(component, error)
        if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      })
  }
}
