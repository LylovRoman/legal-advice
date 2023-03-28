import Vue from 'vue'
import TableComponent from "./components/TableComponent";

require('./bootstrap');

const app = new Vue({
    el: '#app',
    components: {
        TableComponent
    }
});
