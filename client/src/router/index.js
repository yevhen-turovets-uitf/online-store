import Vue from 'vue';
import Router from 'vue-router';

const Products = () => import('../views/Products.vue');

Vue.use(Router);

const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            redirect: '/products',
        },
        {
            path: '/products',
            name: 'products',
            component: Products,
        },
    ],
    scrollBehavior: () => ({ x: 0, y: 0 }),
});

export default router;
