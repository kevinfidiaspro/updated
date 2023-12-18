<template>
    <v-dialog v-model="dialog" persistent max-width="450">
        <v-card>
            <v-card-title class="text-h5 aviso" style="justify-content: center; background:#1d2735; color:white">
                {{titulo}}
            </v-card-title>
            <v-card-text class="pa-4">
                <h2 class="text-center">{{texto}}</h2>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" class="white--text" large @click="dialog = false">Cerrar</v-btn>
                <v-btn color="green" class="white--text" large @click="confirm">Si, Fichar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: ['titulo', 'texto'],
        name: 'v-confirm-dialog',
        data() {
            return {
                dialog: false
            }
        },
        created() {
            this.$parent.$on('open', () => this.dialog = true)
        },
        methods: {
            confirm() {
                this.dialog = false
                this.$emit('confirm')
            }
        }
    }
</script>