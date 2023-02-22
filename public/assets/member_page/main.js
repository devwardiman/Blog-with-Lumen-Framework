import Dashboard from './page_dashboard.js'
import Account from './page_account.js'

// 2. Define some routes
// Each route should map to a component.
// We'll talk about nested routes later.
const routes = [
	{ path: '/member', component: Dashboard },
	{ path: '/member/account/:id', component: Account },
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
