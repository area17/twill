<template>
  <div :class="getThumbClasses">
    <a v-if="!row.hasOwnProperty('deleted')"
       :href="editUrl"
       @click="preventEditInPlace($event)">
      <span class="tablecell__thumb-letter">{{ getFirstLetter }}</span>
      <img :src="row[colName]"/>
    </a>
    <a v-else>
      <span class="tablecell__thumb-letter">{{ getFirstLetter }}</span>
      <img :src="row[colName]"/>
    </a>
  </div>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A17TableCellThumbNail',
    mixins: [TableCellMixin],
    computed: {
      getThumbClasses () {
        return [
          'tablecell__thumb',
          this.col.variation ? `tablecell__thumb--${this.col.variation}` : '',
          this.getBackgroundColor
        ]
      },
      getBackgroundColor () {
        // If it's not a rounded thumbnail, return.
        if (!this.col.variation) return

        // Init colors.
        const colors = ['orange', 'blue', 'purple', 'red']

        // Calculate indexColor.
        const indexColor = (this.row.id % colors.length)

        // Return background class.
        return `tablecell__thumb--background-${colors[indexColor]}`
      },
      getFirstLetter () {
        return this.row.name.charAt(0)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Thumbnails */

  .tablecell--thumb {
    width: 1px;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      padding-left: 0;
      padding-right: 0;
    }
  }

  .tablecell__thumb {
    display: block;
    background: $color__border--light;

    @include breakpoint(xsmall) { // no thumbnail on smaller screens
      display: none;
    }

    img {
      display: block;
      width: 80px;
      min-height: 80px;
      height: auto;
    }
  }

  .tablecell__thumb-letter {
    position: absolute;
    top: 1px;
    left: 1px;
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    justify-content: center;
    width: calc(100% - 2px);
    height: calc(100% - 2px);
    color: $color__background;
    border-radius: 50%;
  }

  /* Modifiers */
  .tablecell__thumb--rounded {
    position: relative;
    width: 36px;
    height: 36px;
    margin: -8px 0;
    border-radius: 50%;

    img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      min-height: 0;
      object-fit: cover;
      border-radius: 50%;
    }
  }

  /* Background */
  .tablecell__thumb--background-orange {
    .tablecell__thumb-letter {
      background-color: $color__user-orange;
    }
  }

  .tablecell__thumb--background-blue {
    .tablecell__thumb-letter {
      background-color: $color__user-blue;
    }
  }

  .tablecell__thumb--background-purple {
    .tablecell__thumb-letter {
      background-color: $color__user-purple;
    }
  }

  .tablecell__thumb--background-red {
    .tablecell__thumb-letter {
      background-color: $color__user-red;
    }
  }
</style>
