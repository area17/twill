export const loadScript = (id, src, type) => {
  return new Promise((resolve, reject) => {
    let script = document.getElementById(id)

    const done = () => {
      script.removeEventListener('load', done)
      resolve()
    }

    if (script) {
      script.addEventListener('load', done)
    } else {
      script = document.createElement('script')
      script.setAttribute('id', id)
      script.type = type
      script.onload = done
      script.onerror = reject
      document.getElementsByTagName('head')[0].appendChild(script)
      script.src = src
    }
  })
}

export const loadStyle = (id, href) => {
  let link = document.getElementById(id)
  if (link) return

  link = document.createElement('link')
  link.script.setAttribute('id', id)
  link.rel = 'stylesheet'
  link.type = 'text/css'
  link.href = href
}

export default {
  loadScript
}
