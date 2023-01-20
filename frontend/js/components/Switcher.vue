<template>
  <div class="switcher" :class="switcherClasses" >
    <h4 class="switcher__title">{{ title }}</h4>

    <label :for="name + '_live'" class="switcher__button">
      <span v-if="isChecked" class="switcher__label">{{ formatTextEnabled }}</span>
      <span v-if="!isChecked" class="switcher__label">{{ formatTextDisabled }}</span>

      <input type="checkbox" :disabled="disabled" v-model="checkedValue" :name="name" :id="name + '_live'" value="live" />
      <span class="switcher__switcher"></span>
    </label>
  </div>
</template>

<script>
  import compareAsc from 'date-fns/compare_asc'
  import { mapState } from 'vuex'

  import { PUBLICATION } from '@/store/mutations'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17Toggle',
    props: {
      name: {
        type: String,
        default: ''
      },
      title: {
        default: 'Status'
      },
      disabled: {
        type: Boolean,
        default: false
      },
      textEnabled: {
        type: String,
        default: 'Live'
      },
      textDisabled: {
        type: String,
        default: 'Draft'
      },
      textExpired: {
        type: String,
        default: 'Expired'
      },
      textScheduled: {
        type: String,
        default: 'Scheduled'
      }
    },
    filters: a17VueFilters,
    computed: {
      switcherClasses: function () {
        return [
          this.isChecked ? 'switcher--active' : '',
          this.formatTextEnabled ? `switcher--${this.$options.filters.lowercase(this.formatTextEnabled)}` : ''
        ]
      },
      isChecked: function () {
        return this.published
      },
      formatTextEnabled: function () {
        const scoreStart = compareAsc(this.startDate, new Date())
        const scoreEnd = compareAsc(this.endDate, new Date())

        if (this.endDate && scoreEnd < 0) return this.textExpired
        else if (this.startDate && scoreStart > 0) return this.textScheduled
        else return this.textEnabled
      },
      formatTextDisabled: function () {
        return this.textDisabled
      },
      checkedValue: {
        get: function () {
          return this.published
        },
        set: function (value) {
          this.$store.commit(PUBLICATION.UPDATE_PUBLISH_STATE, value)
          this.$emit('change', value)
        }
      },
      ...mapState({
        startDate: state => state.publication.startDate,
        endDate: state => state.publication.endDate,
        published: state => state.publication.published
      })
    }
  }
</script>

<style lang="scss" scoped>

  .switcher {
    height:50px;
    line-height:50px;
    background:$color__icons;
    color:$color__background;
    padding:0 20px;

    // because the switcher will live into a box and need to overlap the border
    margin: -1px -1px 0 -1px;
    padding:0 21px;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;

    transition: background-color .25s linear;
  }

  .switcher__title {
    display:inline;
    font-weight:600;
    @include font-smoothing();
  }

  .switcher__button {
    float:right;
    position:relative;
    top:16px;
    cursor:pointer;

    input {
      position:absolute;
      opacity:0;
    }
  }

  .switcher__label {
    margin-right:15px;
  }

  .switcher__switcher {
    display:inline-block;
    height:12px;
    border-radius:6px;
    width:40px;
    background:$color__black--70;
    box-shadow: inset 0 0 1px #000;
    position:relative;

    // Big rounded thing
    &::after,
    &::before {
      content:"";
      position:absolute;
      display:block;
      height:18px;
      width:18px;
      border-radius:50%;
      left:0;
      top:-3px;
      transform:translateX(0);
      transition: all .25s $bezier__bounce;
    }

    // Big rounded thing you want to click
    &::after {
      background:$color__background;
      box-shadow: 0 0 1px #666;
    }

    // Big rounded thing for hover / focus states only
    &::before {
      background:$color__background;
      box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
      opacity:0;
    }
  }

  .switcher--active {
    background:$color__lightGreen;
    color:$color__publish;

    .switcher__switcher {
      background:$color__publish;
      box-shadow: inset 0 0 1px rgba($color__black,0.4);
    }

    .switcher__switcher::after,
    .switcher__switcher::before {
      transform:translateX(40px - 18px);
    }
  }

  /* Show something when hover / focus */
  .switcher__button {
    input:focus + .switcher__switcher::before {
      opacity:1;
    }
  }

  .switcher__button:hover,
  .switcher__button:focus {
    .switcher__switcher::before {
      opacity:1;
    }
  }

  /* Expired is looking like draft */
  .switcher--expired {
    background:$color__icons;
    color:$color__background;

    .switcher__switcher {
      background:$color__black--70;
      box-shadow: inset 0 0 1px #000;
    }
  }
</style>
