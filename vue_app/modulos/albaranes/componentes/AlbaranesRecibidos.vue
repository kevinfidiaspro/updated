<template>
    <div class="background my-container">

        <v-custom-title text="Lista Albaranes Recibidos"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="'/form-albaranes-recibidos/'+ user_id">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="albaranes" :search="search" item-key="id" class="elevation-1">

             <template v-slot:item.pdf="{ item }">
             <!--   <a v-for="n,m in item.pdf" :key="m" target="_blank" :href="'/storage/albaranes/recibidos/' + n">-->
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

                <router-link :to="'/form-albaranes-recibidos-update/'+ user_id + '/' + item.id" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteAlbaran(item)" small class="mr-2" color="red">
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
                albaranes: [],
                headers: [{
                        text: 'Fecha',
                        value: 'fecha',
                    },
                    {
                        text: 'Proveedor',
                        value: 'proveedor'
                    },
                    {
                        text: 'Descripción',
                        value: 'descripcion'
                    },
                  
                    {
                        text: 'Archivos',
                        value: 'pdf'
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
            this.getAlbaranes()
        },
        methods: {
            callDown(doc){
                   
                    let imagenes = JSON.parse(doc.pdf) 
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
            getAlbaranes() {
                axios.get(`api/get-albaranes/${localStorage.getItem('user_id')}`).then(res => {
                    this.albaranes = res.data.albaranes
                }, err => {
                    this.$toast.error('Error consultando albaranes')
                })
            },
            deleteAlbaran(item) {
                axios.get(`api/delete-albaran/${item.id}`).then(res => {
                    this.albaranes.splice(this.albaranes.indexOf(item), 1)
                    this.$toast.sucs('Albaran eliminado')
                }, err => {
                    this.$toast.error('Error eliminando albaran')
                })
            },
            descargarArchivos(item) {
                //quitamos todos los caracteres que no necesitamos
                let archivos = item.pdf.replaceAll('"', '');
                archivos = archivos.replaceAll('[', ''); 
                archivos = archivos.replaceAll(']', ''); 
                
                //convertimos en array
                let array_archivos=archivos.split(',');
                //descargamos todos los archivos
                array_archivos.forEach(element => {
                    var archivo = document.createElement('a');
                    archivo.setAttribute('href', "https://fidiasgold.com/storage/albaranes/recibidos/"+element);
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