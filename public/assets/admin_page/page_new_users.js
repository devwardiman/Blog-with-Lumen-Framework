export default {
	name: "User Information",
	template: /*html*/ `
    <main>
        <div class="pb-5 text-center">
            <img class="d-block mx-auto mb-4" src="/assets/img/logo_transparent.png" alt="" width="72" height="57">
            <h2>User form</h2>
            <p class="lead">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.</p>
        </div>

        <div class="row g-5">

            <div class="col-md-12">
                <h4 class="mb-3">User Information</h4>
                <form class="needs-validation" novalidate="" @submit.prevent="store">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="displayname" class="form-label">Display Name</label>
                            <input type="text" class="form-control" id="displayname" placeholder="Display Name" required="" v-model="user.displayname">
                            <div class="invalid-feedback">
                                Valid Display Name is required.
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" name="type" id="type" required="" v-model="user.type">
                                <option value="">Pilih Type User</option>
                                <option value="member">Member</option>
                                <option value="writer">Writer</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a valid type.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" id="username" placeholder="Username" required="" v-model="user.name">
                                <div class="invalid-feedback">
                                    Your username is required.
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="you@example.com" required="" v-model="user.email">
                            <div class="invalid-feedback">
                                Please enter a valid email address for updates.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="text" class="form-control" id="password" placeholder="" v-model="user.password">
                            <div class="invalid-feedback">
                                Please enter user password.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="repeat-password" class="form-label">Ulangi Password</label>
                            <input type="text" class="form-control" id="repeat-password" placeholder="" v-model="user.repeatpassword">
                            <div class="invalid-feedback">
                                Please enter user repeat password.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button class="w-100 btn btn-primary btn-lg" type="submit">Save</button>
                </form>
            </div>
        </div>
		<div class="toast-container p-3 bottom-0 end-0" id="toastPlacement">
			<div class="toast fade" role="alert" aria-live="assertive" aria-atomic="true" ref="toastPlacement">
				<div class="toast-header">
					<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
						<rect width="100%" height="100%" fill="#007aff"></rect>
					</svg>
					<strong class="me-auto">{{res_title}}</strong>
					<small class="text-muted"></small>
					<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div class="toast-body">{{res_message}}</div>
			</div>
        </div>
    </main>
    `,
	data() {
		return {
			id: this.$route.query?.id,
			user: {
                displayname : "",
                type : "",
                name : "",
                email : "",
                password : "",
                repeatpassword : "",
            },
			loading: false,
			toast: undefined,
			res_title: "",
			res_message: "",
		};
	},
	methods: {
		refresh(id) {
			this.loading = true;
			fetch(`/api/user?id=${id}`, {
				method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
				headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
				},
			})
				.then((e) => e.json())
				.then((res) => {
					const _user = res.data[0];
					this.user = _user;
				})
				.finally((e) => {
					this.loading = false;
				});
		},
		toFormData(o) {
			return Object.entries(o).reduce((d, e) => (d.append(...e), d), new FormData());
		},
		store(event) {
			if (this.id == undefined) {
				this.user.store = true;
				fetch("/api/user/create", {
					method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
					body: this.toFormData(this.user),
				})
					.then((e) => e.json())
					.then((res) => {
						this.res_title = res.status;
						this.res_message = res.message;
						this.toast.show();
					});
			} else {
				fetch(`/api/user/update/${this.id}`, {
					method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
					body: this.toFormData(this.user),
				})
					.then((e) => e.json())
					.then((res) => {
						this.res_title = res.status;
						this.res_message = res.message;
						this.toast.show();
					});
			}
		},
	},
	mounted() {
		if (this.id) {
			this.refresh(this.id);
		}
		this.toast = new bootstrap.Toast(this.$refs.toastPlacement);
		window.scrollTo({ top: 0, behavior: 'auto' });
	},
	updated() {
		if (this.$route.query?.id == undefined && this.id != undefined) {
			this.id = undefined;
			this.loading = false;
		}
	},
	unmounted() {},
};
