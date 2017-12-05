import axios from 'axios'

export default {
  save (endpoint, data, callback) {
    axios.put(endpoint, data).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      console.warn('save request error.')
    })
  }
}
