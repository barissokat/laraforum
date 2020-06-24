<template>
  <div
    class="modal fade"
    id="exampleModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Leaderboard</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div
            v-for="(user, key) in leaderboard"
            :key="user.id"
            class="my-10 p-4 rounded border border-grey-light flex justify-between"
          >
            <span class="text-5xl text-grey-light">{{ key+1 }}. </span>
            <a
              :href="userProfileLink(user.username)"
              class="inline mb-4 text-xl text-blue"
            >{{ user.username }}</a>
            <span
              class="inline px-2 bg-green rounded font-semibold text-white"
            >{{ user.reputation }} XP</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Leaderboard",
  data() {
    return {
      leaderboard: {}
    };
  },
  created() {
    this.fetch();
  },
  methods: {
    fetch() {
      window.axios.get("api/leaderboard").then(this.refresh);
    },
    refresh({ data }) {
      this.leaderboard = data.leaderboard;
    },
    userProfileLink(username) {
      return `/profiles/${username}`;
    }
  }
};
</script>
