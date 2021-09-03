export default ({ Vue }) => {
  Vue.mixin({
    computed: {
      $title () {
        return this.$page.title + ' – Twill'
      }
    }
  })
}
