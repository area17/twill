import axios from 'axios'

// Shuffle : for demo purpose only
function shuffle (a) {
  var j, x, i
  for (i = a.length - 1; i > 0; i--) {
    j = Math.floor(Math.random() * (i + 1))
    x = a[i]
    a[i] = a[j]
    a[j] = x
  }

  return a
}

export default {
  get (params, callback) {
    // Params
    //
    // sortKey : colmun used for sorting content
    // sortDir : desc or asc
    // page : current page number
    // offset : number of items per page
    // columns: the set of visible columns
    // filter: the current navigation ("all", "mine", "published", "draft", "trash")

    axios.get(window.location.href, { params: params }).then(function (resp) {
      // update data and max page
      const _data = resp.data.mappedData ? resp.data.mappedData : shuffle(window.STORE.datatable.data)

      if (callback && typeof callback === 'function') {
        callback({
          data: _data,
          maxPage: (resp.data.maxPage ? resp.data.maxPage : 10)
        })
      }
    }, function (resp) {
      // error callback
    })
  },

  reorder (params) {
    // Todo : Do ajax here for reorder
    // Should only send ids of position ?
  },

  togglePublished (id, published, callback) {
    // Params
    //
    // id : id of the item to toggle
    console.log({ id: id, active: published })
    // Set endpoint in global config  https://github.com/axios/axios#axiosposturl-data-config-1
    axios.put(window.CMS_URLS.publish, { id: id, active: published }).then(function (resp) {
      // todo : this need to be in the resp
      const navigation = [
        {
          name: 'Published',
          number: Math.round(Math.random() * 10)
        },
        {
          name: 'Draft',
          number: Math.round(Math.random() * 10)
        }
      ]

      if (callback && typeof callback === 'function') callback(id, navigation)
    }, function (resp) {
      // error callback
    })
  },

  bulkPublish (params, callback) {
    // Params
    //
    // ids : comma separated list of ids to publish/unpublish
    // status : boolean (publish or unpublish)

    // Set endpoint in global config https://github.com/axios/axios#axiosposturl-data-config-1
    axios.put('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { ids: params.ids, status: params.toPublish }).then(function (resp) {
      // todo : this need to be in the resp
      const navigation = [
        {
          name: 'Published',
          number: Math.round(Math.random() * 10)
        },
        {
          name: 'Draft',
          number: Math.round(Math.random() * 10)
        }
      ]

      if (callback && typeof callback === 'function') callback(params.ids, navigation)
    }, function (resp) {
      // error callback
    })
  },

  toggleFeatured (id, callback) {
    // Params
    //
    // id : id of the item to toggle

    // Set endpoint in global config https://github.com/axios/axios#axiosposturl-data-config-1
    axios.put('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { id: id }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(id)
    }, function (resp) {
      // error callback
    })
  },

  bulkFeature (ids, callback) {
    // Set endpoint in global config https://github.com/axios/axios#axiosposturl-data-config-1
    axios.put('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(ids)
    }, function (resp) {
      // error callback
    })
  },

  delete (params, callback) {
    // Params (same as get)
    //
    // sortKey : colmun used for sorting content
    // sortDir : desc or asc
    // page : current page number
    // offset : number of items per page
    // columns: the set of visible columns
    // filter: the current navigation ("all", "mine", "published", "draft", "trash")

    // + the following :
    // id : id of the item to delete

    // Set endpoint in global config and adjust setting using axios DELETE https://github.com/axios/axios#axiosdeleteurl-config-1
    axios.get('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { params: params }).then(function (resp) {
      // update data and max page
      const _data = window.STORE.datatable.data || []
      const _newData = shuffle(_data)

      if (callback && typeof callback === 'function') {
        callback({
          data: _newData,
          maxPage: 10 // maxPage need to be updated if needed
        })
      }
    }, function (resp) {
      // error callback
    })
  },

  bulkDelete (params, callback) {
    // Params (same as get)
    //
    // sortKey : colmun used for sorting content
    // sortDir : desc or asc
    // page : current page number
    // offset : number of items per page
    // columns: the set of visible columns
    // filter: the current navigation ("all", "mine", "published", "draft", "trash")

    // + the following :
    // ids : comma separated list of ids to delete

    // Set endpoint in global config https://github.com/axios/axios#axiosposturl-data-config-1
    axios.get('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { params: params }).then(function (resp) {
      // update data and max page
      const _data = window.STORE.datatable.data || []
      const _newData = shuffle(_data)

      if (callback && typeof callback === 'function') {
        callback({
          data: _newData,
          maxPage: 10 // maxPage need to be updated if needed
        })
      }
    }, function (resp) {
      // error callback
    })
  }
}
