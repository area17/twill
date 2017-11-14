const SVGO = require('svgo')
const svgstore = require('svgstore')
const path = require('path')
const fs = require('fs')
const createLogger = require('logging').default
const logger = createLogger('Icons')

const iconPath = path.resolve('frontend/icons-files')
const svgo = new SVGO()

let files = fs.readdirSync(iconPath)
let sprite = svgstore()
let icons = []
let scss = ''

/**
 *
 * @param items An array of items.
 * @param fn A function that accepts an item from the array and returns a promise.
 * @returns {Promise}
 */
function forEachPromise(items, fn) {
    return items.reduce(function (promise, item) {
        return promise.then(function () {
            return fn(item);
        });
    }, Promise.resolve());
}

forEachPromise(files, buildIcon).then(() => {
    storeSprite()
    makeScssFile()
});

function buildIcon(fileName) {
  if (fileName === '.keep') { return false }
  if (fileName === '.DS_Store') { return false }
  const title = path.basename(fileName, '.svg')

  let file = fs.readFileSync(path.resolve(iconPath, fileName))
  return svgo.optimize(file).then(function(result) {
    if(result.error) logger.info('Icon error '+ fileName +'.svg : ', result.error)
    else {
      icons.push(Object.assign({title}, result.info))
      sprite.add(path.parse(fileName).name, result.data)
    }
  })
}

function storeSprite () {
  const destination = path.resolve('public/assets/admin/icons', 'icons-files.svg')
  fs.writeFileSync(destination, sprite.toString())
  logger.info('Icons compiled to: ', destination)
}

function makeScssFile() {
  const destination = path.resolve('frontend/scss', 'setup', '_icons-files.scss')
  icons.forEach(icon => {
    scss = scss.concat(`.icon--${icon.title}, .icon--${icon.title} svg { width: ${icon.width}px; height: ${icon.height}px }\n`)
  })
  fs.writeFileSync(destination, scss)
  logger.info('Icons SCSS file written at: ', destination)
}

