export default {
	name: "Members",
	template: /*html*/ `
    <main class="container">
        <div class="d-flex align-items-center p-3 my-3 bg-purple rounded shadow-sm">
			<img src="/assets/img/logo_transparent.png" width="48" height="38" class="me-3">
			<div class="lh-1">
                <h1 class="h6 mb-0 lh-1">Members</h1>
                <small>Pemberi Komentar Pada Artikel</small>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h6 class="border-bottom pb-2 mb-0">Daftar Member</h6>
            <div class="d-flex text-muted pt-3" v-for="item in users.data">
                <img class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" :src="item.user_picture" v-if="item.user_picture">
                <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false" v-else><title>Placeholder</title><rect width="100%" height="100%" :fill="getRandomColor(item.name).color"></rect><text x="50%" y="50%" :fill="getBackgroundColor()" dy=".3em">{{getRandomColor(item.name).character}}</text></svg>
                <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                    <div class="d-flex justify-content-between">
                    <strong class="text-gray-dark" v-text="item.displayname"></strong>
                    <div class="row g-1">
                        <div class="col">
							<router-link :to="{ path: '/app/user', query: { id: item.id }}" class="btn btn-sm btn-bd-light rounded-2">Ubah</router-link>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-sm btn-bd-light rounded-2" data-bs-toggle="modal" data-bs-target="#modalChoice" :data-bs-id="item.id">Hapus</button>
                        </div>
                    </div>
                    </div>
                    <span class="d-block" v-text="'@' + item.name"></span>
                </div>
            </div>
			<nav class="mt-3" v-if="page_total > 1">
				<ul class="pagination justify-content-center">
					<li class="page-item" v-if="page > 1">
						<router-link class="page-link" :to="{path: '/app/admin', query: {page: page_previous}}">Previous</router-link>
					</li>
					<li class="page-item disabled" v-else>
						<a class="page-link">Previous</a>
					</li>
					<li class="page-item" v-for="n in page_total">
						<router-link class="page-link" v-if="page != n" :to="{path: '/app/admin', query: {page: n}} " v-text="n"></router-link>
						<a class="page-link active" v-else v-text="n"></a>
					</li>
					<li class="page-item" v-if="page < page_total">
						<router-link class="page-link" :to="{path: '/app/admin', query: {page: page_next}}">Next</router-link>
					</li>
					<li class="page-item disabled" v-else>
						<a class="page-link">Next</a>
					</li>
				</ul>
			</nav>
        </div>
		<div class="modal modal-alert py-5" tabindex="-1" role="dialog" id="modalChoice" ref="modalChoice">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content rounded-3 shadow">
                    <div class="modal-body p-4 text-center">
                        <h5 class="mb-0">Yakin menghapus User?</h5>
                        <p class="mb-0">User yang dihapus tidak dapat dipulihkan!</p>
                    </div>
                    <div class="modal-footer flex-nowrap p-0">
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 border-end" data-bs-dismiss="modal" @click="destroy"><strong>Yes, hapus</strong></button>
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0" data-bs-dismiss="modal">No thanks</button>
                    </div>
                </div>
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
			storedTheme: localStorage.getItem("theme"),
			channel: new BroadcastChannel("theme"),
			useDarkMode: window.matchMedia("(prefers-color-scheme: dark)").matches,
			users: [],
			page: 1,
			page_number: 0,
			page_total: 0,
			page_previous: 0,
			page_next: 1,
			id: 0,
			toast: undefined,
			res_title: "",
			res_message: "",
		};
	},
	methods: {
		refresh() {
			fetch(`/api/user/member?page=${this.page}`, {
				method: "GET",
				headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
				},
			})
				.then((e) => e.json())
				.then((res) => {
					this.users = res;
					this.page = res.page;
					this.page_previous = parseInt(this.page) - 1;
					this.page_next = parseInt(this.page) + 1;
					this.page_number = res.page_number;
					this.page_total  = res.page_total;
					window.scrollTo({ top: 0, behavior: 'auto' });
				});
		},
		getBackgroundColor() {
			if (this.storedTheme !== "light" && this.storedTheme !== "dark") {
				return this.useDarkMode ? "#FFFFFF" : "#000000";
			} else {
				return this.storedTheme == "light" ? "#000000" : "#FFFFFF";
			}
		},
		getRandomColor(name) {
			// get first alphabet in upper case
			const firstAlphabet = name.charAt(0).toLowerCase();

			// get the ASCII code of the character
			const asciiCode = firstAlphabet.charCodeAt(0);

			// number that contains 3 times ASCII value of character -- unique for every alphabet
			const colorNum = asciiCode.toString() + asciiCode.toString() + asciiCode.toString();

			var num = Math.round(0xffffff * parseInt(colorNum));
			var r = (num >> 16) & 255;
			var g = (num >> 8) & 255;
			var b = num & 255;

			return {
				color: "rgb(" + r + ", " + g + ", " + b + ", 0.3)",
				character: firstAlphabet.toUpperCase(),
			};
		},
		destroy() {
			fetch(`/api/user/delete/${this.id}`, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                }
            })
				.then((e) => e.json())
				.then((res) => {
					this.refresh();
					this.res_title = res.status;
					this.res_message = res.message;
					this.toast.show();
				});
		},
	},
	mounted() {
		this.refresh();
		this.channel.addEventListener("message", (event) => {
			console.log("Hello");
			this.storedTheme = event.data;
			if (this.storedTheme !== "light" || this.storedTheme !== "dark") {
				this.useDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;
				const usersBaru = this.users;
				this.users = [];
				this.users = usersBaru;
			}
		});

		this.$refs.modalChoice.addEventListener("show.bs.modal", (event) => {
			this.id = event.relatedTarget.getAttribute("data-bs-id");
		});
		this.toast = new bootstrap.Toast(this.$refs.toastPlacement);
		window.scrollTo({ top: 0, behavior: 'auto' });
	},
	unmounted() {
		this.channel.close();
	},
	updated() {
		if(this.$route.query?.page != undefined && this.page != this.$route.query?.page) {
			this.page = this.$route.query?.page;
			this.refresh();
		}
	}
};
