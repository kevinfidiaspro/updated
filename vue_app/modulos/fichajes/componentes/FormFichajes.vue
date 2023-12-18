<template>
    
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title
                        >Guardar / Editar Fichaje</v-toolbar-title
                    >
                </v-toolbar>
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12" md="4">
                            <v-select  v-model="fichaje.usuario_id" :items="usuarios" item-text="nombre" item-value="id" label="Usuarios"></v-select>
                        </v-col>
                   
                        
                        <v-col cols="12" md="4">
                            <date-select label="Fecha" v-model="fichaje.fecha">
                            </date-select>                            
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field label="Hora" v-mask="`##:##`" v-model="fichaje.hora"></v-text-field>
                        </v-col>
                    </v-row>
                  
                    <v-row class="mt-3">
                <!-- Botones Navegacion -->
                <v-col cols="12">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                @click="$router.push('/lista-social')"
                                :loading="isloading"
                                :disabled="isloading"
                                color="blue"
                                class="mx-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon class="white--text"
                                    >mdi-arrow-left-bold-outline</v-icon
                                >
                            </v-btn>
                        </template>
                        <span>Volver</span>
                    </v-tooltip>
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                @click="saveFichaje"
                                :loading="isloading"
                                :disabled="isloading"
                                color="success"
                                class="mx-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon class="white--text"
                                    >mdi-content-save-all</v-icon
                                >
                            </v-btn>
                        </template>
                        <span>Guardar</span>
                    </v-tooltip>
                </v-col>
            </v-row>
                </v-card-text>
            </v-card>
        
        </v-form>

   
</template>

<style>
.v-window__container {
}
</style>
<script>
export default {
    props:['value'],
    watch:{
        value(val){
            this.fichaje = val;
        },
        fichaje(val){
            this.$emit('input',val);
        }
    },
    data() {
        return {
            
        nombre:'',
        index:'',
            fichaje: {
                id: null,
                usuario_id:null,
                fecha:null,
                hora:null,
            },
            usuarios: [],
            estados: [],
            imagePreview: [],
            uploadPercentage: 0,
        };
    },
    created() {
        this.getUsuarios();
        this.fichaje = this.value??{};
        console.log(this.fichaje);
        if (this.$route.query.id) {
            this.getFichaje(this.$route.query.id);
        }
    },
    methods: {
        getUsuarios() {
                axios.post(`api/get-usuarios-empleados`).then(res => {
                    this.usuarios = res.data.users.data;
                    this.usuarios.unshift('')
                }, err => {
                    this.$toast.error('Error consultando Empleados')
                })
            },
        getFichaje(id){
            axios.get(`api/get-fichaje/${id}`).then(res => {
                    this.fichaje = res.data.data;
                }, err => {
                    this.$toast.error('Error consultando Fichajes')
                })
        },
        saveFichaje(){
            axios.post(`api/save-fichaje`,this.fichaje).then(res => {
                    this.$toast.sucs('Fichaje guardado con exito');
                    this.fichaje = {};
                    this.$emit('done');
                }, err => {
                    this.$toast.error('Error consultando Empleados')
                })
        }
    },

    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
        errors() {
            return this.$store.getters.geterrors;
        },
    },
};
</script>
