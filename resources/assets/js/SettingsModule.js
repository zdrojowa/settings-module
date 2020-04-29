import Vue from 'vue';
import MultiSelect from 'vue-multiselect';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import { Datetime } from 'vue-datetime';

window.axios = require('axios');

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);

Vue.component('multiselect', MultiSelect);
Vue.component('datetime', Datetime);
Vue.component('array', require('./components/array.vue').default);
Vue.component('media-selector', require('./components/media-selector.vue').default);
Vue.component('setting', require('./components/setting.vue').default);

const app = new Vue({
    el: '#app'
});
