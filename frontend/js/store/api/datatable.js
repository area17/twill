import axios from 'axios'
import { replaceState } from '@/utils/pushState.js'
import { globalError } from '@/utils/errors'

const component = 'DATATABLE'

export default {
  /*
  *
  * Main listing request with multiple params
  *
  * sortKey : column used for sorting content
  * sortDir : desc or asc
  * page : current page number
  * offset : number of items per page
  * columns: the set of visible columns
  * filter: the current navigation ("all", "mine", "published", "draft", "trash")
  *
  */
  get (params, callback) {
    axios.get(window.CMS_URLS.index, { params: params }).then(function (resp) {
      if (resp.data.replaceUrl) {
        const url = resp.request.responseURL
        replaceState(url)
      }

      if (callback && typeof callback === 'function') {
        const data = {
          data: resp.data.tableData ? resp.data.tableData : [],
          nav: resp.data.tableMainFilters ? resp.data.tableMainFilters : [],
          maxPage: (resp.data.maxPage ? resp.data.maxPage : 1)
        }

        callback(data)
      }
    }, function (resp) {
      globalError(component, resp)
      console.log('get request error.')
    })
  },

  togglePublished (row, callback, errorCallback) {
    axios.put(window.CMS_URLS.publish, { id: row.id, active: row.published }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp.response)
      console.log('publish request error.')
    })
  },

  toggleFeatured (row, callback) {
    axios.put(window.CMS_URLS.feature, { id: row.id, active: row.featured }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('feature request error.')
    })
  },

  delete (row, callback) {
    axios.delete(row.delete).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('delete request error.')
    })
  },

  restore (row, callback) {
    axios.put(window.CMS_URLS.restore, { id: row.id }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('restore request error.')
    })
  },

  reorder (ids, callback) {
    axios.post(window.CMS_URLS.reorder, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('reorder request error.')
    })
  },

  bulkPublish (params, callback) {
    axios.post(window.CMS_URLS.bulkPublish, { ids: params.ids, publish: params.toPublish }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('bulk publish request error.')
    })
  },

  bulkFeature (params, callback) {
    axios.post(window.CMS_URLS.bulkFeature, { ids: params.ids, feature: params.toFeature }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('bulk feature request error.')
    })
  },

  bulkDelete (ids, callback) {
    axios.post(window.CMS_URLS.bulkDelete, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('bulk delete request error.')
    })
  },

  bulkRestore (ids, callback) {
    axios.post(window.CMS_URLS.bulkRestore, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      globalError(component, resp)
      console.log('bulk restore request error.')
    })
  }
}
