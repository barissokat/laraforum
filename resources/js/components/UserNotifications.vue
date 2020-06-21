<template>
  <li class="nav-item dropdown" v-if="notifications.length">
    <a
      id="navbarDropdown"
      class="nav-link dropdown-toggle"
      href="#"
      role="button"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
    >Notifications</a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
      <div v-for="notification in notifications" :key="notification.id">
        <a
          class="dropdown-item"
          :href="notification.data.link"
          v-text="notification.data.message"
          @click.prevent="markAsRead(notification)"
        ></a>
      </div>
    </div>
  </li>
</template>

<script>
export default {
  data() {
    return {
      notifications: false
    };
  },

  created() {
    this.fetchNotifications();
  },

  computed: {
    endpoint() {
      return `/profiles/${window.App.user.username}/notifications`;
    }
  },

  methods: {
    fetchNotifications() {
      axios
        .get(this.endpoint)
        .then(response => (this.notifications = response.data));
    },

    markAsRead(notification) {
      axios.delete(`${this.endpoint}/${notification.id}`).then(({ data }) => {
        this.fetchNotifications();

        document.location.replace(data.link);
      });
    }
  }
};
</script>
