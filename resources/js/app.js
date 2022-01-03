require('./bootstrap');
const Vue = require("vue");

// import Vue from 'vue';
// import VueI18n from 'vue-i18n';

import categoriesComponent from './components/categories.vue';
Vue.component('categories', categoriesComponent);
var categoriesVue = new Vue({
    el: '#categories-vue-wrapper',
});
// Vue.use(VueI18n);
//
// if (typeof window.messages === 'undefined') {
//     window.messages = {}
// }
//
// const i18n = new VueI18n({
//     locale: 'pl', // set locale
//     fallbackLocale: 'en',
//     messages, // set locale messages
// });
//
// const app = new Vue({
//     i18n,
//     el: '.app-container',
//
//     data() {
//
//     }
// });
//
// global.vm = app;

