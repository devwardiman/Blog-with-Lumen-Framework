export default {
	name: "Daftar Artikel",
	template: /*html*/ `
    <main class="bd-main">
        <div class="bd-intro pt-2 ps-lg-2">
            <div class="d-md-flex flex-md-row-reverse align-items-center justify-content-between">
                <div class="mb-3 mb-md-0 d-flex">
                    <router-link :to="{path: '/app/article', query: {type: 'tos'}}" class="btn btn-sm btn-bd-light rounded-2">New Terms Of Service</router-link>
                </div>
                <h1 class="bd-title mb-0" id="content">Terms Of Service</h1>
            </div>
            <p class="bd-lead">Ini adalah daftar Terms Of Service yang telah dibuat</p>
        </div>

        <div class="bd-content ps-lg-2">
            <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative" v-for="item in articles">
                <div class="col p-4 d-flex flex-column position-static">
                    <strong class="d-inline-block mb-2 text-primary">
                        <router-link :to="{path: '/app/category', query: { id : cat.id }}" class="text-decoration-none" v-for="cat in item.cat">
							<span class="badge rounded-pill text-bg-primary">{{ cat.nama }}</span> 
						</router-link>
                    </strong>
                    <h3 class="mb-0" v-text="item.article_title"></h3>
                    <div class="mb-1 text-muted" v-text="formatDate(item.updated_at)"></div>
                    <p class="card-text mb-auto" v-text="item.article_abstract"></p>
                    <div class="row gap-2">
                        <button class="col btn btn-bd-light" @click="gotoArtcile(item.id)">
                            Edit
                        </button>
                        <button class="col btn btn-bd-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" :data-bs-id="item.id" aria-controls="offcanvasRight">
                            Publish
                        </button>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <img :src="item.article_cover" width="250" height="240" style="object-fit: cover;object-position: center center;">
					<div class="ribbon-wrapper ribbon-xl">
						<div class="ribbon bg-info text-lg" v-text="item.article_status" v-if="item.article_status == 'Publish'"></div>
						<div class="ribbon bg-warning text-lg" v-text="item.article_status" v-else></div>
					</div>
                </div>
            </div>
			<nav v-if="page_total > 1">>
				<ul class="pagination justify-content-center">
					<li class="page-item" v-if="page > 1">
						<router-link class="page-link" :to="{path: '/app/articles', query: {page: page_previous}}">Previous</router-link>
					</li>
					<li class="page-item disabled" v-else>
						<a class="page-link">Previous</a>
					</li>
					<li class="page-item" v-for="n in page_total">
						<router-link class="page-link" v-if="page != n" :to="{path: '/app/articles', query: {page: n}} " v-text="n"></router-link>
						<a class="page-link active" v-else v-text="n"></a>
					</li>
					<li class="page-item" v-if="page < page_total">
						<router-link class="page-link" :to="{path: '/app/articles', query: {page: page_next}}">Next</router-link>
					</li>
					<li class="page-item disabled" v-else>
						<a class="page-link">Next</a>
					</li>
				</ul>
			</nav>
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
                    <img class="card-img" width="100%" height="260" :src="article_cover" style="object-fit: cover;object-position: center center;">
                </div>
                <div class="input-group mt-1 mb-3">
                    <input type="file" class="form-control" name="article_cover" id="article_cover">
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
    `,
	data() {
		return {
			articles: [],
			article_id: undefined,
			article_cover: undefined,
			article_cat: [],
			bsOffcanvas: null,
			page: 1,
			page_number: 0,
			page_total: 0,
			page_previous: 0,
			page_next: 1,
		};
	},
	methods: {
		refresh() {
			fetch(`/api/article/tos?page=${this.page}`, {
				method: "GET",
				headers: {
					Accept: "application/json",
				},
			})
				.then((e) => e.json())
				.then((res) => {
					this.articles = res.data.articles;
                    this.page = res.data.paginate.page;
                    this.page_previous = parseInt(this.page) - 1;
                    this.page_next = parseInt(this.page) + 1;
                    this.page_number = res.data.paginate.nomor;
                    this.page_total = res.data.paginate.total;
					window.scrollTo({ top: 0, behavior: 'auto' });
					// const category = JSON.parse(this.articles[0].cat);
					// console.log(category);
				})
				.finally((e) => {
					if (this.article_id != undefined) {
						this.showPublish(this.article_id);
					}
				});
		},
		showPublish(id) {
			const articleFilter = this.findArticleById(id);
			if (articleFilter.length) {
				const article = articleFilter[0];
				this.article_id = article.id;
				this.article_cover = article.article_cover;
				this.article_cat = article.cat ? article.cat : [];
			}
		},
		formatDate(dateX) {
			var date = new Date(dateX);
			var hours = date.getHours();
			var minutes = date.getMinutes();
			var ampm = hours >= 12 ? "PM" : "AM";
			hours = hours % 12;
			hours = hours ? hours : 12; // the hour '0' should be '12'
			minutes = minutes < 10 ? "0" + minutes : minutes;
			var strTime = hours + ":" + minutes + " " + ampm;
			return date.getDate() + "/" + date.getMonth() + 1 + "/" + date.getFullYear() + "  " + strTime;
		},
		findArticleById(id) {
			return this.articles.filter((article) => article.id == id);
		},
		store(event) {
			const formData = new FormData(event.target);
            formData.append("_method", "PUT");
			formData.append("publish", true);
			formData.append("id", this.article_id);
			formData.append("type", "tos");
			formData.append("article_status", event.submitter.getAttribute("data-bs-status"));
			fetch(`/api/article/publish/${this.article_id}`, {
				method: "POST",
				body: formData,
			})
				.then((e) => e.text())
				.then((res) => console.log(res))
				.finally((e) => {
					this.refresh();
					this.bsOffcanvas.hide();
				});
		},
		gotoArtcile(id) {
			this.$router.push({ path: "/app/article", query: { type:'tos', id: id } });
		},
	},
	mounted() {
		this.refresh();
		this.bsOffcanvas = new bootstrap.Offcanvas("#offcanvasRight");
		this.$refs.offcanvasRight.addEventListener("show.bs.offcanvas", (event) => {
			const id = event.relatedTarget.getAttribute("data-bs-id");
			this.showPublish(id);
		});
	},
	updated() {
		if(this.$route.query?.page != undefined && this.page != this.$route.query?.page) {
			this.page = this.$route.query?.page;
			this.refresh();
		}
	}
};
