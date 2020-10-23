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
  * filter: the current navigation ('all', 'mine', 'published', 'draft', 'trash')
  *
  */
  get (params, callback) {
    axios.get(window[process.env.VUE_APP_NAME].CMS_URLS.index, { params: params }).then(function (resp) {
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
      const error = {
        message: 'Get request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  togglePublished (row, callback, errorCallback) {
    axios.put(window[process.env.VUE_APP_NAME].CMS_URLS.publish, { id: row.id, active: row.published }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Publish request error.',
        value: resp
      }
      globalError(component, error)
      if (errorCallback && typeof errorCallback === 'function') errorCallback(resp.response)
    })
  },

  toggleFeatured (row, callback) {
    axios.put(window[process.env.VUE_APP_NAME].CMS_URLS.feature, { id: row.id, active: row.featured }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Feature request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  delete (row, callback) {
    axios.delete(row.delete).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Delete request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  restore (row, callback) {
    axios.put(window[process.env.VUE_APP_NAME].CMS_URLS.restore, { id: row.id }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Restore request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  destroy (row, callback) {
    axios.put(window[process.env.VUE_APP_NAME].CMS_URLS.forceDelete, { id: row.id }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Destroy request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  duplicate (row, callback) {
    axios.put(row.duplicate).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Duplicate request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  reorder (ids, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.reorder, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Reorder request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  bulkPublish (params, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.bulkPublish, { ids: params.ids, publish: params.toPublish }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Bulk publish request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  bulkFeature (params, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.bulkFeature, { ids: params.ids, feature: params.toFeature }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Bulk feature request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  bulkDelete (ids, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.bulkDelete, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Bulk delete request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  export (row, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.export, { ids: row.id, responseType: 'blob' }).then(function (resp) {
      const blob = new Blob([resp.data], { type: 'application/vnd.ms-excel' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = 'Export table.xlsx'
      a.click()
      window.URL.revokeObjectURL(url)
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Export request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  bulkExport (ids, callback) {
    const config = {
      // headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      responseType: 'blob',
      headers: {
        Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.bulkExport, { ids: ids }, config).then(function (resp) {
    // axios.post('/storage/contacts_20201023095616.xlsx', { ids: ids }, { responseType: 'blob' }).then(function (resp) {
      const type = resp.headers['content-type']
      const blob = new Blob([resp.data], { type: type, encoding: 'UTF-8' })
      console.log(blob)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = 'Contacts_Export.xlsx'
      a.click()
      window.URL.revokeObjectURL(url)
      console.log(resp)
      // const blob = new Blob([resp.data.data], {
      //   type: 'application/csv'
      // })
      // const filename = 'Contacts_Export.csv'
      // if (typeof window.navigator.msSaveBlob !== 'undefined') {
      //   // IE workaround for "HTML7007: One or more blob URLs were
      //   // revoked by closing the blob for which they were created.
      //   // These URLs will no longer resolve as the data backing
      //   // the URL has been freed."
      //   window.navigator.msSaveBlob(blob, filename)
      // } else {
      //   const blobURL = window.URL.createObjectURL(blob)
      //   const tempLink = document.createElement('a')
      //   tempLink.style.display = 'none'
      //   tempLink.href = blobURL
      //   tempLink.download = filename
      //   tempLink.click()
      //   window.URL.revokeObjectURL(blobURL)
      // }
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Bulk export request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  bulkRestore (ids, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.bulkRestore, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Bulk restore request error.',
        value: resp
      }
      globalError(component, error)
    })
  },

  bulkDestroy (ids, callback) {
    axios.post(window[process.env.VUE_APP_NAME].CMS_URLS.bulkForceDelete, { ids: ids }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(resp)
    }, function (resp) {
      const error = {
        message: 'Bulk destroy request error.',
        value: resp
      }
      globalError(component, error)
    })
  }
}
