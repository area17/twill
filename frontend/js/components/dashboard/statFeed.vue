<template>
  <div class="box statFeed">
    <header class="box__header">
      <div class="wrapper">
          <div class="col--double">
            <b><slot></slot></b>
          </div>
          <div class="col--double">
            <div class="statFeed__dropdown">
              <a17-dropdown ref="statPeriodDropdown" position="bottom-right">
                <a17-button variant="ghost" @click="$refs.statPeriodDropdown.toggle()">{{ selectedPeriodLabel }} <span v-svg class="statFeed__dropdownIcon" symbol="dropdown_module"></span></a17-button>
                <div slot="dropdown__content">
                  <button type="button" v-for="period in periods" v-if="period.value !== selectedPeriod" @click="selectPeriod(period.value)">{{ period.label }}</button>
                </div>
              </a17-dropdown>
            </div>
          </div>
      </div>
    </header>
    <div class="box__body">
      <template  v-for="(fact, index) in factsForSelectedPeriod">
      <a :href="fact.url" class="statFeed__item" target="_blank">
        <h3 class="statFeed__numb f--heading" :class="trending(index)">{{ fact.figure }}</h3>
        <div class="statFeed__info">
          <h4 class="statFeed__label">{{ fact.label }}</h4>
          <p class="statFeed__meta f--note f--small">{{ fact.insight }}</p>
        </div>
      </a>
      </template>
    </div>
    <footer class="box__footer statFeed__footer">
      <a href="https://analytics.google.com/analytics/web" class="f--external" target="_blank">Google Analytics</a>
    </footer>
  </div>
</template>

<script>
  export default {
    name: 'A17StatFeed',
    props: {
      facts: {
        type: Object,
        default: function () {
          return {}
        }
      }
    },
    data: function () {
      return {
        selectedPeriod: 'yesterday',
        periods: [
          {
            label: 'Today',
            value: 'today'
          },
          {
            label: 'Yesterday',
            value: 'yesterday'
          },
          {
            label: 'This week',
            value: 'week'
          },
          {
            label: 'This month',
            value: 'month'
          }
        ]
      }
    },
    computed: {
      factsForSelectedPeriod () {
        return this.facts[this.selectedPeriod]
      },
      selectedPeriodLabel () {
        return this.periods.find((p) => { return p.value === this.selectedPeriod }).label
      }
    },
    methods: {
      trending: function (index) {
        return 'statFeed__numb--' + this.factsForSelectedPeriod[index].trend
      },
      selectPeriod: function (period) {
        this.selectedPeriod = period
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .statFeed {

  }

  .statFeed__dropdown {
    text-align: right;
  }

  .statFeed__item {
    border-top:1px solid $color__border--light;
    text-decoration:none;
    padding:15px 0;
    display:flex;
    @include monospaced-figures(off);

    &:hover {
      background: $color__ultralight;
    }
  }

  .statFeed__numb {
    line-height:1em;
    min-width:33.333%;
    position:relative;
    padding:10px 35px 10px 20px;
  }

  .statFeed__item:first-child {
    border-top:0 none;
  }

  .statFeed__numb,
  .statFeed__footer {
    color:$color__stats;
  }

  .statFeed__numb--up::after,
  .statFeed__numb--down::after {
    font-size:15px;
    color:inherit;
    position:absolute;
    top:0;
    /*bottom:0em;*/
    vertical-align:baseline;
    transform: translateX(50%);
    font-weight:400;
  }

  .statFeed__numb--up::after {
    content: "\2197";
  }

  .statFeed__numb--down::after {
    content: "\2198";
  }

  .statFeed__info {
    padding:10px 20px;
  }

  .statFeed__info {
    border-left:1px solid $color__border--light;
  }
</style>
