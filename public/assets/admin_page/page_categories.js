export default {
	name: "Categories",
	template: /*html*/ `
    <main class="bd-main">
        <div class="bd-intro pt-2 ps-lg-2">
            <div class="d-md-flex flex-md-row-reverse align-items-center justify-content-between">
                <div class="mb-3 mb-md-0 d-flex">
                    <router-link to="/app/category" class="btn btn-sm btn-bd-light rounded-2">New Category</router-link>
                </div>
                <h1 class="bd-title mb-0" id="content">Daftar Category</h1>
            </div>
            <p class="bd-lead">Ini adalah daftar Category yang telah dibuat</p>
        </div>

        <div class="bd-content ps-lg-2">
            <div class="bg-body rounded shadow-sm">
                <div class="d-flex text-muted pt-3 placeholder-wave" v-if="loading">
                    <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
                    <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                        <div class="d-flex justify-content-between">
                            <strong class="text-gray-dark mb-2 placeholder">Memuat Nama Categori</strong>
                        </div>
                        <span class="d-block placeholder">Memuat Deskripsi Category</span>
                    </div>
                </div>
                <div class="d-flex text-muted pt-3 placeholder-wave" v-if="loading">
                    <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
                    <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                        <div class="d-flex justify-content-between">
                            <strong class="text-gray-dark mb-2 placeholder">Memuat Nama Categori</strong>
                        </div>
                        <span class="d-block placeholder">Memuat Deskripsi Category</span>
                    </div>
                </div>
                <div class="d-flex text-muted pt-3 placeholder-wave" v-if="loading">
                    <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
                    <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                        <div class="d-flex justify-content-between">
                            <strong class="text-gray-dark mb-2 placeholder">Memuat Nama Categori</strong>
                        </div>
                        <span class="d-block placeholder">Memuat Deskripsi Category</span>
                    </div>
                </div>
                <div class="d-flex text-muted pt-3" v-for="item in categories" v-else>
                    <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"></rect><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
                    <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                        <div class="d-flex justify-content-between">
                            <strong class="text-gray-dark" v-text="item.category_name"></strong>
                            <button class="btn btn-outline-primary m-0 py-0" @click="gotoCategory(item.id)">Edit</button>
                        </div>
                        <span class="d-block" v-text="item.category_desc"></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    `,
	data() {
		return {
			categories: [],
            loading: false,
		};
	},
	methods: {
		refresh() {
            this.loading = true;
			fetch("/api/category", {
				method: "GET",
				headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
				},
			})
				.then((e) => e.json())
				.then((res) => {
					this.categories = res.data;
				}).finally(e => {
                    this.loading = false;
                });
		},
		gotoCategory(id) {
			this.$router.push({ path: "/app/category", query: { id: id } });
		},
	},
	mounted() {
		this.refresh();
		window.scrollTo({ top: 0, behavior: 'auto' });
	},
};
