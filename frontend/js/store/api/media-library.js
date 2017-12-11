import axios from 'axios'

export default {

  get (endpoint, params, callback) {
    // Params
    //
    // Form datas : query, page

    // Set endpoint in global config
    axios.get(endpoint, { params: params }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
    })
  },

  update (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
    })
  },

  delete (endpoint, callback) {
    axios.delete(endpoint).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
    })
  },

  bulkDelete (endpoint, params, callback) {
    axios.put(endpoint, params).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      // error callback
    })
  }
}
