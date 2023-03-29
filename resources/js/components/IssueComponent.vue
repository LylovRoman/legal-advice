<template>
    <div class="table-container">
        <div>
            <h5>Категория: {{issue.category}}</h5>
            <h2>[{{issue.status}}] {{ issue.question }}</h2>
            <p>Клиент: {{issue.client.name}}</p>
            <p>Дата: {{issue.created_at}}</p>
            <img :src="issue.image_path">
        </div>
        <div v-if="(issue.status == 'new') && (role == 'lawyer')" style="display: flex; flex-direction: column;">
            <textarea v-model="textarea" class="w-25" style="min-height: 200px"></textarea>
            <input type="submit" @click.prevent="sendResponse" class="w-25">
        </div>
    </div>
</template>

<script>
export default {
    name: "Issue",
    methods: {
        getRole() {
            axios.get('/api/role', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                }
            })
                .then(data => {
                    this.role = data.data.data.role;
                    console.log(document.location.pathname.split('/')[2]);
                })
        },
        getIssue() {
            axios.get('/api/issue/' + document.location.pathname.split('/')[2], {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                }
            })
                .then(data => {
                    this.issue = data.data.data;
                    console.log(this.issue);
                })
        },
        sendResponse() {
            axios('/api/issue/response/' + document.location.pathname.split('/')[2], {
                method: 'PATCH',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                },
                data: {
                    response: this.textarea
                }
            })
                .then(data => {
                    window.location.href = '/';
                })
        },
    },
    data(){
        return {
            issue: {},
            role: undefined,
            textarea: null
        }
    },
    mounted() {
        this.getRole();
        this.getIssue();
    }
};
</script>

<style>
.table-container {
    overflow-x: auto; /* Enable horizontal scrolling */
}
table {
    border-collapse: collapse;
    width: 100%;
}
th,
td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
th {
    background-color: #f2f2f2;
}
</style>
