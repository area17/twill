<template>
  <div class="colorpicker">
    <div class="colorpicker__color">
      <div class="colorpicker__saturation" ref="satContainer" :style="{background: bgColor}" @mousedown="handleMouseDown('saturation')">
        <div class="colorpicker__saturation--white"></div>
        <div class="colorpicker__saturation--black"></div>
        <div class="colorpicker__saturation-pointer" :style="{top: satPointerTop, left: satPointerLeft}">
          <div class="colorpicker__saturation-circle"></div>
        </div>
      </div>
      <div class="colorpicker__hue colorpicker__hue--vertical">
        <div class="colorpicker__hue-container" ref="hueContainer"
             @mousedown="handleMouseDown('hue')">
          <div class="colorpicker__hue-pointer" :style="{top: huePointerTop, left: huePointerLeft}">
            <div class="colorpicker__hue-picker"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  // Hightly inspired by https://github.com/xiaokaike/vue-color
  import tinyColor from 'tinycolor2'
  import throttle from 'lodash/throttle'

  export default {
    name: 'a17ColorPicker',
    props: {
      color: {
        type: String,
        required: true
      },
      direction: {
        type: String,
        // [horizontal | vertical]
        default: 'vertical'
      }
    },
    data: function () {
      return {
        currentColor: tinyColor(this.color),
        currentColorHue: tinyColor(this.color).toHsv().h,
        currentTarget: '', // [saturation | hue]
        pullDirection: ''
      }
    },
    computed: {
      bgColor () {
        return `hsl(${this.currentColorHue}, 100%, 50%)`
      },
      satPointerTop () {
        return (-(this.currentColor.toHsv().v * 100) + 1) + 100 + '%'
      },
      satPointerLeft () {
        return this.currentColor.toHsv().s * 100 + '%'
      },
      huePointerTop () {
        if (this.direction === 'vertical') {
          if (this.currentColorHue === 0 && this.pullDirection === 'right') return 0
          return -((this.currentColorHue * 100) / 360) + 100 + '%'
        } else {
          return 0
        }
      },
      huePointerLeft () {
        if (this.direction === 'vertical') {
          return 0
        } else {
          if (this.currentColorHue === 0 && this.pullDirection === 'right') return '100%'
          return (this.currentColorHue * 100) / 360 + '%'
        }
      }
    },
    methods: {
      throttle: throttle((fn, data) => {
        fn(data)
      }, 20, {
        'leading': true,
        'trailing': false
      }),
      satHandleChange (event, skip) {
        !skip && event.preventDefault()
        const container = this.$refs.satContainer

        if (!container) return

        const containerWidth = container.clientWidth
        const containerHeight = container.clientHeight
        const xOffset = container.getBoundingClientRect().left + window.pageXOffset
        const yOffset = container.getBoundingClientRect().top + window.pageYOffset
        const pageX = event.pageX || (event.touches ? event.touches[0].pageX : 0)
        const pageY = event.pageY || (event.touches ? event.touches[0].pageY : 0)

        let left = pageX - xOffset
        let top = pageY - yOffset

        if (left < 0) {
          left = 0
        } else if (left > containerWidth) {
          left = containerWidth
        } else if (top < 0) {
          top = 0
        } else if (top > containerHeight) {
          top = containerHeight
        }

        const saturation = left / containerWidth
        let bright = -(top / containerHeight) + 1

        bright = bright > 0 ? bright : 0
        bright = bright > 1 ? 1 : bright

        this.throttle(this.onChange, {
          h: this.currentColorHue,
          s: saturation,
          v: bright,
          a: this.currentColor.toHsv().a
        })
      },
      hueHandleChange (event, skip) {
        !skip && event.preventDefault()
        const container = this.$refs.hueContainer
        const containerWidth = container.clientWidth
        const containerHeight = container.clientHeight
        const xOffset = container.getBoundingClientRect().left + window.pageXOffset
        const yOffset = container.getBoundingClientRect().top + window.pageYOffset
        const pageX = event.pageX || (event.touches ? event.touches[0].pageX : 0)
        const pageY = event.pageY || (event.touches ? event.touches[0].pageY : 0)
        const left = pageX - xOffset
        const top = pageY - yOffset

        let h
        let percent

        if (this.direction === 'vertical') {
          if (top < 0) {
            h = 360
          } else if (top > containerHeight) {
            h = 0
          } else {
            percent = -(top * 100 / containerHeight) + 100
            h = (360 * percent / 100)
          }
        } else {
          if (left < 0) {
            h = 0
          } else if (left > containerWidth) {
            h = 360
          } else {
            percent = left * 100 / containerWidth
            h = (360 * percent / 100)
          }
        }

        if (this.currentColorHue !== h) {
          this.throttle(this.onChange, {
            h: h,
            s: this.currentColor.toHsl().s,
            l: this.currentColor.toHsl().l,
            a: this.currentColor.toHsl().a,
            source: 'hsl'
          })
        }
      },
      handleMouseDown (type) {
        // this.handleChange(e, true)
        this.currentTarget = type
        if (this.currentTarget === 'saturation') {
          window.addEventListener('mousemove', this.satHandleChange)
          window.addEventListener('mouseup', this.satHandleChange)
        } else {
          window.addEventListener('mousemove', this.hueHandleChange)
          window.addEventListener('mouseup', this.hueHandleChange)
        }
        window.addEventListener('mouseup', this.handleMouseUp)
      },
      handleMouseUp (e) {
        this.unbindEventListeners()
      },
      unbindEventListeners () {
        if (this.currentTarget === 'saturation') {
          window.removeEventListener('mousemove', this.satHandleChange)
          window.removeEventListener('mouseup', this.satHandleChange)
        } else {
          window.removeEventListener('mousemove', this.hueHandleChange)
          window.removeEventListener('mouseup', this.hueHandleChange)
        }

        window.removeEventListener('mouseup', this.handleMouseUp)
      },
      onChange (param) {
        this.currentColor = tinyColor(param)
        this.currentColorHue = param.h
        this.$emit('change', this.currentColor.toHexString())
      }
    }
  }
