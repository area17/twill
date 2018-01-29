import axios from 'axios'

export default {
  getRevisionContent (endpoint, revisionId, callback, errorCallback) {
    axios.put(endpoint, { revisionId: revisionId }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('preview request error.')
    })
  },

  getCurrentContent (endpoint, data, callback, errorCallback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp.data)
    }, function (resp) {
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp)
      console.warn('preview request error.')
    })
  }
}
