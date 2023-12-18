<template functional>
    <v-row dense>
        <v-col cols="auto">
            <v-card style="width:fit-content" color="light-green" dark>
                <v-card-text>
                    <nl2br tag="h4" :text="props.mensaje" />
                    <!--<h4 class="font-weight-regular white--text"></h4>-->
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
    export default {
        props: ['mensaje']
    }
</script>