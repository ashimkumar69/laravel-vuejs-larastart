<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12 mt-3">
        <div class="card card-widget widget-user">
          <!-- Add the bg color to the header using any of the bg-* classes   -->
          <div
            class="widget-user-header text-white bgImg"
            style="background: url('img/photo1.png');"
          >
            <h3 class="widget-user-username text-right">{{ this.form.name }}</h3>
            <h5 class="widget-user-desc text-right">{{ this.form.bio }}</h5>
          </div>
          <div class="widget-user-image">
            <img
              class="img-circle img-fluid"
              :src="userProfilePhoto()"
              width="128px"
              height="100px"
              alt="User Avatar"
            />
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">3,200</h5>
                  <span class="description-text">SALES</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">13,000</h5>
                  <span class="description-text">FOLLOWERS</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4">
                <div class="description-block">
                  <h5 class="description-header">35</h5>
                  <span class="description-text">PRODUCTS</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link" href="#activity" data-toggle="tab">Activity</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="#settings" data-toggle="tab">Settings</a>
              </li>
            </ul>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane" id="activity">
                <!-- Post -->
                No Posts
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane active" id="settings">
                <form class="form-horizontal">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      <input
                        v-model="form.name"
                        placeholder="Name"
                        type="text"
                        name="name"
                        class="form-control"
                        :class="{
                               'is-invalid': form.errors.has(
                                      'name'
                                       )
                                    }"
                      />
                      <has-error :form="form" field="name"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input
                        v-model="form.email"
                        placeholder="Email Address"
                        type="email"
                        name="email"
                        class="form-control"
                        :class="{
                                                        'is-invalid': form.errors.has(
                                                            'email'
                                                        )
                                                    }"
                      />
                      <has-error :form="form" field="email"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputName2" class="col-sm-2 col-form-label">Bio</label>
                    <div class="col-sm-10">
                      <textarea
                        v-model="form.bio"
                        placeholder="Sort Bio for User (Optional)"
                        type="text"
                        name="bio"
                        class="form-control"
                        :class="{
                                                        'is-invalid': form.errors.has(
                                                            'bio'
                                                        )
                                                    }"
                        rows="3"
                      ></textarea>
                      <has-error :form="form" field="bio"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputExperience" class="col-sm-2 col-form-label">Photo</label>
                    <div class="col-sm-10">
                      <input
                        type="file"
                        name="photo"
                        class="form-control-file"
                        @change="updateProfile"
                        :class="{
                                                        'is-invalid': form.errors.has(
                                                            'photo'
                                                        )
                                                    }"
                        aria-describedby="inputGroupFileAddon01"
                      />

                      <has-error :form="form" field="photo"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputSkills" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                      <input
                        placeholder="Password"
                        v-model="form.password"
                        type="password"
                        name="password"
                        class="form-control"
                        :class="{
                                                        'is-invalid': form.errors.has(
                                                            'password'
                                                        )
                                                    }"
                      />
                      <has-error :form="form" field="password"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button
                        type="submit"
                        @click.prevent="updateInfo"
                        class="btn btn-danger"
                      >Submit</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        bio: "",
        photo: ""
      })
    };
  },
  methods: {
    userProfilePhoto() {
      let photo =
        this.form.photo.length > 200
          ? this.form.photo
          : "/img/userprofile/" + this.form.photo;

      return photo;
    },
    updateInfo() {
      this.$Progress.start();
      this.form
        .patch("api/profile")
        .then(() => {
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    updateProfile(e) {
      let file = e.target.files[0];

      let reader = new FileReader();
      if (file.size < 2111775) {
        reader.onloadend = () => {
          this.form.photo = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Something went wrong!"
        });
      }
    }
  },
  created() {
    axios
      .get("api/profile")
      .then(data => {
        this.form.fill(data.data);
      })
      .catch();
  },
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
<style scoped>
.bgImg {
  background-position: center center !important;
  background-size: cover !important;
  background-repeat: no-repeat !important;
}
</style>