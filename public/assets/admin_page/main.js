import Dashboard from './page_dashboard.js'
import NewArticle from './page_new_article.js'
import Category from './page_new_category.js';
import Articles from './page_articles.js';
import Features from './page_features.js';
import Comment from './page_comment.js';
import Categories from './page_categories.js';
import Admin from './page_user_admin.js';
import Member from './page_user_members.js';
import User from './page_new_users.js';
import Tos from './page_app_tos.js';
import PrivacyPolish from './page_app_privacy.js';

// 2. Define some routes
// Each route should map to a component.
// We'll talk about nested routes later.
const routes = [
	{ path: '/app', component: Dashboard },
	{ path: '/app/article', component: NewArticle },
	{ path: '/app/category', component: Category},
	{ path: '/app/articles', component: Articles },
	{ path: '/app/features', component: Features },
	{ path: '/app/comment', component: Comment },
	{ path: '/app/categories', component: Categories },
	{ path: '/app/admin', component: Admin },
	{ path: '/app/member', component: Member },
	{ path: '/app/user', component: User },
	{ path: '/app/privacy-policy', component: PrivacyPolish },
	{ path: '/app/tos', component: Tos },
];

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = VueRouter.createRouter({
	// 4. Provide the history implementation to use. We are using the hash history for simplicity here.
	history: VueRouter.createWebHistory(),
	routes, // short for `routes: routes`
});

const app = Vue.createApp({
	// components: {
	//     'app-header': header,
	// },
	data() {
		return {
			countNumber: 0,
		};
	},
	methods: {
		count() {
			this.countNumber++;
		},
		getOut() {
			fetch("/logout")
				.then((e) => e.json())
				.then((res) => {
					if (res.status == "ok") {
						location.href = "/";
					}
				});
		},
	},
});
// Make sure to _use_ the router instance to make the
// whole app router-aware.
app.use(router);

router.isReady().then(() => {
	// Now the app has started!
	app.mount('#app');
})
