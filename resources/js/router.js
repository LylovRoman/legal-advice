import Vue from 'vue'
import VueRouter from "vue-router";
import TableComponent from "./components/TableComponent";
import AuthComponent from "./components/AuthComponent";
import IssueComponent from "./components/IssueComponent";

Vue.use(VueRouter);

export default new VueRouter({
    mode: "history",
    routes: [
        {
            name: 'index',
            path: '/',
            component: TableComponent
        },
        {
            name: 'auth',
            path: '/auth',
            component: AuthComponent
        },
        {
            name: 'issue',
            path: '/issue/:id',
            component: IssueComponent
        }
    ]
})
