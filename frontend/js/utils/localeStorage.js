export function localStoreSupport () {
  const mod = 'test'
  try {
    localStorage.setItem(mod, mod)
    localStorage.removeItem(mod)
    return true
  } catch (e) {
    return false
  }
}

export function setStorage (name, value) {
  const expires = ''

  if (!window.TWILL.debug) {
    if (localStoreSupport()) {
      localStorage.setItem(name, value)
    } else {
      document.cookie = name + '=' + value + expires + '; path=/'
    }
  }
}

export function getStorage (name) {
  if (window.TWILL.debug) {
    return null
  }
  if (localStoreSupport()) {
    return localStorage.getItem(name)
  } else {
    const cookieName = name + '='
    const ca = document.cookie.split(';')
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i]
      while (c.charAt(0) === ' ') c = c.substring(1, c.length)
      if (c.indexOf(cookieName) === 0) return c.substring(cookieName.length, c.length)
    }
    return null
  }
}
