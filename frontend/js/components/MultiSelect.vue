<template>
  <a17-inputframe :error="error" :note="note" :label="label" :name="name">
    <div class="multiselector">
      <div class="multiselector__grid">
        <div class="multiselector__item" v-for="(checkbox, index) in options">
          <input class="multiselector__checkbox" type="checkbox" :value="checkbox.value" :name="name" :id="uniqId(checkbox.value, index)" :disabled="checkbox.disabled || disabled" v-model="checkedValue">
          <label class="multiselector__label" :for="uniqId(checkbox.value, index)">
            <span class="multiselector__icon"><span v-svg symbol="check"></span></span>
            {{ checkbox.label }}
          </label>
          <span class="multiselector__bg"></span>
        </div>
      </div>
    </div>
  </a17-inputframe>
</template>

<script>
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import CheckboxMixin from '@/mixins/checkboxes'

  export default {
    name: 'A17Multiselect',
    mixins: [InputframeMixin, CheckboxMixin, FormStoreMixin],
    data: function () {
      return {
        randKey: Date.now() + Math.floor(Math.random() * 9999) // Label for attributes need to be uniq in the page - we use a random key so the ids are uniqs for each time the component is used
      }
    },
    computed: {
      checkedValue: {
        get: function () {
          return this.currentValue
        },
        set: function (value) {
          this.currentValue = value
          this.$emit('change', value)
        }
      }
    },
    watch: {
      currentValue: function (value) {
        this.saveIntoStore(value)
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        this.currentValue = newValue
      },
      uniqId: function (value, index) {
        return this.name + '_' + value + '-' + (this.randKey * (index + 1))
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .multiselector {
    color:$color__text;
    border:1px solid $color__border;
    background-clip: padding-box;
    box-sizing: border-box;
    overflow:hidden;
    border-radius:2px;
  }

  .multiselector__grid {
    display: flex;
    flex-direction: row;
    flex-wrap:wrap;
    box-sizing: border-box;
    overflow:hidden;
    margin-bottom: -1px;
    margin-right: -1px;
  }

  .multiselector__item {
    width:100%;
    height:50%;
    border-right:1px solid $color__border--light;
    border-bottom:1px solid $color__border--light;
    overflow: hidden;
    position:relative;

    @include breakpoint('small') {
      width:33.3333%;
    }

    @include breakpoint('medium') {
      width:100%;
    }

    @include breakpoint('large') {
      width:33.3333%;
    }

    @include breakpoint('large+') {
      width:25%;
    }
  }

  .multiselector__checkbox {
    position: absolute;
    width: 1px;
    height: 1px;
    margin-top: -1px;
    margin-left: -1px;
    padding: 0;
    border: 0 none;
    clip: rect(1px, 1px, 1px, 1px);
    overflow: hidden;
  }

  .multiselector__label {
    display: block;
    position: relative;
    padding-left: 30px + 10px;
    color: $color__text--light;
    cursor: pointer;
    z-index:1;
    height:50px;
    line-height:50px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow:hidden;
    padding-right:5px;
  }

  .multiselector__icon {
    display:block;
    position:absolute;
    left: 15px;
    top: 50%;
    margin-top:-8px;
    width: 15px;
    height: 15px;
    border: 1px solid $color__fborder;
    background: $color__f--bg;
    border-radius: 2px;
    transition: all .25s $bezier__bounce;

    .icon {
      color:$color__background;
      top: 1px;
      position: relative;
      line-height:11px;
      display: block;
      margin-left:auto;
      margin-right:auto;
    }
  }

  .multiselector__label:hover .multiselector__icon,
  .multiselector__label:focus .multiselector__icon {
    border-color: $color__fborder--hover;
  }

  .multiselector__label:hover,
  .multiselector__checkbox:hover   + .multiselector__label,
  .multiselector__checkbox:focus   + .multiselector__label,
  .multiselector__checkbox:checked + .multiselector__label {
    color:$color__text;
  }

  .multiselector__checkbox:checked + .multiselector__label .multiselector__icon {
    border-color: $color__ok;
    background-color: $color__ok;
  }

  .multiselector__checkbox:disabled + .multiselector__label {
    opacity: .5;
    pointer-events: none;
  }

  .multiselector__checkbox:focus + .multiselector__label .multiselector__icon {
    border-color: $color__border--focus;
  }

  .multiselector__checkbox:focus:checked + .multiselector__label .multiselector__icon {
    border-color: $color__ok;
  }

  .multiselector__bg {
    display:block;
    position:absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    z-index:0;
    background-color:$color__background;
    transition: background-color .25s $bezier__bounce;
  }

  .multiselector__checkbox:hover,
  .multiselector__checkbox:checked {
    + .multiselector__label + .multiselector__bg {
      background-color:$color__ultralight;
    }
  }
</style>
