<template>
  <div class="container">
    <div class="row" v-if="$gate.isAdminOrAuthor()">
      <div class="col-lg-12 mt-3">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Users Table</h3>

            <div class="card-tools">
              <!-- Button trigger modal -->
              <button class="btn btn-success" @click="newModel">
                Add New
                <i class="fas fa-user-plus fa-fw"></i>
              </button>

              <!-- Modal -->

              <form @submit.prevent="edidMode ? updateUser() : createUser()">
                <div
                  class="modal fade"
                  id="addnew"
                  tabindex="-1"
                  role="dialog"
                  aria-labelledby="modelTitleId"
                  aria-hidden="true"
                >
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" v-show="edidMode">Update User Info</h5>
                        <h5 class="modal-title" v-show="!edidMode">Add New</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
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
                        <div class="form-group">
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
                        <div class="form-group">
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
                        <div class="form-group">
                          <select
                            v-model="form.type"
                            name="type"
                            class="form-control"
                            :class="{
                                                        'is-invalid': form.errors.has(
                                                            'type'
                                                        )
                                                    }"
                          >
                            <option value>
                              Select User
                              Role
                            </option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard User</option>
                            <option value="author">Author</option>
                          </select>
                          <has-error :form="form" field="type"></has-error>
                        </div>
                        <div class="form-group">
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
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary" v-show="edidMode">Update</button>

                        <button type="submit" class="btn btn-primary" v-show="!edidMode">Create</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <!--end  Modal -->
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Registered At</th>
                  <th>Modify</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users.data" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.type | firstCaseToUpper }}</td>
                  <td>{{ user.created_at | userCreatedTime }}</td>
                  <td>
                    <a href="#!" @click="editModel(user)">
                      <i class="fa fa-edit"></i>
                    </a>
                    |
                    <a href="#!" @click="deleteUser(user.id)">
                      <i class="fa fa-trash red"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->

          <div class="card-footer d-flex justify-content-center">
            <pagination :data="users" @pagination-change-page="getResults">
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </div>

          <!-- /.card -->
        </div>
      </div>
    </div>
    <div v-if="!$gate.isAdminOrAuthor()">
      <not-found></not-found>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      edidMode: false,
      users: {},
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        type: "",
        bio: "",
        photo: ""
      })
    };
  },
  methods: {
    getResults(page = 1) {
      axios.get("api/user?page=" + page).then(response => {
        this.users = response.data;
      });
    },
    updateUser() {
      this.$Progress.start();
      this.form
        .put("api/user/" + this.form.id)
        .then(() => {
          this.loadUsers();

          $("#addnew").modal("hide");
          Toast.fire({
            icon: "success",
            title: "User Updated successfully"
          });
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    editModel(user) {
      this.form.clear();
      this.edidMode = true;
      $("#addnew").modal("show");

      this.form.fill(user);
    },
    newModel() {
      this.edidMode = false;
      $("#addnew").modal("show");
      this.form.reset();
    },
    deleteUser(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$Progress.start();
          this.form
            .delete("api/user/" + id)
            .then(() => {
              this.loadUsers();
              Swal.fire("Deleted!", "Your file has been deleted.", "success");

              this.$Progress.finish();
            })
            .catch(() => {
              this.$Progress.fail();
              Swal.fire({
                title: "Error!",
                text: "Do you want to continue",
                icon: "error",
                confirmButtonText: "Ok"
              });
            });
        }
      });
    },
    loadUsers() {
      if (this.$gate.isAdminOrAuthor()) {
        this.$Progress.start();
        axios
          .get("api/user")
          .then(({ data }) => {
            this.users = data;

            this.$Progress.finish();
          })
          .catch(errors => {
            console.log(errors);
            this.$Progress.fail();
          });
      }
    },
    createUser() {
      this.$Progress.start();
      this.form
        .post("api/user")
        .then(() => {
          //   this.loadUsers();
          Fire.$emit("loadNewUser");
          $("#addnew").modal("hide");
          Toast.fire({
            icon: "success",
            title: "User Created successfully"
          });
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    }
  },
  created() {
    Fire.$on("searching", () => {
      let query = this.$parent.search;
      this.$Progress.start();
      axios
        .get("api/search?q=" + query)
        .then(({ data }) => {
          this.users = data;

          this.$Progress.finish();
        })
        .catch(errors => {
          console.log(errors);
          this.$Progress.fail();
        });
    });

    this.getResults();
    this.loadUsers();
    Fire.$on("loadNewUser", () => {
      this.loadUsers();
    });
  }
};
</script>