</script>

<style lang="scss">

  .colorpicker {
    position: relative;
    display: flex;
    max-width: 100%;
    height: 250px;
    margin: 0 10px;
    // margin-top: 10px;
    // background: #ccc;
  }

  .colorpicker__color {
    display: flex;
    width:100%;
    // width: 50%;
  }

  .colorpicker__saturation {
    position: relative;
    flex-grow: 1;
    margin-right: 5px;
    cursor: pointer;
    overflow:hidden;

    .colorpicker__saturation--white,
    .colorpicker__saturation--black {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
    }

    .colorpicker__saturation--white {
      background: linear-gradient(to right, #fff, rgba(255, 255, 255, 0));
    }

    .colorpicker__saturation--black {
      background: linear-gradient(to top, #000, rgba(0, 0, 0, 0));
    }

    .colorpicker__saturation-pointer {
      cursor: pointer;
      position: absolute;
    }

    .colorpicker__saturation-circle {
      cursor: head;
      width: 8px;
      height: 8px;
      box-shadow: 0 0 0 1.5px #fff, inset 0 0 1px 1px rgba(0, 0, 0, .3), 0 0 1px 2px rgba(0, 0, 0, .4);
      border-radius: 50%;
      transform: translate(-4px, -4px);
    }
  }

  .colorpicker__hue {
    width: 12px;
    position: relative;
    border-radius: 2px;
  }

  .colorpicker__hue--horizontal {
    background: linear-gradient(to right, #f00 0%, #ff0 17%, #0f0 33%, #0ff 50%, #00f 67%, #f0f 83%, #f00 100%);
  }

  .colorpicker__hue--vertical {
    background: linear-gradient(to top, #f00 0%, #ff0 17%, #0f0 33%, #0ff 50%, #00f 67%, #f0f 83%, #f00 100%);
  }

  .colorpicker__hue-container {
    position: relative;
    cursor: pointer;
    margin: 0 2px;
    height: 100%;
  }

  .colorpicker__hue-pointer {
    z-index: 2;
    position: absolute;
  }

  .colorpicker__hue-picker {
    cursor: pointer;
    margin-left: -2px;
    width: 14px;
    border-radius: 2px;
    height: 8px;
    box-shadow: 0 0 2px rgba(0, 0, 0, .5);
    background: #fff;
    transform: translateX(-1px) translateY(-4px);
  }

</style>
