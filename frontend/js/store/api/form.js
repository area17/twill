import axios from 'axios'

export default {
  get (endpoint, callback, errorCallback) {
    axios.get(endpoint).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('get request error.')
    })
  },
  post (endpoint, data, callback, errorCallback) {
    axios.post(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('post request error.')
    })
  },
  save (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('save request error.')
    })
  }
}
