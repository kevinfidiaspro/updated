<template>
    <div class="background my-container">

        <v-custom-title text="Lista Facturas Recibidas"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="'/form-facturas-recibidas/'+ user_id">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="facturaRecibidas" :search="search" item-key="id" class="elevation-1">

             <template v-slot:item.imagen="{ item }">
             <!--   <a v-for="n,m in item.imagen" :key="m" target="_blank" :href="'/storage/facturaes/recibidos/' + n">-->
                    <v-icon @click="callDown(item)" medium color="primary" class="mr-2">
                       mdi-cloud-download
                    </v-icon>
                <!--</a>
            </template>-->
            <!-- <template v-slot:item.imagen="{ item }">
                <a target="_blank" :href="item.path">
                    <v-icon medium color="orange" class="mr-2">
                        mdi-cloud-download
                    </v-icon>
                </a>-->
            </template> 



            <template v-slot:item.action="{ item }">

                <router-link :to="'/form-facturas-recibidas-update/' + item.id" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteFac(item)" small class="mr-2" color="red">
                    mdi-trash-can
                </v-icon>

            </template>

        </v-data-table>

    </div>

</template>

<script>
    export default {
        data() {
            return {
                search: '',
                facturaRecibidas: [],
                headers: [{
                        text: 'Fecha',
                        value: 'fecha',
                    },
                    {
                        text: 'Proveedor',
                        value: 'proveedor.nombre'
                    },
                    {
                        text: 'Descripción',
                        value: 'descripcion'
                    },
                  
                    {
                        text: 'Archivos',
                        value: 'imagen'
                    },
                    {
                        text: 'Acciones',
                        value: 'action',
                        sortable: false
                    },
                ],
            }
        },
        created() {
            this.getFactRecibidas()
        },
        methods: {
                callDown(doc){

                    let imagenes =JSON.parse( doc.imagen)
                    let originaName = window.location.origin + '/'
                    let pathServer = 'storage/documentos/userId_' + this.user_id + '/factura_recibidas/'
                    let pathDoc = ''
                    let documentImagen = ''
                    for(var r = 0; r < imagenes.length; r++){
                        pathDoc = originaName + pathServer + imagenes[r]
                        documentImagen = imagenes[r]
                        this.downloadFiles(pathDoc, documentImagen)
                    }
                   
                    

                    
               },
               // downAll(){
               //      for (var i = 0; i < this.docs.length; i++) {

               //           this.downloadFiles(this.pathDoc + this.docs[i][0].imagen, this.docs[i][0].imagen)
               //      }
               // },
               downloadFiles(url, filename) {
                 fetch(url).then(function(t) {
                     return t.blob().then((b)=>{
                             var a = document.createElement("a");
                             a.href = URL.createObjectURL(b);
                             a.setAttribute("download", filename);
                             a.click();
                         }
                     );
                 });
                },


            getFactRecibidas() {
                axios.get(`api/facturas-recibidas/${localStorage.getItem('user_id')}`).then(res => {
                    this.facturaRecibidas = res.data.facturaRecibidas
                    // console.log(this.facturaRecibidas)
                }, err => {
                    this.$toast.error('Error consultando facturaRecibidas')
                })
            },
            deleteFac(item) {
                axios.post(`api/facturas-recibidas-delete/${item.id}`).then(res => {
                    this.getFactRecibidas()
                    this.$toast.sucs('factura eliminada')
                }, err => {
                    this.$toast.error('Error eliminando factura')
                })
            },
            descargarArchivos(item) {
              
                //quitamos todos los caracteres que no necesitamos
                let archivos = item.imagen.replaceAll('"', '');
                archivos = archivos.replaceAll('[', ''); 
                archivos = archivos.replaceAll(']', ''); 
                
                //convertimos en array
                let array_archivos=archivos.split(',');
                //descargamos todos los archivos
                array_archivos.forEach(element => {
                    var archivo = document.createElement('a');
                    archivo.setAttribute('href', "/storage/documentos/" + "userId_" + this.user_id + "recibidos/"+element);
                    archivo.setAttribute('download', element);
                    document.body.appendChild(archivo);
                    archivo.click();
                });

            }
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
            user_id(){
                return localStorage.user_id
            }
        }
    }
</script>