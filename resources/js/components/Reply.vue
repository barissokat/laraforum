<template>
  <div>
    <h2>Replies</h2>
    <div :id="`reply-${id}`" class="card mb-2" :class="isBest ? 'border-success' : ''">
      <div class="card-header" :class="isBest ? 'alert-success' : ''">
        <h5 class="card-title d-flex justify-content-between">
          <div class="flex-grow-1 align-self-center">
            <a :href="`/profiles/${reply.owner.name}`" v-text="reply.owner.name"></a>
            said
            <span v-text="ago"></span>
          </div>

          <div class="flex-shrink-1" v-if="signedIn">
            <favorite :reply="reply"></favorite>
          </div>
        </h5>
      </div>
      <div class="card-body">
        <div class="card-text mb-2">
          <div v-if="editing">
            <form @submit.prevent="update">
              <div class="form-group">
                <wysiwyg v-model="body"></wysiwyg>
              </div>

              <button type="submit" class="btn btn-outline-primary btn-sm">Update</button>
              <button class="btn btn-link btn-sm" @click="cancel">Cancel</button>
            </form>
          </div>

          <div v-else v-html="body"></div>
        </div>
      </div>
      <div
        class="card-footer d-flex"
        v-if="authorize('owns', reply.thread) || authorize('owns', reply.thread)"
      >
        <div v-if="authorize('owns', reply)">
          <button class="btn btn-primary btn-sm mr-2" @click="editing = true">Edit</button>
          <button class="btn btn-danger btn-sm mr-2" @click="destroy" type="button">Delete</button>
        </div>

        <button
          class="btn btn-secondary btn-sm mr-2 ml-auto"
          @click="markBestReply"
          type="button"
          v-if="authorize('owns', reply.thread)"
          v-show="! isBest"
        >Best Reply?</button>
      </div>
    </div>
  </div>
</template>

<script>
import Favorite from "./Favorite";
import moment from "moment";

export default {
  props: ["reply"],

  components: { Favorite },

  data() {
    return {
      editing: false,
      id: this.reply.id,
      body: this.reply.body,
      isBest: this.reply.isBest
    };
  },

  computed: {
    ago() {
      return moment(this.reply.created_at).fromNow() + "...";
    }
  },

  created() {
    window.events.$on("best-reply-selected", id => {
      this.isBest = id === this.id;
    });
  },

  methods: {
    update() {
      axios
        .patch("/replies/" + this.id, {
          body: this.body
        })
        .catch(error => {
          flash(error.response.data, "danger");
        });

      this.editing = false;

      flash("Your reply is updated!");
    },

    cancel() {
      this.editing = false;
      this.body = this.reply.body;
    },

    destroy() {
      axios.delete("/replies/" + this.id);

      flash("Your reply is deleted!");

      this.$emit("deleted", this.id);
    },

    markBestReply() {
      axios.post("/replies/" + this.id + "/best");

      window.events.$emit("best-reply-selected", this.id);
    }
  }
};
</script>
