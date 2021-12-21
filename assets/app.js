/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import './bootstrap';

import Vue from 'vue'
import App from './App.vue'

import axios from 'axios'
import VueAxios from 'vue-axios'

import Snotify from 'vue-snotify';
Vue.use(VueAxios, axios)
Vue.use(Snotify)

new Vue({
    el: '#app',
    components: {App}
})

