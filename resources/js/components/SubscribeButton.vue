<template>
  <button :class="classes" @click="subscribe" v-text="result"></button>
</template>

<script>
export default {
  props: ["active"],

  data() {
    return {
      result: this.active ? "Subscribed" : "Subscribe"
    };
  },

  computed: {
    classes() {
      return ["btn btn-block", this.active ? "btn-success" : "btn-primary"];
    }
  },

  methods: {
    subscribe() {
      axios[this.active ? "delete" : "post"](
        location.pathname + "/subscriptions"
      );

      this.active = !this.active;
      this.result = this.active ? "Subscribed" : "Subscribe";
    }
  }
};
</script>
