<template>
  <div :id="`reply-${id}`" class="card">
    <div class="card-body">
      <h5 class="card-title d-flex justify-content-between">
        <div class="flex-grow-1 align-self-center">
          <a :href="`/profiles/${data.owner.name}`" v-text="data.owner.name"></a>
          said {{ data.created_at }}...
        </div>

        <div class="flex-shrink-1" v-if="signedIn">
          <favorite :reply="data"></favorite>
        </div>
      </h5>
      <div class="card-text">
        <div v-if="editing">
          <div class="form-group">
            <textarea class="form-control" name id rows="3" v-model="body"></textarea>
          </div>

          <button class="btn btn-outline-primary btn-sm" @click="update">Update</button>
          <button class="btn btn-link btn-sm" @click="editing = false">Cancel</button>
        </div>

        <div v-else v-text="body"></div>
      </div>
      <div class="card-footer d-flex" v-if="canUpdate">
        <button class="btn btn-secondary btn-sm mr-2" @click="editing = true">Edit</button>
        <button class="btn btn-danger btn-sm mr-2" @click="destroy">Delete</button>
      </div>
    </div>
  </div>
</template>

<script>
import Favorite from "./Favorite";

export default {
  props: ["data"],

  components: { Favorite },

  data() {
    return {
      editing: false,
      id: this.data.id,
      body: this.data.body
    };
  },

  computed: {
    signedIn() {
      return window.App.signedIn;
    },

    canUpdate() {
        return this.authorize(user => this.data.user_id == user.id);
        // return this.data.user_id == window.App.user.id;
    }
  },

  methods: {
    update() {
      axios.patch("/replies/" + this.data.id, {
        body: this.body
      });

      this.editing = false;

      flash("Updated");
    },

    destroy() {
      axios.delete("/replies/" + this.data.id);

      this.$emit("deleted", this.data.id);
    }
  }
};
</script>