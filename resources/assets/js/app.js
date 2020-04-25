
require('./bootstrap');

window.Vue = require('vue');
Vue.use(require('vue-chat-scroll'));

Vue.component('chat-component', require('./components/ChatComponent.vue'));
Vue.component('game-component', require('./components/GameComponent.vue'));


const app = new Vue({
    el: '#app'
});
