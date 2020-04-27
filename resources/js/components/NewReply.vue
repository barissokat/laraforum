<template>
  <div>
    <div class="card bg-light border-light">
      <div class="card-body">
        <div v-if="signedIn">
          <div class="form-group">
            <textarea
              class="form-control"
              name="body"
              id="body"
              rows="3"
              placeholder="Have something to say?"
              required
              v-model="body"
            ></textarea>
          </div>
          <button type="submit" class="btn btn-primary" @click="addReply">Post</button>
        </div>
        <p class="text-muted text-center" v-else>
          Please
          <a href="/login">sign in</a> to participate in this discussion.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      body: ""
    };
  },

  computed: {
    signedIn() {
      return window.App.signedIn;
    }
  },

  methods: {
    addReply() {
      axios
        .post(location.pathname + "/replies", { body: this.body })
        .catch(error => {
          flash(error.response.data, 'danger');
        })
        .then(({ data }) => {
          this.body = "";

          flash("Your reply has been posted.");

          this.$emit("created", data);
        });
    }
  }
};
</script>
