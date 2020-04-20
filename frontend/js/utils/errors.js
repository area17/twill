export function globalError (component = null, error = { message: '', value: null }) {
  let prefix = ''

  if (component && typeof component === 'string') {
    prefix = `${process.env.VUE_APP_NAME} - [${component}]: `
  }

  const errorMessage = prefix + 'An error occured.\n' + error.message
  console.error(errorMessage)
  if (error.value) {
    console.error(error.value)
  }

  const errorStatusMapping = {
    401: {
      message: 'Your session has expired, please <a href="' + document.location + '" target="_blank">login in another tab</a>. You can then continue working here.',
      variant: 'warning'
    },
    403: {
      message: 'You don\'t have permission to perform this action.',
      variant: 'warning'
    }
  }

  if ('response' in error.value && 'status' in error.value.response && errorStatusMapping.hasOwnProperty(error.value.response.status)) {
    window[process.env.VUE_APP_NAME].vm.notif(errorStatusMapping[error.value.response.status])
  }
}
