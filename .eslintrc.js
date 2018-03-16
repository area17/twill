// http://eslint.org/docs/user-guide/configuring

module.exports = {
  root: true,
  parserOptions: {
    parser: 'babel-eslint',
    sourceType: 'module'
  },
  env: {
    browser: true,
  },
  // https://github.com/standard/standard
  extends: [
    'standard',
    'plugin:vue/essential'
  ],
  // required to lint *.vue files
  plugins: [
    'vue'
  ],
  // add your custom rules here
  'rules': {
    // v-key in vue v-for loop
    'vue/require-v-for-key': 'off',
    // indent
    'indent': ['warn', 2],
    'vue/script-indent': ['warn', 2, {
      'baseIndent': 1
    }],
    'no-useless-escape': 0,
    // allow paren-less arrow functions
    'arrow-parens': 0,
    // allow async-await
    'generator-star-spacing': 0,
    // allow debugger during development
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0
  },
	overrides: [
		{
			files: ["*.vue"],
			rules: {
				"indent": "off",
				"vue/script-indent": ['warn', 2, {
          'baseIndent': 1
        }]
			}
		}
	]
}
