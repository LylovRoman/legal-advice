<template>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th v-for="column in columns" :key="column.id">{{ column.label }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="issue in issues" :key="issue.id">
                <td>{{ issue.category }}</td>
                <td>{{ issue.client.name }}</td>
                <td><a :href="'/issue/' + issue.id">{{ issue.question }}</a></td>
                <td>{{ (issue.lawyer != '---') ? issue.lawyer.name : '---' }}</td>
                <td>{{ issue.status }}</td>
                <td>{{ issue.created_at | date }}</td>
            </tr>
            </tbody>
        </table>
        <div v-if="role != 'lawyer'">
            <input @click="onlyMyShow" v-model="onlyMy" type="checkbox"> Показывать только мои заявки
            <div style="display: flex; flex-direction: column;">
                <textarea v-model="question" class="w-25" style="min-height: 200px"></textarea>
                <input type="file" @change="uploadImage">
                <select v-model="category" class="w-25">
                    <option value="land disputes">land disputes</option>
                    <option value="family disputes">family disputes</option>
                    <option value="labor disputes">labor disputes</option>
                    <option value="disputes with the traffic police">disputes with the traffic police</option>
                </select>
                <input type="submit" @click.prevent="sendQuestion" class="w-25">
            </div>
        </div>
        <div v-if="role == 'lawyer'" style="display: flex" class="gap-5">
            <div>
                <h4>Фильтровать по статусу</h4>
                <select v-model="status" @change="onlyStatusShow">
                    <option value="">Сбросить</option>
                    <option value="new">Новые</option>
                    <option value="in progress">В работе</option>
                    <option value="completed">Завершенные</option>
                </select>
            </div>
            <div>
                <h4>Фильтровать по дате</h4>
                <input v-model="from" type="date">
                <input v-model="to" type="date">
                <input @click.prevent="onlyDateBetween" type="submit">
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Table",
    methods: {
        sendQuestion() {
            const form = new FormData();
            form.set('category', this.category);
            form.set('question', this.question);
            form.set('image', this.image);
            axios.post('/api/issue/question', form, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token'),
                    'Content-Type': 'form/data',
                }
            }).then(response => {
                window.location.href = '/';
            });
        },
        getIssues() {
            axios.get('/api/issues' + location.search, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                }
            })
            .then(data => {
                this.issues = data.data.data.issues;
            })
        },
        getRole() {
            if (!localStorage.getItem('user_token')){
                window.location.href = '/auth';
            }
            axios.get('/api/role', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token')
                }
            })
                .then(data => {
                    this.role = data.data.data.role;
                })
        },
        onlyMyShow() {
            if (this.onlyMy) {
                window.location.href = '/'
            } else {
                window.location.href = '/?onlyMy=yes'
            }
        },
        onlyStatusShow() {
            if (this.status) {
                window.location.href = '/?status=' + this.status;
            } else {
                window.location.href = '/';
            }
        },
        onlyDateBetween() {
            if (this.from && this.to) {
                window.location.href = '/?from=' + this.from + '&to=' + this.to;
            } else {
                window.location.href = '/';
            }
        },
        uploadImage() {
            this.image = event.target.files[0];
        }
    },
    filters: {
        date: function (value) {
            value = value.toString()
            return value.substr(0, 10);
        }
    },
    computed: {
        onlyMy()
        {
            return location.search.split('onlyMy?=yes')[0] == '?onlyMy=yes';
        }
    },
    data(){
        return {
            issues: [],
            role: undefined,
            columns: [
                { id: 1, label: "Категория" },
                { id: 2, label: "Клиент" },
                { id: 3, label: "Вопрос" },
                { id: 4, label: "Юрист" },
                { id: 5, label: "Статус" },
                { id: 6, label: "Дата" }
            ],
            status: null,
            from: null,
            to: null,
            question: null,
            image: null,
            category: null
        }
    },
    mounted() {
        this.getRole();
        this.getIssues();
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
