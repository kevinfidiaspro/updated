<template functional>
    <v-col cols="12" md="3">
        <v-row dense>
            <v-col cols="12" sm="6" md="6"><b>Subtotal:</b></v-col>
            <v-col cols="12" sm="6" md="6" align="right">{{ props.recibo.sub_total }}€</v-col>
        </v-row>

        <v-row dense>
            <v-col cols="12" sm="6" md="6"><b>IVA:</b> </v-col>
            <v-col cols="12" sm="6" md="6" align="right">{{ props.recibo.iva }}€</v-col>
        </v-row>

        <v-row dense v-if="props.tipo != 'parte-trabajo'">
            <v-col cols="12" sm="6" md="6"><b>Descuento:</b></v-col>
            <v-col cols="12" sm="6" md="6" align="right">{{ props.recibo.total_descuento }}€</v-col>
        </v-row>

        <v-row dense>
            <v-col cols="12" sm="6" md="6"><b>Total:</b></v-col>
            <v-col cols="12" sm="6" md="6" align="right">{{ props.recibo.total }}€</v-col>
        </v-row>

    </v-col>
</template>
<script>
    export default {
        props: ['recibo', 'tipo', 'has_iva']
    }
</script>