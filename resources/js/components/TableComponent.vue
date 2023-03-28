<template>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th v-for="column in columns" :key="column.id">{{ column.label }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(row, index) in rows" :key="row.id">
                <td v-for="(cell, cellIndex) in row.cells" :key="cellIndex">{{ cell }}</td>
                <td><button @click="deleteRow(index)">Удалить</button></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "Table",
    methods: {
        deleteRow(index) {
            this.rows.splice(index, 1);
        },
        getUsers() {
            axios.get('/users')
                .then(data => {
                    console.log(data.data);
                })
        }
    },
    data(){
        return {
            columns: [
                { id: 1, label: "Имя" },
                { id: 2, label: "Возраст" },
                { id: 3, label: "Email" }
            ],
            rows: [
                { id: 1, cells: ["John Smith", 25, "john.smith@email.com"] },
                { id: 2, cells: ["Jane Doe", 30, "jane.doe@email.com"] }
            ]
        }
    },
    mounted() {
        this.getUsers();
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
