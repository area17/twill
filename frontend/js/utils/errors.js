export function globalError (component = null, error) {
  let prefix = ''

  if (component && typeof component === 'string') {
    prefix = '[' + component + ']: '
  }

  const errorMessage = prefix + 'An error occured.\n' + error

  console.error(errorMessage)

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

  if (errorStatusMapping.hasOwnProperty(error.response.status)) {
    window.vm.notif(errorStatusMapping[error.response.status])
  }
}
