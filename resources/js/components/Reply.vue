<template>
  <div :id="`reply-${id}`" class="card" :class="isBest ? 'border-success' : ''">
    <div class="card-header" :class="isBest ? 'alert alert-success' : ''">
      <h5 class="card-title d-flex justify-content-between">
        <div class="flex-grow-1 align-self-center">
          <a :href="`/profiles/${data.owner.name}`" v-text="data.owner.name"></a>
          said
          <span v-text="ago"></span>
        </div>

        <div class="flex-shrink-1" v-if="signedIn">
          <favorite :reply="data"></favorite>
        </div>
      </h5>
    </div>
    <div class="card-body">
      <div class="card-text">
        <div v-if="editing">
          <form @submit="update">
            <div class="form-group">
              <textarea class="form-control" rows="3" v-model="body" required></textarea>
            </div>

            <button class="btn btn-outline-primary btn-sm">Update</button>
            <button class="btn btn-link btn-sm" @click="editing = false">Cancel</button>
          </form>
        </div>

        <div v-else v-html="body"></div>
      </div>
      <div class="card-footer d-flex">
        <div v-if="authorize('updateReply', reply)">
          <button class="btn btn-primary btn-sm mr-2" @click="editing = true">Edit</button>
          <button class="btn btn-danger btn-sm mr-2" @click="destroy" type="button">Delete</button>
        </div>
        <button
          class="btn btn-secondary btn-sm mr-2 ml-auto"
          @click="markBestReply"
          type="button"
          v-show="!isBest"
        >Best Reply?</button>
      </div>
    </div>
  </div>
</template>

<script>
import Favorite from "./Favorite";
import moment from "moment";

export default {
  props: ["data"],

  components: { Favorite },

  data() {
    return {
      editing: false,
      id: this.data.id,
      body: this.data.body,
      isBest: false,
      reply: this.data
    };
  },

  computed: {
    ago() {
      return moment(this.data.created_at).fromNow() + "...";
    }
  },

  methods: {
    update() {
      axios
        .patch("/replies/" + this.data.id, {
          body: this.body
        })
        .catch(error => {
          flash(error.response.data, "danger");
        });

      this.editing = false;

      flash("Your reply is updated");
    },

    destroy() {
      axios.delete("/replies/" + this.data.id);

      this.$emit("deleted", this.data.id);
    },

    markBestReply() {
      this.isBest = true;
    }
  }
};
</script>
