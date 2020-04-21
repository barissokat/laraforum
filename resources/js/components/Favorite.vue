<template>
  <button type="submit" :class="classes" @click="toggle">
    <span>Fav</span>
    <span v-text="count"></span>
  </button>
</template>

<script>
export default {
  props: ["reply"],

  data() {
    return {
      count: this.reply.favoritesCount,
      active: this.reply.isFavorited
    };
  },

  computed: {
    classes() {
      return [
        "btn btn-sm",
        this.active ? "btn-outline-secondary" : "btn-secondary"
      ];
    },

    endpoint() {
      return "/replies/" + this.reply.id + "/favorites";
    }
  },

  methods: {
    toggle() {
      this.active ? this.destory() : this.create();
    },

    create() {
      axios.post(this.endpoint);

      this.active = true;
      this.count++;
    },

    destory() {
      axios.delete(this.endpoint);

      this.active = false;
      this.count--;
    }
  }
};
</script>
