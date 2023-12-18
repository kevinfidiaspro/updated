<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-row>
            
                <v-col cols="12">
                    <v-toolbar
                        flat
                        color="#1d2735"
                        dark
                        style="border-radius: 5px"
                    >
                        <v-icon class="white--text" style="font-size: 45px"
                            >mdi-account-supervisor-circle</v-icon
                        >
                        <pre><v-toolbar-title><h2>Calendario reuniones</h2></v-toolbar-title></pre>
                    </v-toolbar>
                    <v-tooltip right>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                        fab
                        to="/guardar-reuniones"
                        :loading="isloading"
                        :disabled="isloading"
                        color="orange darken-1"
                        class="mt-2"
                        v-bind="attrs"
                        v-on="on"
                        >
                        <v-icon class="white--text">mdi-plus-box</v-icon>
                        </v-btn>
                    </template>
                    <span>Nueva Reunion</span>
                    </v-tooltip>
                    <v-form class="mt-3"> </v-form>
                </v-col>
            </v-row>
            <v-row>
                
            </v-row>
            <v-row>
                <v-col cols="12">
                    <reunion-picker v-model="reuniones"></reunion-picker>
                </v-col>
            </v-row>
        </v-card>
    </v-container>
</template>

<script>
import ReunionPicker from '../../../components/general/planpicker/reunionpicker.vue';
export default {
    components:{
        ReunionPicker
    },
    data() {
        return {
            rol: 0,
            clientes:[],
            reunion: {},
            agentes: {},
            reuniones: [],
            controles:[],
            acciones:[],
            productos:[]
        };
    },

    created() {
        this.getreuniones();
    
    },
    methods: {
       
      
        getreuniones() {
            const self = this;
                axios.get(`api/get-reuniones`).then(res => {
                    self.reuniones = res.data
                  
                }, err => {
                    this.$toast.error('Error consultando reuniones')
                })
        },
        formatNumber(number) {
            return Number.parseFloat(number.toString())
                .toFixed(2)
                .replace(".", ",")
                .replace(",00", "");
        },
    },
    filters: {
       
    },

    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
        provincias() {
            return this.$store.getters.getprovincias;
        },
    },
};
</script>
<style>
.rowtwo {
    display: flex;
    flex-direction: row;
}
.spacing {
    padding-top: 0 !important;
}
</style>
