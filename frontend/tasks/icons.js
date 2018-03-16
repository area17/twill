const SVGO = require('svgo')
const svgstore = require('svgstore')
const path = require('path')
const fs = require('fs')
const createLogger = require('logging').default
const logger = createLogger('Icons')

const iconPaths = [
  {
    path: path.resolve('frontend/icons'),
    destination: {
      svg: 'icons.svg',
      scss: '_icons.scss'
    },
    output: {
      sprite: svgstore(),
      icons: []
    }
  },
  {
    path: path.resolve('frontend/icons-files'),
    destination: {
      svg: 'icons-files.svg',
      scss: '_icons-files.scss'
    },
    output: {
      sprite: svgstore(),
      icons: []
    }
  }
]
const svgo = new SVGO()

/**
 *
 * @param items An array of items.
 * @param fn A function that accepts an item from the array and returns a promise.
 * @returns {Promise}
 */
function forEachPromise (items, fn, iconPath) {
  return items.reduce(function (promise, item) {
    return promise.then(function () {
      return fn(item, iconPath)
    })
  }, Promise.resolve())
}

function buildIcon (fileName, iconPath) {
  if (fileName === '.keep') { return false }
  if (fileName === '.DS_Store') { return false }
  const title = path.basename(fileName, '.svg')

  let file = fs.readFileSync(path.resolve(iconPath.path, fileName))
  return svgo.optimize(file).then(function (result) {
    if (result.error) logger.info('Icon error ' + fileName + '.svg : ', result.error)
    else {
      iconPath.output.icons.push(Object.assign({title}, result.info))
      iconPath.output.sprite.add(path.parse(fileName).name, result.data)
    }
  })
}

function storeSprite (iconPath) {
  const destination = path.resolve('public/assets/admin/icons', iconPath.destination.svg)

  fs.writeFileSync(destination, iconPath.output.sprite.toString())
  logger.info('Icons compiled to: ', destination)
}

function makeScssFile (iconPath) {
  const destination = path.resolve('frontend/scss', 'setup', iconPath.destination.scss)
  let scss = ''
  iconPath.output.icons.forEach(icon => {
    scss = scss.concat(`.icon--${icon.title}, .icon--${icon.title} svg { width: ${icon.width}px; height: ${icon.height}px }\n`)
  })

  fs.writeFileSync(destination, scss)
  logger.info('Icons SCSS file written at: ', destination)
}

function handleIconPaths () {
  iconPaths.forEach((iconPath) => {
    let files = fs.readdirSync(iconPath.path)
    forEachPromise(files, buildIcon, iconPath).then(() => {
      storeSprite(iconPath)
      makeScssFile(iconPath)
    })
  })
}

handleIconPaths()
