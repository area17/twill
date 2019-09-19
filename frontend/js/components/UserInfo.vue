<template>
  <div class="userInfo">

    <!-- Content -->
    <div class="userInfo__content">
      <div v-if="userInfo.registered_at" class="userInfo__item">
        <div class="userInfo__row">
          <span class="userInfo__row-cell">Registered at :</span>
          <span class="userInfo__row-cell userInfo__row-cell--value">{{ userInfo.registered_at }}</span>
        </div>
      </div>
      <div v-if="userInfo.last_login_at" class="userInfo__item">
        <div class="userInfo__row">
          <span class="userInfo__row-cell">Last login at :</span>
          <span class="userInfo__row-cell userInfo__row-cell--value">{{ userInfo.last_login_at }}</span>
        </div>
      </div>
      <div v-if="userInfo.resend_registration_link" class="userInfo__item">
        <a class="userInfo__link" :href="userInfo.resend_registration_link" type="submit">
          <span class="f--link-underlined--o">Resend registration email</span>
        </a>
      </div>
      <div class="userInfo__item">
        <button class="userInfo__link" @click="openPasswordModal" type="button">
          <span class="f--link-underlined--o">{{ userInfo.is_activated ? 'Reset password' : 'Register account now' }}</span>
        </button>
      </div>
    </div>

    <!-- Modal : Password -->
    <a17-modal class="modal--form" ref="passwordModal" :title="userInfo.is_activated ? `Reset password for ${userInfo.user_name}` : `Register account for ${userInfo.user_name}`">
      <div class="userInfo__form-row">
        <a17-textfield name="new_password" id="new_password" label="New password" :maxlength="50" note="Must have at least 8 characters" required></a17-textfield>
      </div>
      <div v-if="userInfo.is_activated" class="userInfo__form-row">
        <a17-checkbox name="require_password_change" id="require_password_change" label="Require password change at next login"></a17-checkbox>
      </div>
      <div class="userInfo__form-row">
        <a17-modal-validation :mode="userInfo.is_activated ? 'Update' : 'Create'"></a17-modal-validation>
      </div>
    </a17-modal>

  </div>
</template>

<script>
  import A17Textfield from '@/components/Textfield.vue'
  import A17Checkbox from '@/components/Checkbox.vue'
  import A17ModalValidation from '@/components/modals/ModalValidationButtons.vue'

  export default {
    name: 'A17UserInfo',
    components: {
      'a17-textfield': A17Textfield,
      'a17-checkbox': A17Checkbox,
      'a17-modal-validation': A17ModalValidation
    },
    props: {
      userInfo: {
        type: Object,
        default: null
      }
    },
    data () {
      return {
        isPasswordModalOpen: false
      }
    },
    methods: {
      openPasswordModal () {
        this.isPasswordModalOpen = true
        this.$refs.passwordModal.open()
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .userInfo__item {
    padding: 20px;
    border-bottom: 1px solid $color__border--light;
  }

  .userInfo__row {
    display: flex;
    flex-flow: row wrap;
    justify-content: space-between;
  }

  .userInfo__row-cell {
    flex: 1;

    &:first-child {
      padding-right: 20px;
    }

    &:last-child {
      text-align: right;
    }

    /* Modifiers */

    &--value {
      @include font-smoothing(on);
      color: $color__text--light;
    }
  }

  .userInfo__link {
    @include btn-reset;
    padding: 0;
    color: $color__link;
    text-decoration: none;
  }

  .userInfo__form-row + .userInfo__form-row {
    margin-top: 33px;
  }
</style>
