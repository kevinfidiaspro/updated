<template>
    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="local_date" transition="scale-transition" offset-y min-width="290px">

        <template v-slot:activator="{ on, attrs }">
            <v-text-field filled v-model="local_date" :label="label" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
        </template>

        <v-date-picker v-model="local_date" no-title scrollable>

            <v-spacer></v-spacer>

            <v-btn text color="primary" @click="closeMenu">
                Cancel
            </v-btn>

            <v-btn text color="primary" @click="setDate">
                OK
            </v-btn>

        </v-date-picker>

    </v-menu>
</template>

<script>
    export default {
        props: ['menu', 'date', 'label'],

        data() {
            return {
                local_date: '',
            }
        },

        watch: {
            date(n) {
                local_date = n
            }
        },

        methods: {
            setDate(date) {
                console.log(date);
                //this.$refs.menu.save(date)
                //this.$emit('set_date', date)
            },
            closeMenu() {
                this.$emit('close_menu')
            }
        }
    }
</script>