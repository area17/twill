export function globalError (component = null, error = { message: '', value: null }) {
  let prefix = ''

  if (component && typeof component === 'string') {
    prefix = `${process.env.VUE_APP_NAME} - [${component}]: `
  }

  const errorMessage = prefix + error.message

  const statusCode = error?.value?.response?.status ?? error?.response?.status ?? null

  console.error(errorMessage)

  if (error?.value && error.value?.response) {
    console.error(error.value.response?.data)
  }

  // Error 401 = session expired / not authenticated
  // Error 419 = CSRF token mismatched
  if (statusCode === 401 || statusCode === 419) {
    window[process.env.VUE_APP_NAME].vm.notif({
      message: 'Your session has expired, please <a href="' + document.location + '" target="_blank">login in another tab</a>. You can then continue working here.',
      variant: 'warning'
    })
  } else if (statusCode === 403) {
    window[process.env.VUE_APP_NAME].vm.notif({
      message: 'You don\'t have permission to perform this action.',
      variant: 'warning'
    })
  }
}
