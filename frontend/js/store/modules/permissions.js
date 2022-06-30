const state = {
  groups: window[import.meta.env.VITE_APP_NAME].STORE.groups || [],
  groupUserMapping: window[import.meta.env.VITE_APP_NAME].STORE.groupUserMapping || []
}

export default {
  state
}
