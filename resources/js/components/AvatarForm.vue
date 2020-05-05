<template>
  <div class="card">
    <img class="card-img-top" :src="avatar" />
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data" v-if="canUpdate">
        <hr />
        <div class="form-group">
          <image-upload name="avatar" class="form-control-file" @loaded="onLoad"></image-upload>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ImageUpload from "./ImageUpload.vue";

export default {
  props: ['user'],

  components: { ImageUpload },

  data() {
    return {
      avatar: this.user.avatar_path
    };
  },

  computed: {
    canUpdate() {
      return this.authorize(user => user.id === this.user.id);
    }
  },

  methods: {
    onLoad(avatar) {
      this.avatar = avatar.src;

      // Persist to the server
      this.persist(avatar.file);
    },

    persist(avatar) {
      let data = new FormData();

      data.append("avatar", avatar);

      axios
        .post(`/api/users/${this.user.name}/avatar`, data)
        .then(() => flash("Avatar uploaded!"));
    }
  }
};
</script>
