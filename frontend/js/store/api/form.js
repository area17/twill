import axios from 'axios'

export default {
  save (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('save request error.')
    })
  }
}
