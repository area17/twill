export function globalError (component = null, error) {
  let prefix = ''

  if (component && typeof component === 'string') {
    prefix = `${process.env.VUE_APP_NAME} - [' + component + ']: `
  }

  const errorMessage = prefix + 'An error occured.\n' + error

  console.error(errorMessage)

  if (error.response.status === 401) {
    window[process.env.VUE_APP_NAME].vm.notif({
      message: 'Your session has expired, please <a href="' + document.location + '" target="_blank">login in another tab</a>. You can then continue working here.',
      variant: 'warning'
    })
  }
}
