<template>
    <form>
        <div class="modal-body">
            <div class="form-group">
                <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    placeholder="Role Name"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }"
                />
                <has-error :form="form" field="name"></has-error>
            </div>

            <div class="mb-3">
                <label class="form-label">Assign Permissions</label>
                <div class="d-flex flex-column gap-2">
                    <div
                        v-for="(option, index) in permissions"
                        :key="option.name"
                        class="form-check"
                    >
                        <input
                            class="form-check-input"
                            type="checkbox"
                            :id="'role-perm-' + index"
                            v-model="form.permissions"
                            :value="option.name"
                        />
                        <label
                            class="form-check-label"
                            :for="'role-perm-' + index"
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
                class="btn btn-lg btn-primary"
                v-if="!dis"
                disabled
            >
                <span
                    class="spinner-grow spinner-grow-sm me-2"
                    role="status"
                    aria-hidden="true"
                ></span>
                Creating Role
            </button>
            <button
                type="submit"
                v-if="dis"
                @click.prevent="createRole()"
                class="btn btn-lg btn-primary"
            >
                <i class="fas fa-save"></i> Save Role
            </button>
        </div>
    </form>
</template>
<script>
export default {
    data() {
        return {
            dis: true,
            permissions: [],
            form: new Form({
                name: "",
                permissions: [],
            }),
        };
    },
    methods: {
        getPermissions() {
            axios
                .get("/getAllPermission")
                .then((response) => {
                    this.permissions = response.data.permissions;
                })
                .catch(() => {
                    swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                    });
                });
        },
        createRole() {
            this.dis = false;
            this.form
                .post("/postRole")
                .then(() => {
                    swal.fire({
                        icon: "success",
                        title: "Role Created",
                        text: "Role has been created",
                    });
                    window.location = "/role";
                })
                .catch(() => {
                    swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                    });
                });
            this.dis = true;
        },
    },
    created() {
        this.getPermissions();
    },
};
</script>
