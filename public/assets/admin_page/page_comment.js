export default {
    name: "Members",
    template: /*html*/ `
    <main class="container">
        <div class="d-flex align-items-center p-3 my-3 bg-purple rounded shadow-sm">
			<img src="/assets/img/logo_transparent.png" width="48" height="38" class="me-3">
			<div class="lh-1">
                <h1 class="h6 mb-0 lh-1">Komentar</h1>
                <small>Komentar dikirim oleh member Pada Artikel</small>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm">
			<div class="row border-bottom pb-2">
				<div class="col">
					<h6 class="mb-0">Daftar Komentar</h6>
				</div>
				<div class="col-auto">
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" v-model="justDelete">
						<label class="form-check-label" for="flexSwitchCheckChecked">Hapus Jangan Tanya</label>
					</div>
				</div>
			</div>
            <div class="d-flex text-muted pt-3" v-for="item in comments.comments" :key="item.id">
                <img class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" src="/assets/img/logo_transparent.png">
                <div class="pb-1 mb-0 small lh-sm border-bottom w-100">
                    <div class="d-flex justify-content-between align-items-center">
						<strong class="text-gray-dark" v-text="item.user.displayname"></strong>
						<div class="row g-1">
							<div class="col">
								<button type="button" class="btn btn-sm btn-bd-light rounded-2" data-bs-toggle="modal" data-bs-target="#modalChoice" :data-bs-id="item.id">Hapus</button>
							</div>
						</div>
                    </div>
					<figure>
						<blockquote class="blockquote">
							<p v-html="item.comment_content"></p>
						</blockquote>
						<figcaption class="blockquote-footer" v-text="'by @' + item.user.name + ' di ' + item.article.article_title"></figcaption>
					</figure>
                </div>
            </div>

			<nav class="mt-5" v-if="page_total > 1">
				<ul class="pagination justify-content-center">
					<li class="page-item" v-if="page > 1">
						<router-link class="page-link" :to="{path: '/app/comment', query: {page: page_previous}}">Previous</router-link>
					</li>
					<li class="page-item disabled" v-else>
						<a class="page-link">Previous</a>
					</li>
					<li class="page-item" v-for="n in page_total">
						<router-link class="page-link" v-if="page != n" :to="{path: '/app/comment', query: {page: n}} " v-text="n"></router-link>
						<a class="page-link active" v-else v-text="n"></a>
					</li>
					<li class="page-item" v-if="page < page_total">
						<router-link class="page-link" :to="{path: '/app/comment', query: {page: page_next}}">Next</router-link>
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
                        <h5 class="mb-0">Yakin menghapus Komentar Artikel?</h5>
                        <p class="mb-0">Komentar yang dihapus tidak dapat dipulihkan!</p>
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
            useDarkMode: window.matchMedia("(prefers-color-scheme: dark)")
                .matches,
            comments: [],
            page: 1,
            page_number: 0,
            page_total: 0,
            page_previous: 0,
            page_next: 1,
            id: 0,
            toast: undefined,
            res_title: "",
            res_message: "",
            justDelete: false,
            modalChoice: undefined,
        };
    },
    methods: {
        refresh() {
            fetch(`/api/comment?&page=${this.page}`, {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((e) => e.json())
                .then((res) => {
                    this.comments = res.data;
                    this.page = res.data.paginate.page;
                    this.page_previous = parseInt(res.data.paginate.page) - 1;
                    this.page_next = parseInt(res.data.paginate.page) + 1;
                    this.page_number = res.data.paginate.nomor;
                    this.page_total = res.data.paginate.total;
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
            const colorNum =
                asciiCode.toString() +
                asciiCode.toString() +
                asciiCode.toString();

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
            const formData = new FormData();
            formData.append("id", this.id);
            formData.append("destroy", true);
            fetch(`/api/comment/delete/${this.id}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
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
                this.useDarkMode = window.matchMedia(
                    "(prefers-color-scheme: dark)"
                ).matches;
                const usersBaru = this.users;
                this.users = [];
                this.users = usersBaru;
            }
        });

        this.modalChoice = new bootstrap.Modal("#modalChoice");

        this.$refs.modalChoice.addEventListener("shown.bs.modal", (event) => {
            this.id = event.relatedTarget.getAttribute("data-bs-id");
            if (this.justDelete) {
                this.destroy();
                this.modalChoice.hide();
            }
        });
        this.toast = new bootstrap.Toast(this.$refs.toastPlacement);
        window.scrollTo({ top: 0, behavior: "auto" });
    },
    unmounted() {
        this.channel.close();
    },
    updated() {
        if (
            this.$route.query?.page != undefined &&
            this.page != this.$route.query?.page
        ) {
            this.page = this.$route.query?.page;
            this.refresh();
        }
    },
};
