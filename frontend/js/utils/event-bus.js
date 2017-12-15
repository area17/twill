import Vue from 'vue'
export const EventBus = new Vue()

export const Events = {
  drag: {
    start: 'drag-start',
    end: 'drag-end'
  }
}
