<template>
    <div>
        <h1>Авторизация</h1>
        <div style="display: flex; flex-direction: column" class="w-25 gap-3">
            <input type="email" name="email" placeholder="Email" v-model="email">
            <input type="password" name="password" placeholder="Пароль" v-model="password">
            <input type="submit" @click.prevent="login" value="Войти">
        </div>
    </div>
</template>

<script>
export default {
    name: "Auth",
    methods: {
        login() {
            axios.post('/api/auth', {
                email: this.email,
                password: this.password,
            })
                .then(data => {
                    localStorage.setItem('user_token', data.data.data.user_token);
                    window.location.href = '/';
                })
        }
    },
    data(){
        return {
            email: null,
            password: null
        }
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
