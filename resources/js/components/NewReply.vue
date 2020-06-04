<template>
  <div class="card bg-light border-light">
    <div class="card-body">
      <p class="text-muted text-center" v-if="!signedIn">
        Please
        <a href="/login">sign in</a> to participate in this discussion.
      </p>
      <p class="text-muted text-center" v-else-if="!verified">
        To participate in this thread, please check your email and confirm your account.
      </p>
      <div v-else>
        <div class="form-group">
          <wysiwyg placeholder="Have something to say?" v-model="body"></wysiwyg>
        </div>
        <button type="submit" class="btn btn-primary" @click="addReply">Post</button>
      </div>
    </div>
  </div>
</template>

<script>
import Tribute from "tributejs";

export default {
  data() {
    return {
      body: ""
    };
  },

  computed: {
      verified() {
          return window.App.user.email_verified_at;
      }
  },

  mounted() {
    let tribute = new Tribute({
      // column to search against in the object (accepts function or string)
      lookup: "value",
      // column that contains the content to insert by default
      fillAttr: "value",
      values: function(query, cb) {
        axios
          .get("/api/users", { params: { name: query } })
          .then(function(response) {
            console.log(response);
            cb(response.data);
          });
      }
    });
    tribute.attach(document.querySelectorAll("#body"));
  },

  methods: {
    addReply() {
      axios
        .post(location.pathname + "/replies", { body: this.body })
        .catch(error => {
          flash(error.response.data, "danger");
        })
        .then(({ data }) => {
          this.body = "";
          this.$emit("created", data);

          flash("Your reply has been posted.");
        });
    }
  }
};
</script>
