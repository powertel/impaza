<template>
    <div class="p-0">
        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move">
                <h3 class="card-title">
                        <i class="fas fa-users me-1"></i>
                    Users
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ms-auto">
                        <li class="nav-item me-1">
                            <button
                                class="btn btn-sm btn-primary"
                                @click="createMode"
                            >
                                <i class="fas fa-plus-circle"></i> Add New
                            </button>
                        </li>
                        <li class="nav-item">
                            <div
                                class="input-group mt-0 input-group-sm"
                                style="width: 350px"
                            >
                                <input
                                    type="text"
                                    name="table_search"
                                    v-model="searchWord"
                                    class="form-control float-right"
                                    placeholder="Search by name, email"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    @click="search"
                                >
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Action</th>
                            <th>Date Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.role }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <button
                                    class="btn btn-sm btn-info"
                                    @click="viewUser(user)"
                                >
                                    <i class="fa fa-eye"></i> View
                                </button>
                                <button
                                    class="btn btn-sm btn-warning"
                                    @click="editUser(user)"
                                >
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <button
                                    class="btn btn-sm btn-danger"
                                    @click="deleteUser(user)"
                                >
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                            <td>
                                {{ user.created_at | date }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <loading :loading="loading"></loading>
        </div>

        <div
            class="modal fade"
            id="viewUser"
            tabindex="-1"
            role="dialog"
            aria-labelledby="viewUserModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><b>Name:</b> {{ user.name }}</p>
                                <p><b>Email:</b> {{ user.email }}</p>
                                <p>
                                    <b>Last Updated:</b>
                                    {{ user.updated_at | date }}
                                </p>
                                <p>
                                    <b>Date Posted:</b>
                                    {{ user.created_at | date }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <img :src="img" class="rounded-circle img-fluid" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="modal fade"
            id="createUser"
            tabindex="-1"
            role="dialog"
            aria-labelledby="createUserModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="createUserModalLabel"
                            v-show="!editMode"
                        >
                            Create User
                        </h5>
                        <h5
                            class="modal-title"
                            id="createUserModalLabel"
                            v-show="editMode"
                        >
                            Edit User
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <form
                        @submit.prevent="
                            !editMode ? createUser() : updateUser()
                        "
                    >
                        <div class="modal-body">
                            <div class="mb-3">
                                <label> Name </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    placeholder="Name"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('name'),
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="name"
                                ></has-error>
                            </div>

                            <div class="mb-3">
                                <label> Email </label>
                                <input
                                    v-model="form.email"
                                    type="text"
                                    name="email"
                                    placeholder="Email"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('email'),
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="email"
                                ></has-error>
                            </div>

                            <div class="mb-3">
                                <label> Phone Number </label>
                                <input
                                    v-model="form.phone"
                                    type="text"
                                    name="phone"
                                    placeholder="Phone Number"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('phone'),
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="phone"
                                ></has-error>
                            </div>

                            <div class="mb-3">
                                <label> Choose Role </label>
                                <select
                                    v-model="form.role"
                                    class="form-select"
                                >
                                    <option
                                        v-for="role in roles"
                                        :key="role.id"
                                        :value="role.id"
                                    >
                                        {{ role.name }}
                                    </option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="role"
                                ></has-error>
                            </div>

                            <div class="mb-3">
                                <label> Password </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    name="password"
                                    placeholder="Password"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            form.errors.has('password'),
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="password"
                                ></has-error>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"
                                    >Assign Permissions</label
                                >
                                <div class="d-flex flex-column gap-2">
                                    <div
                                        v-for="(option, index) in permissions"
                                        :key="option.name"
                                        class="form-check"
                                    >
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            :id="'user-perm-' + index"
                                            v-model="form.permissions"
                                            :value="option.name"
                                        />
                                        <label
                                            class="form-check-label"
                                            :for="'user-perm-' + index"
                                        >
                                            {{ option.name }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button
                                type="button"
                                class="btn btn-lg btn-danger"
                                data-bs-dismiss="modal"
                            >
                                Close
                            </button>
                            <button
                                type="button"
                                class="btn btn-lg btn-primary"
                                v-if="!load"
                                disabled
                            >
                                <span
                                    class="spinner-grow spinner-grow-sm me-2"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                {{ action }}
                            </button>
                            <button
                                type="submit"
                                v-if="load"
                                v-show="!editMode"
                                class="btn btn-lg btn-primary"
                            >
                                Save User
                            </button>
                            <button
                                type="submit"
                                v-if="load"
                                v-show="editMode"
                                class="btn btn-lg btn-success"
                            >
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            action: "",
            searchWord: "",
            loading: false,
            editMode: false,
            load: true,
            img: "http://localhost:8000/img/avatar.jpg",
            user: {},
            users: [],
            roles: [],
            permissions: [],
            form: new Form({
                id: "",
                name: "",
                phone: "",
                password: "",
                email: "",
                permissions: [],
                role: 3,
            }),
        };
    },
    methods: {
        search() {
            this.loading = true;
            axios
                .get("/search/user?s=" + this.searchWord)
                .then((response) => {
                    this.loading = false;
                    this.users = response.data.users;
                })
                .catch(() => {
                    this.loading = false;
                    toast.fire({
                        icon: "error",
                        title: "search failed",
                    });
                });
        },
        createMode() {
            this.editMode = false;
            window.bootstrap.Modal.getOrCreateInstance(
                document.getElementById("createUser")
            ).show();
        },
        deleteUser(user) {
            swal.fire({
                title: "Are you sure?",
                text: user.name + " will be deleted permanently!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.value) {
                    axios
                        .delete("/delete/user/" + user.id)
                        .then(() => {
                            toast.fire({
                                icon: "success",
                                title:
                                    user.name + " has been deleted sucessfully",
                            });
                            Fire.$emit("loadUser");
                        })
                        .catch(() => {
                            toast.fire({
                                icon: "error",
                                title:
                                    user.name +
                                    " was unable to be remove, tyr again later",
                            });
                        });
                }
            });
        },
        editUser(user) {
            this.editMode = true;
            this.form.reset();
            this.form.fill(user);
            this.form.role = user.roles[0].id;
            this.form.permissions = user.userPermissions;
            window.bootstrap.Modal.getOrCreateInstance(document.getElementById('createUser')).show();
        },
        getUsers() {
            this.loading = true;
            axios
                .get("/getAllUsers")
                .then((response) => {
                    this.loading = false;
                    this.users = response.data.users;
                })
                .catch(() => {
                    this.loading = false;
                    this.$toastr.e("Cannot load users", "Error");
                });
        },
        viewUser(user) {
            this.img = "http://localhost:8000/img/" + user.photo;
            this.user = user;
            window.bootstrap.Modal.getOrCreateInstance(
                document.getElementById("viewUser")
            ).show();
        },
        getRoles() {
            axios.get("/getAllRoles").then((response) => {
                this.roles = response.data.roles;
            });
        },
        getPermissions() {
            axios.get("/getAllPermissions").then((response) => {
                this.permissions = response.data.permissions;
            });
        },
        createUser() {
            this.action = "Creating user ...";
            this.load = false;
            this.form
                .post("/account/create")
                .then((response) => {
                    this.load = true;
                    this.$toastr.s("user create succefully", "Created");
                    Fire.$emit("loadUser");
                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('createUser')).hide();
                    this.form.reset();
                })
                .catch(() => {
                    this.load = true;
                    this.$toastr.e("Cannot create user, try again", "Error");
                });
        },
        updateUser() {
            this.action = "Update user ...";
            this.load = false;
            this.form
                .put("/account/update/" + this.form.id)
                .then((response) => {
                    this.load = true;
                    this.$toastr.s(
                        "user information updated succefully",
                        "Created"
                    );
                    Fire.$emit("loadUser");
                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('createUser')).hide();
                    this.form.reset();
                })
                .catch(() => {
                    this.load = true;
                    this.$toastr.e(
                        "Cannot update user information, try again",
                        "Error"
                    );
                });
        },
    },
    created() {
        this.getUsers();
        this.getRoles();
        this.getPermissions();
        Fire.$on("loadUser", () => {
            this.getUsers();
        });
    },
};
</script>
