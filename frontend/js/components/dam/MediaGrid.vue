<template>
    <div class="mediagrid">
      <div class="mediagrid__item" v-for="item in items" :key="item.id">
        <span class="mediagrid__button s--picked">
            <img :src="item.src" class="mediagrid__img" />
            <span v-if="item.fileExtension" class="mediagrid__label">{{ item.fileExtension }}</span>
            <span class="mediagrid__close__btn" @click="deleteMedia(item)">
                <span v-svg  symbol="close_icon"></span>
            </span>
          </span>
      </div>
    </div>
  </template>
  
  <script>
  
  import mediaItemsMixin from '@/mixins/mediaLibrary/mediaItems'
  
  export default {
    name: 'A17Mediagrid',
    mixins: [mediaItemsMixin],
    props: {
    },
    computed: {
    },
    methods: {
      deleteMedia: function(media) {
        this.$emit('deleteMedia', media)
      }
    }
  }
  </script>
  
  <style lang="scss" scoped>
    $height_text: 17px;
  
    .mediagrid {
      display: block;
      width: 100%;
      height: 100%;
      font-size:0;
      line-height: 1em;
    }
  
    .mediagrid__item {
      position:relative;
      display:inline-block;
      width: 16.66666665%;
      padding-bottom: 16.66666665%;
      overflow: hidden;
  
      @media (max-width: 300px) {
        width: 100%;
        padding-bottom: 100%;
      }
  
      @media (min-width: 300px) {
        width: calc(100% / 2);
        padding-bottom: calc(100% / 2);
      }
  
      @media (min-width: 600px) {
        width: calc(100% / 2);
        padding-bottom: calc(100% / 2);
      }
  
      @media (min-width: 800px) {
        width: calc(100% / 3);
        padding-bottom: calc(100% / 3);
      }
  
      @media (min-width: 1000px) {
        width: calc(100% / 4);
        padding-bottom: calc(100% / 4);
      }
  
      @media (min-width: 1200px) {
        width: calc(100% / 5);
        padding-bottom: calc(100% / 5);
      }
  
      @media (min-width: 1400px) {
        width: calc(100% / 6);
        padding-bottom: calc(100% / 6);
      }
  
      @media (min-width: 1600px) {
        width: calc(100% / 7);
        padding-bottom: calc(100% / 7);
      }
  
      @media (min-width: 1800px) {
        width: calc(100% / 8);
        padding-bottom: calc(100% / 8);
      }
  
      @media (min-width: 2000px) {
        width: calc(100% / 9);
        padding-bottom: calc(100% / 9);
      }
  
      @media (min-width: 2200px) {
        width: calc(100% / 10);
        padding-bottom: calc(100% / 10);
      }
  
    
  
    }

    .mediagrid__button {
    position: absolute;
   

    display: flex;
    justify-content: center; /* align horizontal */
    flex-direction: column;
    align-items: center; /* align vertical */
    @include font-regular;

    user-select: none;
    // background:$color__lighter;

    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;

    .s--hasFilename & {
      bottom: calc(10px + #{$height_text})
    }

    &:before {
      content: "";
      position: absolute;
      display:block;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border:1px solid rgba(0,0,0,0.05);
    }

    &.s--picked {
      &:after {
        content: "";
        position: absolute;
        display:block;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border:4px solid $color__link;
        z-index: 1;
      }
    }

    &.s--used {
      &:before {
        content: "";
        position: absolute;
        display:block;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: $color__translucentBlue;
        opacity: 0.85;
      }
    }

    &.s--disabled {
      pointer-events: none;
      opacity: 0.2;
    }
  }
    

    .mediagrid__img {
      display:block;
      max-width:100%;
      height:auto;
      max-height:100%;
    }
     
    .mediagrid__close__btn {
        position: absolute;
        top: rem-calc(10);
        right: rem-calc(10);
        color: $color__icons;
        opacity: 1;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;         
        border-radius: 50%;          
        cursor: pointer;
        width: rem-calc(23);
        height: rem-calc(23);
        z-index:2;
    }


  </style>
  