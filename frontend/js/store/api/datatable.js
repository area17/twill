import axios from 'axios'

const _data = [
  {
    id: 1,
    featured: true,
    published: true,
    name: 'The New School Website',
    client: 'The New School',
    studio: 'New York',
    status: 'In use',
    capabilities: 'Optimization',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=1',
    permalink: 'https://pentagram.com'
  },
  {
    id: 2,
    featured: true,
    published: true,
    name: 'Barnes Foundation website',
    client: 'Barnes Foundation',
    studio: 'Philadephia',
    status: 'In use',
    capabilities: 'Strategy',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=2',
    permalink: 'https://pentagram.com'
  },
  {
    id: 3,
    featured: false,
    published: false,
    name: 'Pentagram website',
    client: 'Pentagram',
    studio: 'New York',
    status: 'In use',
    capabilities: 'Enginering',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=3',
    permalink: 'https://pentagram.com'
  },
  {
    id: 4,
    featured: false,
    published: false,
    name: 'Mai 36 Galerie website',
    client: 'Mai 36 Galerie',
    studio: 'Paris',
    status: 'In use',
    capabilities: 'Experience',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=4',
    permalink: 'https://pentagram.com'
  },
  {
    id: 5,
    featured: false,
    published: false,
    name: 'Mai 36 Galerie website',
    client: 'Mai 36 Galerie',
    studio: 'Paris',
    status: 'In use',
    capabilities: 'Experience',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=5',
    permalink: 'https://pentagram.com'
  },
  {
    id: 6,
    featured: false,
    published: false,
    name: 'Roto website',
    client: 'Roto',
    studio: 'New York',
    status: 'In use',
    capabilities: 'Experience',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=6',
    permalink: 'https://pentagram.com'
  },
  {
    id: 7,
    featured: false,
    published: true,
    name: 'THG Paris website',
    client: 'THG',
    studio: 'Paris',
    status: 'In use',
    capabilities: 'Strategy',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=7',
    permalink: 'https://pentagram.com'
  },
  {
    id: 8,
    featured: false,
    published: true,
    name: 'La Parqueterie Nouvelle strategie',
    client: 'La Parqueterie Nouvelle',
    studio: 'Paris',
    status: 'In use',
    capabilities: 'Strategy',
    industry: 'Service',
    edit: 'http://pentagram.com',
    thumbnail: 'https://source.unsplash.com/random/80x80?sig=8',
    permalink: 'https://pentagram.com'
  }
]

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

    // Set endpoint in global config
    axios.get('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { params: params }).then(function (resp) {
      // update data and max page
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

  reorder (params) {
    // Todo : Do ajax here for reorder
    // Should only send ids of position ?
  },

  togglePublished (id, callback) {
    // Params
    //
    // id : id of the item to toggle

    // Set endpoint in global config  https://github.com/axios/axios#axiosposturl-data-config-1
    axios.put('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { id: id }).then(function (resp) {
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
    axios.put('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { ids: params.ids, status: params.toPublish }).then(function (resp) {
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
    axios.put('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { id: id }).then(function (resp) {
      if (callback && typeof callback === 'function') callback(id)
    }, function (resp) {
      // error callback
    })
  },

  bulkFeature (ids, callback) {
    // Set endpoint in global config https://github.com/axios/axios#axiosposturl-data-config-1
    axios.put('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { ids: ids }).then(function (resp) {
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
    axios.get('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { params: params }).then(function (resp) {
      // update data and max page
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
    axios.get('http://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { params: params }).then(function (resp) {
      // update data and max page
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
