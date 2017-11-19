import axios from 'axios'
import { replaceState } from '@/utils/pushState.js'

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

    axios.get(window.CMS_URLS.index, { params: params }).then(function (resp) {
      if (resp.data.replaceUrl) {
        const url = resp.request.responseURL
        replaceState(url)
      }

      if (callback && typeof callback === 'function') {
      // update data, nav and max page
        callback({
          data: resp.data.tableData ? resp.data.tableData : [],
          nav: resp.data.tableMainFilters ? resp.data.tableMainFilters : [],
          maxPage: (resp.data.maxPage ? resp.data.maxPage : 1)
        })
      }
    }, function (resp) {
      // error callback
    })
  },

  togglePublished (row, callback) {
    axios.put(window.CMS_URLS.publish, { id: row.id, active: row.published }).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  },

  delete (row, callback) {
    axios.delete(row.delete).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  },
  restore (row, callback) {
    axios.put(window.CMS_URLS.restore, { id: row.id }).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      // error callback
    })
  },

  reorder (ids, callback) {
    axios.post(window.CMS_URLS.reorder, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback()
    }, function (resp) {
      console.log('reorder request error.')
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

  toggleFeatured (row, callback) {
    // Params
    //
    // id : id of the item to toggle

    axios.put(window.CMS_URLS.feature, { id: row.id, active: row.featured }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(row.id)
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
