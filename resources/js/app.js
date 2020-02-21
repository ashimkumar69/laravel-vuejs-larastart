/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");
window.Vue = require("vue");

let moment = require("moment");
import VueRouter from "vue-router";
import { routes } from "./routes";
import { Form, HasError, AlertError } from "vform";
import VueProgressBar from "vue-progressbar";
import Swal from "sweetalert2";
import Gate from "./gate";

Vue.prototype.$gate = new Gate(window.user);

window.Swal = Swal;

window.Form = Form;
window.Fire = new Vue();

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: toast => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    }
});

window.Toast = Toast;

Vue.use(VueRouter);

const options = {
    color: "#38c172",
    failedColor: "#874b4b",
    thickness: "5px",
    transition: {
        speed: "0.2s",
        opacity: "0.6s",
        termination: 300
    },
    autoRevert: true,
    location: "top",
    inverse: false
};

Vue.use(VueProgressBar, options);

const router = new VueRouter({
    mode: "history",
    routes // short for `routes: routes`
});

Vue.filter("firstCaseToUpper", value => {
    return value.charAt(0).toUpperCase() + value.slice(1);
});
Vue.filter("userCreatedTime", time => {
    return moment(time).format("MMM Do YY");
});
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);
Vue.component(
    "passport-clients",
    require("./components/passport/Clients.vue").default
);

Vue.component(
    "passport-authorized-clients",
    require("./components/passport/AuthorizedClients.vue").default
);

Vue.component(
    "passport-personal-access-tokens",
    require("./components/passport/PersonalAccessTokens.vue").default
);
Vue.component("not-found", require("./components/NotFound.vue").default);
Vue.component("pagination", require("laravel-vue-pagination"));
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    router,
    data() {
        return {
            search: ""
        };
    },
    methods: {
        searchit() {
            Fire.$emit("searching");
        }
    }
});
