export function globalError (component = null, error) {
  let prefix = ''
  if (component && typeof component === 'string') {
    prefix = '[' + component + ']: '
  }
  const errorMessage = prefix + 'An error occured.\nError: ' + error
  console.error(errorMessage)
}
