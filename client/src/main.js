import Vue from 'vue';
import VueCarousel from 'vue-carousel';
import App from './App.vue';
import store from './store';
import router from './router';

new Vue({
    router,
    store,
    VueCarousel,
    render: h => h(App),
}).$mount('#app');
