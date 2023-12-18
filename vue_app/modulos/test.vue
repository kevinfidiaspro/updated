<template>
    <v-row dense>
        <v-col cols="12" md="5">
            <v-text-field v-model="url" outlined label=""></v-text-field>
            <v-btn @click="test" class="white--text" color="blue">test</v-btn>
            <pre>{{response}}</pre>
        </v-col>
    </v-row>

</template>

<script>
    export default {
        data() {
            return {
                url: null,
                response: null
            }
        },

        methods: {
            test() {
                axios.get(this.url).then(res => {
                    console.log(res.data)
                    this.response = res.data
                }, res => {})
            }
        }
    }
</script>