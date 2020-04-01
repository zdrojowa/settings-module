window.Vue   = require('vue');
window.axios = require('axios');

import { Datetime } from 'vue-datetime';

Vue.component('datetime', Datetime);
Vue.component('array', require('./components/array.vue').default);
Vue.component('setting', require('./components/setting.vue').default);

const app = new Vue({
    el: '#app'
});
