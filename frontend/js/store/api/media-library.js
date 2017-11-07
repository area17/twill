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

  add (media, callback) {
    // Params
    //
    // media : full media datas

    // Set endpoint in global config
    axios.put('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { media: media }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(media)
    }, function (resp) {
      // error callback
    })
  }
}
