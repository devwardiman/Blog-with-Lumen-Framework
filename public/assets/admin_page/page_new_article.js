export default {
    name: "New Article",
    template: /*html*/ `
        <main class="bd-main order-1">
            <div class="bd-intro pt-2 ps-lg-2">
                <div class="d-md-flex flex-md-row-reverse align-items-center justify-content-between">
                    <div class="mb-3 mb-md-0 d-flex">
						<button v-if="!loading && id && type == 'article'" class="btn btn-sm btn-bd-light rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#modalChoice">Delete</button>
						<button v-if="!loading" class="btn btn-sm btn-bd-light rounded-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
							Publish
						</button>
                    </div>
                    <h1 class="bd-title mb-0" id="content" contenteditable v-text="title" @input="onTitleInput"></h1>
                </div>
                <p class="bd-lead" contenteditable v-text="abstract" @input="onAbsrtactInput"></p>
            </div>

            <div class="bd-content ps-lg-2">
				<tinymce-editor config="myconfig" ref="editor"></tinymce-editor>
            </div>
        </main>

		<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" ref="offcanvasRight" aria-labelledby="offcanvasRightLabel">
			<div class="offcanvas-header">
				<h5 class="offcanvas-title" id="offcanvasRightLabel">Publish Article</h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body d-flex flex-nowrap">
				<form @submit.prevent="store" class="w-100 d-flex flex-column flex-shrink-0 p-1 text-bg-dark">
					<p class="m-0 p-0">Cover</p>
					<div class="card">
						<img class="card-img" width="100%" height="260" :src="article_cover" style="object-fit: cover;object-position: center center;" v-if="id">
						<svg class="bd-placeholder-img card-img" width="100%" height="260" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Cover Article" preserveAspectRatio="xMidYMid slice" focusable="false" v-else><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"></rect><text x="50%" y="50%" fill="#dee2e6" dy=".3em">Cover Article</text></svg>
					</div>
					<div class="input-group mt-1 mb-3">
						<input type="file" class="form-control" name="article_cover" id="article_cover">
					</div>
					<div v-if="type == 'article'">
						<p class="m-0 p-0">Feature</p>
						<div class="card">
							<img class="card-img" width="100%" height="140" :src="article_feature" style="object-fit: cover;object-position: center center;" v-if="id">
							<svg class="bd-placeholder-img card-img" width="100%" height="140" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Cover Article" preserveAspectRatio="xMidYMid slice" focusable="false" v-else><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"></rect><text x="50%" y="50%" fill="#dee2e6" dy=".3em">Cover Article</text></svg>
						</div>
						<div class="input-group mt-1 mb-3">
							<input type="file" class="form-control" name="article_feature" id="article_feature">
						</div>
						<p class="m-0 p-0">Categories</p>
						<div class="form-check" v-for="item in categories">
							<input class="form-check-input" type="checkbox" name="categories[]" :value="item.id" :id="'category_' + item.id" checked v-if="catCheckSelected(item.id)">
							<input class="form-check-input" type="checkbox" name="categories[]" :value="item.id" :id="'category_' + item.id" v-else>
							<label class="form-check-label" :for="'category_' + item.id" v-text="item.category_name"></label>
						</div>
					</div>
					<div class="mb-auto"></div>
					<hr>
					<button class="btn btn-sm btn-bd-light rounded-2 mb-2" data-bs-status="Publish" type="submit">
						Publish
					</button>
					<button class="btn btn-sm btn-bd-light rounded-2" data-bs-status="Draf" type="submit">
						Draf
					</button>
				</form>
			</div>
		</div>

		<div class="modal modal-alert py-5" tabindex="-1" role="dialog" id="modalChoice">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content rounded-3 shadow">
                    <div class="modal-body p-4 text-center">
                        <h5 class="mb-3">Yakin menghapus Artikel?</h5>
                        <p class="mb-0">Artikel yang dihapus tidak dapat dipulihkan!</p>
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
    `,
    data() {
        return {
            type: this.$route.query?.type,
            lasttype: undefined,
            useDarkMode: window.matchMedia("(prefers-color-scheme: dark)")
                .matches,
            isSmallScreen: window.matchMedia("(max-width: 1023.5px)").matches,
            categories: [],
            id: undefined,
            title: "Judul",
            abstract: "Abstract",
            article_cover: "",
            article_feature: "",
            article_cat: [],
            loading: false,
            bsOffcanvas: undefined,
            toast: undefined,
            res_title: "",
            res_message: "",
        };
    },
    methods: {
        onTitleInput(e) {
            this.title = e.target.innerText;
        },
        onAbsrtactInput(e) {
            this.abstract = e.target.innerText;
        },
        catCheckSelected(cat) {
            return (
                this.article_cat.filter((cats) => cats.id == cat).length == 1
            );
        },
        refresh(id) {
            this.loading = true;

            if (this.type == "article") {
                fetch(`/api/article?type=${this.type}&id=${id}`, {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                })
                    .then((e) => e.json())
                    .then((res) => {
                        const article = res.data[0];
                        this.title = article.article_title;
                        this.abstract = article.article_abstract;
                        this.$refs.editor.value = article.article_content;
                        this.article_cover = article.article_cover;
                        this.article_feature = article.article_feature;
                        this.article_cat = article.category
                            ? article.category
                            : [];
                    })
                    .finally((e) => {
                        this.loading = false;
                    });
            } else {
                fetch(`/api/article/${this.type}?id=${id}`, {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                })
                    .then((e) => e.json())
                    .then((res) => {
                        const article = res.data[0];
                        this.title = article.article_title;
                        this.abstract = article.article_abstract;
                        this.$refs.editor.value = article.article_content;
                        this.article_cover = article.article_cover;
                    })
                    .finally((e) => {
                        this.loading = false;
                    });
            }
        },
        refreshCat() {
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
                });
        },
        store(event) {
            const formData = new FormData(event.target);
            formData.append("article_title", this.title);
            formData.append("article_abstract", this.abstract);
            formData.append("article_content", this.$refs.editor.value);
            formData.append(
                "article_status",
                event.submitter.getAttribute("data-bs-status")
            );
            formData.append("article_type", this.type);
            if (this.id == undefined) {
                formData.append("store", true);
                fetch("/api/article/create", {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: formData,
                })
                    .then((e) => e.json())
                    .then((res) => {
                        this.bsOffcanvas.hide();
                        this.res_title = res.status;
                        this.res_message = res.message;
                        this.toast.show();
                        if (res.status == "ok") {
                            this.$router.push({
                                path: "/app/article",
                                query: { type: this.type, id: res.data.id },
                            });
                        }
                        // this.$router.push("/app/articles");
                    });
            } else {
                formData.append("_method", "PATCH");
                formData.append("type", this.type);
                fetch(`/api/article/update/${this.id}`, {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: formData,
                })
                    .then((e) => e.json())
                    .then((res) => {
                        this.bsOffcanvas.hide();
                        this.res_title = res.status;
                        this.res_message = res.message;
                        this.toast.show();
                        this.refresh(this.id);
                        // this.$router.push("/app/articles");
                    });
            }
        },
        destroy() {
            const formData = new FormData();
            formData.append("_method", "DELETE");
            formData.append("type", this.type);
            fetch(`/api/article/delete/${this.id}`, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            })
                .then((e) => e.text())
                .then((res) => {
                    this.$router.push("/app/articles");
                });
        },
    },
    created() {
        window.myconfig = {
            name: "editor",
            promotion: false,
            plugins:
                "preview importcss searchreplace autolink directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons",
            editimage_cors_hosts: ["picsum.photos"],
            menubar: "file edit view insert format tools table help",
            toolbar:
                "undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl",
            toolbar_sticky: true,
            toolbar_sticky_offset: this.isSmallScreen ? 64 : 65,
            image_advtab: true,
            link_list: [
                {
                    title: "My page 1",
                    value: "https://www.tiny.cloud",
                },
                {
                    title: "My page 2",
                    value: "http://www.moxiecode.com",
                },
            ],
            image_list: [
                {
                    title: "My page 1",
                    value: "https://www.tiny.cloud",
                },
                {
                    title: "My page 2",
                    value: "http://www.moxiecode.com",
                },
            ],
            image_class_list: [
                {
                    title: "None",
                    value: "",
                },
                {
                    title: "Some class",
                    value: "class-name",
                },
            ],
            importcss_append: true,
            file_picker_callback: (callback, value, meta) => {
                /* Provide file and text for the link dialog */
                if (meta.filetype === "file") {
                    callback("https://www.google.com/logos/google.jpg", {
                        text: "My text",
                    });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === "image") {
                    callback("https://www.google.com/logos/google.jpg", {
                        alt: "My alt text",
                    });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === "media") {
                    callback("movie.mp4", {
                        source2: "alt.ogg",
                        poster: "https://www.google.com/logos/google.jpg",
                    });
                }
            },
            templates: [
                {
                    title: "New Table",
                    description: "creates a new table",
                    content:
                        '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>',
                },
                {
                    title: "Starting my story",
                    description: "A cure for writers block",
                    content: "Once upon a time...",
                },
                {
                    title: "New list with dates",
                    description: "New List with dates",
                    content:
                        '<div class="mceTmpl"><span class="cdate">cdate</span><br><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>',
                },
            ],
            template_cdate_format:
                "[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]",
            template_mdate_format:
                "[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]",
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar:
                "bold italic | quicklink h2 h3 blockquote quickimage quicktable",
            noneditable_class: "mceNonEditable",
            toolbar_mode: "sliding",
            contextmenu: "link image table",
            skin: this.useDarkMode ? "oxide-dark" : "oxide",
            content_css: this.useDarkMode ? "dark" : "default",
            content_style:
                "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
            setup: (editor) => {
                editor.on("init", (e) => {
                    if (this.$route.query?.id) {
                        this.id = this.$route.query?.id;
                        this.refresh(this.id);
                    }
                });
            },
        };
    },
    mounted() {
        this.refreshCat();
        // if (this.id) {
        // 	this.refresh(this.id);
        // }
        this.title = `Judul ${this.type}`;
        this.bsOffcanvas = new bootstrap.Offcanvas("#offcanvasRight");
        this.toast = new bootstrap.Toast(this.$refs.toastPlacement);
        window.scrollTo({ top: 0, behavior: "auto" });
    },
    updated() {
        this.type = this.$route.query?.type;
        if (this.$route.query?.id == undefined && this.id != undefined) {
            this.id = undefined;
            this.title = `Judul ${this.type}`;
            this.abstract = "Abstract";
            this.$refs.editor.value = "";
            this.article_cover = undefined;
            this.article_feature = undefined;
            this.article_cat = [];
            this.loading = false;
        } else if (this.$route.query?.id != undefined && this.id == undefined) {
            this.id = this.$route.query?.id;
            this.refresh(this.id);
        }

        if (this.type != this.lasttype) {
            this.lasttype = this.type;
            this.title = `Judul ${this.type}`;
        }
    },
    unmounted() {},
};
