<template>
  <button :class="classes" @click="subscribe" v-text="result"></button>
</template>

<script>
export default {
  props: ["active"],

  computed: {
    classes() {
      return ["btn btn-block", this.active ? "btn-success" : "btn-primary"];
    },

    result() {
      return this.active
        ? (this.result = "Subscribed")
        : (this.result = "Subscribe");
    }
  },

  methods: {
    subscribe() {
      axios[this.active ? "delete" : "post"](
        location.pathname + "/subscriptions"
      );

      this.active = !this.active;
    }
  }
};
</script>
