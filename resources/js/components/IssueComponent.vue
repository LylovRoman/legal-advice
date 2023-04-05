<template>
    <div class="table-container">
        <div>
            <h5>Категория: {{issue.category}}</h5>
            <h2>[{{issue.status}}] {{ issue.question }}</h2>
            <p>Клиент: {{issue.client.name}}</p>
            <p>Дата: {{issue.created_at}}</p>
            <img :src="issue.image_path">
        </div>
        <div v-if="issue.status != 'new'">
            <h2>{{ issue.response }}</h2>
            <p>Юрист: {{issue.lawyer.name}}</p>
        </div>
        <div v-if="issue.status == 'completed'">
            <h2>{{ issue.comment }}</h2>
            <p>Клиент: {{issue.client.name}}</p>
            <p>Дата: {{issue.updated_at}}</p>
        </div>
        <div v-if="(issue.status == 'new') && (role == 'lawyer')" style="display: flex; flex-direction: column;">
            <textarea v-model="textarea" class="w-25" style="min-height: 200px"></textarea>
            <input type="submit" @click.prevent="sendResponse" class="w-25">
        </div>
        <div v-if="((issue.status == 'in progress') && (role == 'client')) && (id == 'client_id')" style="display: flex; flex-direction: column;">
            <textarea v-model="comment" class="w-25" style="min-height: 200px"></textarea>
            <input type="submit" @click.prevent="sendComment" class="w-25" value="Проблема решена">
        </div>
    </div>
</template>

<script>
import router from "../router";

export default {
    name: "Issue",
    methods: {
        getRole() {
            if (!localStorage.getItem('user_token')){
                router.push('/auth');
            }
            axios.get('/api/role', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                }
            })
                .then(data => {
                    this.id = data.data.data.id;
                    this.role = data.data.data.role;
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
                })
        },
        sendResponse() {
            axios.patch('/api/issue/response/' + document.location.pathname.split('/')[2], {
                response: this.textarea
            }, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                }
            }).then(data => {
                    router.push('/');
                })
        },
        sendComment() {
            axios.patch(`/api/issue/comment/${document.location.pathname.split('/')[2]}`, {
                comment: this.comment
            }, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('user_token')}`
                }
            }).then(() => {
                router.push('/');
            });
        }
    },
    data(){
        return {
            issue: {
                lawyer: {},
                client: {}
            },
            role: undefined,
            id: undefined,
            textarea: null,
            comment: null
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
