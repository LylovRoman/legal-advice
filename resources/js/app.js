import Vue from 'vue'
import TableComponent from "./components/TableComponent.vue";
import AuthComponent from "./components/AuthComponent.vue";
import IssueComponent from "./components/IssueComponent.vue";

require('./bootstrap');

const app = new Vue({
    el: '#app',
    components: {
        TableComponent,
        AuthComponent,
        IssueComponent
    }
});
