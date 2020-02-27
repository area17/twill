module.exports = {
  presets: [
    ['@vue/cli-plugin-babel/preset', {
      useBuiltIns: 'entry'
      // polyfills: [
      //   'es6.promise',
      //   'es6.symbol',
      //   'es6.array.find-index',
      //   'es6.array.iterator',
      //   'es6.function.name',
      //   'es6.number.constructor',
      //   'es6.object.assign',
      //   'es6.object.keys',
      //   'es6.regexp.constructor',
      //   'es6.regexp.match',
      //   'es6.regexp.replace',
      //   'es6.regexp.to-string',
      //   'es6.regexp.split',
      //   'es6.string.anchor',
      //   'es6.string.includes',
      //   'es6.string.starts-with',
      //   'es7.array.includes',
      //   'es7.object.get-own-property-descriptors'
      // ]
    }]
  ]
}
