const state = {
  groups: window[process.env.VUE_APP_NAME].STORE.groups || [],
  groupUserMapping: window[process.env.VUE_APP_NAME].STORE.groupUserMapping || []
}

export default {
  state
}
