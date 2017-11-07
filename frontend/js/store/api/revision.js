import axios from 'axios'

export default {
  getRevisionContent (id, callback) {
    // Params
    //
    // id : uniq id of the revision

    // Set endpoint in global config
    axios.get('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { id: id }).then(function (resp) {
      // new full html content
      const _newData = '<html><body style="background:white; font-family:Arial"><p style="padding:10vw"><strong>Revision ' + id + '</strong> <a href="#">Clicking me should not do anything</a></p><p style="padding:10vw">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p></body></html>'

      if (callback && typeof callback === 'function') callback(_newData)
    }, function (resp) {
      // error callback
    })
  },

  getCurrentContent (param, callback) {
    // Params
    //
    // param : TODO to define

    // Set endpoint in global config
    axios.get('https://www.mocky.io/v2/59d77e61120000ce04cb1c5b', { param: param }).then(function (resp) {
      // new full html content
      const _newData = '<html><body style="background:white; font-family:Arial"><p style="padding:10vw"><strong>CURRENT CONTENT</strong> <a href="#">Clicking me should not do anything</a></p><p style="padding:10vw">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p><p style="padding:10vw">Hello world!</p></body></html>'

      if (callback && typeof callback === 'function') callback(_newData)
    }, function (resp) {
      // error callback
    })
  }
}
