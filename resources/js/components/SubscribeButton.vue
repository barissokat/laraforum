<template>
  <button :class="classes" @click="subscribe" v-text="result"></button>
</template>

<script>
export default {
  props: ["active"],

  data() {
    return {
      result: this.isActive ? "Subscribed" : "Subscribe",
      isActive: this.active
    };
  },

  computed: {
    classes() {
      return ["btn btn-block", this.isActive ? "btn-success" : "btn-primary"];
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
