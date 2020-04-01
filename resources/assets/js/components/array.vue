<template>
    <div>
        <input type="text" v-model="str"/>
        <button class="btn btn-success" @click="add" type="button">Dodaj</button>
        <ul>
            <li v-for="(item, index) in items">
                {{ item }}
                <button class="btn btn-danger" @click="remove(index)" type="button">Usuń</button>
            </li>
        </ul>
    </div>
</template>
<script>
    export default {
        name: 'array',
        props: {
            defaultItems: []
        },
        data() {
            return {
                items: [],
                str: ''
            }
        },
        methods: {
            add: function() {
                this.items.push(this.str);
                this.str = '';
                this.$emit('change', this.items);
            },
            remove: function(index) {
                this.items.splice(index, 1);
                this.$emit('change', this.items);
            }
        },
        created () {
            this.items = this.defaultItems;
        }
    }
</script>
