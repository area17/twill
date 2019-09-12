export default ({ Vue }) => {
  Vue.mixin({
    computed: {
      $title () {
        return 'Documentation – Twill'
      }
    }
  })
}
