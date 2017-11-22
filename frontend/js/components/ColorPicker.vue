<template>
  <div class="colorpicker">
    <div class="colorpicker__saturation" ref="container" :style="{background: bgColor}" @mousedown="handleMouseDown">
      <div class="colorpicker__saturation--white"></div>
      <div class="colorpicker__saturation--black"></div>
      <div class="colorpicker__saturation-pointer" :style="{top: pointerTop, left: pointerLeft}">
        <div class="colorpicker__saturation-circle"></div>
      </div>
    </div>
    <div class="colorpicker__info">

    </div>
  </div>
</template>

<script>
  import tinyColor from 'tinycolor2'
  import { throttle } from 'lodash'

  export default {
    name: 'a17ColorPicker',
    props: {
      color: {
        type: String,
        required: true
      }
    },
    data: function () {
      return {
        currentColor: tinyColor(this.color)
      }
    },
    computed: {
      bgColor () {
        return `hsl(${this.currentColor.toHsv().h}, 100%, 50%)`
      },
      pointerTop () {
        return (-(this.currentColor.toHsv().v * 100) + 1) + 100 + '%'
      },
      pointerLeft () {
        return this.currentColor.toHsv().s * 100 + '%'
      }
    },
    methods: {
      throttle: throttle((fn, data) => {
        fn(data)
      }, 20, {
        'leading': true,
        'trailing': false
      }),
      handleChange (event, skip) {
        !skip && event.preventDefault()
        const container = this.$refs.container
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
          h: this.currentColor.toHsv().h,
          s: saturation,
          v: bright,
          a: this.currentColor.toHsv().a
        })
      },
      onChange (param) {
        this.currentColor = tinyColor(param)
        console.log(tinyColor(param).toHexString())
        this.$emit('change', this.currentColor.toHexString())
      },
      handleMouseDown (e) {
        // this.handleChange(e, true)
        window.addEventListener('mousemove', this.handleChange)
        window.addEventListener('mouseup', this.handleChange)
        window.addEventListener('mouseup', this.handleMouseUp)
      },
      handleMouseUp (e) {
        this.unbindEventListeners()
      },
      unbindEventListeners () {
        window.removeEventListener('mousemove', this.handleChange)
        window.removeEventListener('mouseup', this.handleChange)
        window.removeEventListener('mouseup', this.handleMouseUp)
      }
    }
  }
</script>

<style lang="scss">

  .colorpicker {
    position: relative;
    max-width: 100%;
    height: 250px;
    margin-top: 10px;
    background: #ccc;
  }

  .colorpicker__saturation,
  .colorpicker__saturation--white,
  .colorpicker__saturation--black {
    cursor: pointer;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
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
    width: 4px;
    height: 4px;
    box-shadow: 0 0 0 1.5px #fff, inset 0 0 1px 1px rgba(0, 0, 0, .3), 0 0 1px 2px rgba(0, 0, 0, .4);
    border-radius: 50%;
    transform: translate(-2px, -2px);
  }

</style>
