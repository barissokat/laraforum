<template>
  <button :class="classes" @click="subscribe" v-text="isActive ? 'Subscribed' : 'Subscribe'"></button>
</template>

<script>
export default {
  props: ["active"],

  data() {
    return {
      isActive: this.active
    };
  },

  computed: {
    classes() {
      return ["btn btn-block", this.isActive ? "btn-primary" : "btn-secondary"];
    }
  },

  methods: {
    subscribe() {
      axios[this.isActive ? "delete" : "post"](
        location.pathname + "/subscriptions"
      );

      this.isActive = !this.isActive;
      this.result = this.isActive ? "Subscribed" : "Subscribe";
    }
  }
};
</script>
