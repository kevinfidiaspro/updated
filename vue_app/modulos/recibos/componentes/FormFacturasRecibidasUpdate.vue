<template>

    <div class="background my-container col-10 offset-1 mt-2">
        <v-container>
        <v-custom-title text="Nueva Factura Recibida"></v-custom-title>

        <loader v-if="isloading"></loader>

        <v-form class="mt-5">

            <v-row dense>
                <v-col cols="12" md="2">
                    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="facturaRec.fecha" transition="scale-transition" offset-y min-width="290px">

                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" v-model="facturaRec.fecha" label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                            </v-text-field>
                        </template>

                        <v-date-picker v-model="facturaRec.fecha" no-title scrollable>
                            <v-spacer></v-spacer>

                            <v-btn text color="primary" @click="menu = false">
                                Cancel
                            </v-btn>

                            <v-btn text color="primary" @click="$refs.menu.save(facturaRec.fecha)">
                                OK
                            </v-btn>
                        </v-date-picker>

                    </v-menu>
                </v-col>

                <v-col cols="12" md="3">
                    <v-select filled 
                    v-model="facturaRec.proveedor_id" 
                    :error-messages="errors.errors.proveedor_id ? errors.errors.proveedor_id[0] : null" 
                    :items="proveedores" 
                    item-text="nombre" 
                    item-value="id" 
                    label="Proveedor"></v-select>
                </v-col>

            </v-row>

            <v-row>
                <v-col cols="12" md="5">
                    <v-text-field filled v-model="facturaRec.descripcion" label="Descripción"></v-text-field>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="5">
                    <v-file-input filled prepend-icon="mdi-file-image" ref="file" multiple label="Imagen" v-on:change="handleFileUpload"></v-file-input>
                </v-col>
            </v-row>

            <v-row v-if="facturaRec.id != null && facturaRec.imagen.length>0">
                <v-col cols="12" md="5">
                    <p @click="callDown(facturaRec.imagen)" style="cursor:pointer">
                        Descargar Archivos  
                        <v-icon medium color="primary" class="mr-2" >
                        mdi-cloud-download
                    </v-icon>
                    </p>
                </v-col>
                   
            </v-row>
            <v-row class="mt-3">
                <v-col cols="12">
                    <v-btn rounded depressed @click="saveFactRecibidas" :disabled="isloading" color="success" class="white--text">Guardar</v-btn>
                </v-col>
            </v-row>

        </v-form>

    </v-container>
</div>

</template>

<script>
 import FileInput from '../../../global_components/FileInput.vue'
    export default {
          components: {
               'file-input' : FileInput
          },
        data() {
            return {
                menu: false,
                uploadPercentage: 0,
                proveedores: [],
                facturaRec: {
                    id: null,
                    fecha: new Date().toISOString().substr(0, 10),
                    descripcion: '',
                    proveedor_id: '',
                    imagen: false,
                    user_id: localStorage.getItem('user_id')
                },

                files: [],
                imagePreview: [],
                modeloImagenesNews : []
            }
        },

        created() {

            let u = window.location.hash
            let splithash = u.split('/') 
            
            this.getProveedores()

            //id factura splithash[splithash.length -1]
            this.facturaRecById(splithash[splithash.length -1])
        },

        methods: {
            
            callDown(doc){
                    console.log(doc)
                    let imagenes = JSON.parse(doc) 
                    let originaName = window.location.origin + '/'
                    let pathServer = 'storage/documentos/userId_' + this.userID + '/factura_recibidas/'
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
            setFiles(files) {
                   
                    const filesPreview = files

                    Object.keys(filesPreview).forEach(i => {
                         const file = filesPreview[i];
                         const reader = new FileReader();
                         reader.onload = (e) => {
                             this.imagePreview.push(reader.result);
                         }
                         this.imagePreview = []
                         reader.readAsDataURL(file);

                    });
                  

                    if (files !== undefined) {
                         this.files = files
                         this.disableUploadButtonImage = false
                    }
               },

            facturaRecById(id) {
                
                axios.get(`api/facturas-recibidas-show/`+ id).then(res => {
                  
                   this.facturaRec = res.data.fr
                   JSON.parse(this.facturaRec.imagen)
                }, res => {
                    this.$toast.error('Error consultando Proveedores')
                })
            },

            getProveedores() {
                
                axios.get(`api/get-proveedores/`+ this.facturaRec.user_id).then(res => {
                    this.proveedores = res.data
                }, res => {
                    this.$toast.error('Error consultando Proveedores')
                })
            },


            saveFactRecibidas() {

                let formData = new FormData();

                formData.append('id', this.facturaRec.id)
                formData.append('user_id', this.facturaRec.user_id)
                formData.append('fecha', this.facturaRec.fecha)
                formData.append('descripcion', this.facturaRec.descripcion)
                formData.append('proveedor_id', this.facturaRec.proveedor_id)
                formData.append('imagen', this.modeloImagenesNews)
                
               if (this.modeloImagenesNews.length > 0) {
                    
                        for (let fileSave of this.modeloImagenesNews) {
                            formData.append('imagen[]', fileSave, fileSave.name)

                        }
                   
               }
                
                
                
                   

                axios.post(`api/facturas-recibidas-update/` + this.facturaRec.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
                    }.bind(this)
                }).then(res => {
                    this.$toast.sucs('Albaran guardado con exito')
                    this.$router.push('/lista-facturas-recibidas/' + this.userID)
                }, res => {
                    this.$toast.error('Error guardando albaran')
                })
            },
            handleFileUpload(file) {

                this.modeloImagenesNews = file//array
            },

            handleFileUploadPdf(pdf){
                this.files = pdf
                this.facturaRec.imagen = pdf
                
            }
        },
        computed: {
            isloading() {
                return this.$store.getters.getloading
            },

            errors() {
                return this.$store.getters.geterrors
            },
            userID(){
                return localStorage.user_id
            }
        }
    }
</script>