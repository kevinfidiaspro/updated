<template>
    <!-- ajustamos para que se vea el signo plus...-->
    <v-file-input flat solo @change="onFileChange" :label="label" prepend-icon="mdi-plus-circle-outline"></v-file-input>
</template>

<script>
    export default {
        props: {
            label: {
            //     // default: 'Archivo',
                // type: String
            }
        },
        methods: {
            onFileChange: function(e) {
                this.createImage(e)
            },
            createImage: function(e) {
                let reader = new FileReader()
                reader.onload = (e) => {
                    this.$emit('file_changed', e.target.result)
                };
                reader.readAsDataURL(e)
            },
        }
    }
</script>
