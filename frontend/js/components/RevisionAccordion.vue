<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot> <span class="f--small f--note">({{ revisions.length }})</span></span>
    <div slot="accordion__value">Last edited <timeago :auto-update="1" :since="new Date(revisions[0].datetime)"></timeago></div>
    <div class="revaccordion__scroller">
      <ul class="revaccordion__list">
        <li class="revaccordion__item" v-for="(revision, index) in revisions" :key="revision.id">
          <a href="#" @click.prevent="openPreview(revision.id)">
            <span class="revaccordion__author">{{ revision.author }}</span>
            <span class="revaccordion__datetime"><span class="tag" v-if="index === 0">Current</span> {{ revision.datetime | formatDate }}</span>
          </a>
        </li>
      </ul>
    </div>
  </a17-accordion>
</template>

<script>
  import a17Accordion from '@/components/Accordion.vue'
  import VisibilityMixin from '@/mixins/toggleVisibility'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17Revisions',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    props: {
      revisions: {
        default: function () {
          return []
        }
      }
    },
    filters: a17VueFilters,
    methods: {
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      },
      openPreview: function (id) {
        if (this.$root.$refs.preview) this.$root.$refs.preview.open(parseInt(id))
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .revaccordion__scroller {
    height:100%;
    overflow:hidden;
    overflow-y: auto;
    max-height:165px;
    margin:-12px -20px;
  }

  .revaccordion__list {
    padding:12px 20px;
  }

  // .revaccordion__item {
  // }

  .revaccordion__item a {
    display: flex;
    flex-direction: row;
    flex-wrap:no-wrap;
    color:$color__text--light;
    padding:7.5px 20px;
    margin-left:-20px;
    margin-right:-20px;
    text-decoration:none;

    &:focus,
    &:hover {
      color:$color__text;
      background:$color__light;
    }
  }

  .revaccordion__author {
    flex-grow: 1;
    white-space: nowrap;
  }

  .revaccordion__datetime {
    padding-left:10px;
    color:$color__link;
    white-space: nowrap;
    overflow:hidden;
    text-overflow: ellipsis;
  }

</style>
