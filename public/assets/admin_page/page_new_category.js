export default {
	name: "New Category",
	template: /*html*/ `
        <main class="bd-main order-1">
            <div class="bd-intro pt-2 ps-lg-2 placeholder-wave">
                <div class="d-md-flex flex-md-row-reverse align-items-center justify-content-between">
                    <div class="mb-3 mb-md-0 d-flex">
                        <button v-if="!loading && id" class="btn btn-sm btn-bd-light rounded-2 me-1"  data-bs-toggle="modal" data-bs-target="#modalChoice">Delete</button>
                        <button v-if="!loading" class="btn btn-sm btn-bd-light rounded-2" @click="store">Save</button>
                    </div>
                    <h1 class="bd-title mb-1 placeholder" v-if="loading">Memuat Nama Category</h1>
                    <h1 class="bd-title mb-1" v-else contenteditable v-text="category_name" @input="onCNInput"></h1>
                </div>
                <p class="bd-lead placeholder placeholder-sm" v-if="loading">Memuat Abstract</p>
                <p class="bd-lead" v-else contenteditable v-text="category_desc" @input="onCDInput"></p>
            </div>
        </main>

        <div class="modal modal-alert py-5" tabindex="-1" role="dialog" id="modalChoice">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content rounded-3 shadow">
                    <div class="modal-body p-4 text-center">
                        <h5 class="mb-3">Yakin menghapus Category?</h5>
                        <p class="mb-0">Artikel dengan Category ini tidak akan dihapus!</p>
                    </div>
                    <div class="modal-footer flex-nowrap p-0">
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 border-end" data-bs-dismiss="modal" @click="destroy"><strong>Yes, hapus</strong></button>
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0" data-bs-dismiss="modal">No thanks</button>
                    </div>
                </div>
            </div>
        </div>
      `,
	data() {
		return {
			id: this.$route.query?.id,
			category_name: "Nama Category",
			category_desc: "Abstract",
			loading: false,
		};
	},
	methods: {
		onCNInput(e) {
			this.category_name = e.target.innerText;
		},
		onCDInput(e) {
			this.category_desc = e.target.innerText;
		},
		refresh(id) {
			this.loading = true;
			fetch("/api/category?id=" + id, {
				method: "GET",
				headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
				},
			})
				.then((e) => e.json())
				.then((res) => {
					const category = res.data[0];
					this.category_name = category.category_name;
					this.category_desc = category.category_desc;
				})
				.finally((e) => {
                    this.loading = false;
				});
		},
		store() {
			const formData = new FormData();
			formData.append("category_name", this.category_name);
			formData.append("category_desc", this.category_desc);

			if (this.id == undefined) {
				fetch("/api/category/create", {
					method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
					body: formData,
				})
					.then((e) => e.text())
					.then((res) => {
						console.log(res);
						this.$router.push("/app/categories");
					});
			} else {
				formData.append("_method", "PATCH");
				fetch(`/api/category/update/${this.id}`, {
					method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
					body: formData,
				})
					.then((e) => e.text())
					.then((res) => {
						console.log(res);
						this.$router.push("/app/categories");
					});
			}
		},
		destroy() {
			const formData = new FormData()
			fetch(`/api/category/delete/${this.id}`, {
				method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
				body: formData,
			})
				.then((e) => e.text())
				.then((res) => {
					console.log(res);
					this.$router.push("/app/categories");
				});
		},
	},
	mounted() {
		if (this.id) {
			this.refresh(this.id);
		}
		window.scrollTo({ top: 0, behavior: 'auto' });
	},
	updated() {
		if (this.$route.query?.id == undefined && this.id != undefined) {
			this.id = undefined;
			this.category_name = "Nama Category";
			this.category_desc = "Abstract";
			this.loading = false;
		}
	},
};
