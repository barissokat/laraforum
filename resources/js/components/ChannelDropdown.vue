<template>
  <li class="nav-item dropdown" :class="{'open': toggle}">
    <a
      id="channelsDropdown"
      class="nav-link dropdown-toggle"
      href="#"
      role="button"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
      @click.prevent="toggle = !toggle"
    >Channels</a>

    <div
      class="dropdown-menu dropdown-menu-right channel-dropdown"
      aria-labelledby="channelsDropdown"
    >
      <div class="input-wrapper">
        <input type="text" class="form-control" v-model="filter" placeholder="Filter Channels..." />
      </div>
      <div v-for="channel in filteredChannels" :key="channel.id">
        <a class="dropdown-item" :href="`/threads/${channel.slug}`" v-text="channel.name"></a>
      </div>
    </div>
  </li>
</template>

<script>
export default {
  data() {
    return {
      channels: [],
      toggle: false,
      filter: ""
    };
  },

  created() {
    axios.get("/api/channels")
         .then(({ data }) => (this.channels = data));
  },

  computed: {
    filteredChannels() {
      return this.channels.filter(channel => {
        return channel.name
          .toLowerCase()
          .startsWith(this.filter.toLocaleLowerCase());
      });
    }
  }
};
</script>

<style lang="scss">
.channel-dropdown {
  padding: 0;
}
.input-wrapper {
  padding: 0.5rem 1rem;
}
</style>
