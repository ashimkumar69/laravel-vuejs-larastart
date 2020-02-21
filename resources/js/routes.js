import Dashbord from "./components/Dashbord.vue";
import Profile from "./components/Profile.vue";
import Users from "./components/Users.vue";
import Developer from "./components/Developer.vue";
import NotFound from "./components/NotFound.vue";
import Invoice from "./components/Invoice.vue";

export const routes = [
    { path: "/dashbord", component: Dashbord },
    { path: "/profile", component: Profile },
    { path: "/users", component: Users },
    { path: "/developer", component: Developer },
    { path: "/invoice", component: Invoice },
    { path: "*", component: NotFound }
];
