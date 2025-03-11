const state = {
  groups: window[process.env.VUE_APP_NAME].STORE.groups || [],
  groupUserMapping: window[process.env.VUE_APP_NAME].STORE.groupUserMapping || [],
  hasEditPermissions: window[process.env.VUE_APP_NAME].STORE.permissions.hasEditPermissions || false
}

export default {
  state
}
