<template functional>
    <v-col cols="12" md="3">
        <v-row dense>
            <v-col cols="12" sm="6" md="6"><b>Total:</b></v-col>
            <v-col cols="12" sm="6" md="6" align="right">{{ props.total }}€</v-col>
        </v-row>
    </v-col>
</template>

<script>
    export default {
        props: ['total']
    }
</script>