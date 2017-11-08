import axios from 'axios'

const _data = [
  {
    content_type: {
      label: 'Projects',
      value: 'projects'
    },
    items: [
      {
        id: 1,
        name: 'The New School Website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=1',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 2,
        name: 'Barnes Foundation website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=2',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 3,
        name: 'Pentagram website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=3',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 4,
        name: 'Mai 36 Galerie website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=4',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 5,
        name: 'Mai 36 Galerie website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=5',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 6,
        name: 'Roto website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=6',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 7,
        name: 'THG Paris website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=7',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      },
      {
        id: 8,
        name: 'La Parqueterie Nouvelle strategie',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=8',
        publication: '20/02/2017',
        content_type: {
          label: 'Projects',
          value: 'projects'
        }
      }
    ]
  },
  {
    content_type: {
      label: 'Teams Members',
      value: 'teams-members'
    },
    items: [
      {
        id: 4,
        name: 'Mai 36 Galerie website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=4',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 5,
        name: 'Mai 36 Galerie website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=5',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 6,
        name: 'Roto website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=6',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 1,
        name: 'The New School Website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=1',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 2,
        name: 'Barnes Foundation website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=2',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 3,
        name: 'Pentagram website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=3',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 7,
        name: 'THG Paris website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=7',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      },
      {
        id: 8,
        name: 'La Parqueterie Nouvelle strategie',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=8',
        publication: '20/02/2017',
        content_type: {
          label: 'Teams Members',
          value: 'teams-members'
        }
      }
    ]
  },
  {
    content_type: {
      label: 'Users',
      value: 'users'
    },
    items: [
      {
        id: 5,
        name: 'Mai 36 Galerie website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=5',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 6,
        name: 'Roto website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=6',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 7,
        name: 'THG Paris website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=7',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 8,
        name: 'La Parqueterie Nouvelle strategie',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=8',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 1,
        name: 'The New School Website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=1',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 2,
        name: 'Barnes Foundation website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=2',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 3,
        name: 'Pentagram website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=3',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      },
      {
        id: 4,
        name: 'Mai 36 Galerie website',
        edit: '/templates/form',
        thumbnail: 'https://source.unsplash.com/random/80x80?sig=4',
        publication: '20/02/2017',
        content_type: {
          label: 'Users',
          value: 'users'
        }
      }
    ]
  }]

function getDataByContentType (contentType) {
  return _data.filter(function (d) {
    return d.content_type.value === contentType
  })
}

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

  get: function (params, callback) {
    // DO REAL AJAX
    axios.get('#', {params: params}).then(function (resp) {
      const _newData = getDataByContentType(params.content_type)[0]
      _newData.items = shuffle(_newData.items)
      callback(_newData)
    }, function (resp) {
      // error callback
    })
  },

  add: function (params, callback) {
    // Set endpoint in global config (and use PUT instead of get here) https://github.com/axios/axios#axiosposturl-data-config-1
    axios.get('#', params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  },

  reorder (params, callback) {
    // DO AJAX HERE
    axios.get('#', params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  },

  delete (params, callback) {
    // Set endpoint in global config (and use PUT instead of get here) https://github.com/axios/axios#axiosposturl-data-config-1
    axios.get('#', params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  },

  replace (params, callback) {
    // Set endpoint in global config (and use PUT instead of get here) https://github.com/axios/axios#axiosposturl-data-config-1
    axios.get('#', params).then(function (resp) {
      callback()
    }, function (resp) {
      // error callback
    })
  }
}
