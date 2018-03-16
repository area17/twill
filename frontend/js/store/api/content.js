import axios from 'axios'

export default {
  getBlockPreview (endpoint, data, callback, errorCallback) {
    axios.post(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('block preview request error.')
    })
  }
}
