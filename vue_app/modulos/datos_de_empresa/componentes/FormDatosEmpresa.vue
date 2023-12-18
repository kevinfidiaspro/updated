<template>
	<div>
		<v-container>

        <v-custom-title text="Editar Datos de Empresa"></v-custom-title>

      

         <div>       
        <v-custom-title text="Guardar / Editar Usuario" 
            class="black--text col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 mx-4 px-4">
        </v-custom-title>
        <v-row class="mt-3">
            <v-col cols="12" class="offset-9">
               
                <v-btn rounded depressed 
                    @click="updateUsuario" :disabled="isloading"
                    color="success" class="white--text col-1 ml-0">Actualizar</v-btn>
            </v-col>
        </v-row>

        <div class="background my-container col-10 offset-1 mt-2">
            <v-container>
                <loader v-if="isloading"></loader>
                <v-form class="mt-3">
                    <v-row dense>
                        <v-col cols="12" md="2" class="offset-10">
                           <v-avatar size="120px">
                                 <img style="border-radius:50%" 
                                  :src="imagePreview[0]" 
                                  v-if="imagePreview.length > 0" 
                                  class="img-thumbnails" 
                                  width="50%" height="100">

                                   <img style="border-radius:50%" src="/default.png" v-if="imagePreview.length == 0 && usuario.avatar  == null " class="img-thumbnails" width="50%" height="100">


                                   <img style="border-radius:50%" src="/default.png" 
                                   v-if="!usuario.avatar  && imagePreview.length == 0" 
                                   class="img-thumbnails" width="50%" height="100">


                                   <img 
                                        v-if="usuario.avatar && imagePreview.length == 0"
                                        style="border-radius:50%" 
                                        :src="uri + '/storage/users/userId_' + usuario.id + '/' + usuario.avatar" 
                                        class="img-thumbnails" 
                                        width="50%" 
                                        height="100">
                                 <file-input 
                                        class="inputFile" 
                                       :files="files" 
                                       v-on:file-change="setFiles" 
                                       file-clear="clearFiles"
                                       id="inputFile">
                                       
                                   </file-input>
                            </v-avatar>
                        </v-col>
                    </v-row>
                    <v-row dense>
                        <v-col class="offset-1" cols="10" xs="10" sm="10" md="4" lg="4" xl="4">
                            <v-text-field  single-line outlined 
                            :error-messages="errors.errors.name ? errors.errors.name[0] : null" 
                            v-model="usuario.name" 
                            label="Nombre" 
                            required></v-text-field>
                        </v-col>
                        <v-col cols="10" xs="10" sm="10" md="4" lg="4" xl="4">
                            <v-text-field  single-line outlined 
                            :error-messages="errors.errors.nombre_fiscal ? errors.errors.nombre_fiscal[0] : null"
                            v-model="usuario.nombre_fiscal" 
                            label="Nombre Fiscal" 
                            required></v-text-field>
                        </v-col>
                        <v-col cols="10" xs="10" sm="10" md="2" lg="2" xl="2">
                            <v-text-field  single-line outlined  
                            :error-messages="errors.errors.cif ? errors.errors.cif[0] : null" 
                            v-model="usuario.cif" 
                            label="CIF" 
                            counter maxlength="9" minlength="9" 
                            required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row dense>
                        <v-col class="offset-1" cols="10" xs="10" sm="10" md="5" lg="5" xl="5">
                            <v-text-field  single-line outlined 
                                :error-messages="errors.errors.direccion ? errors.errors.direccion[0] : null"  
                                label="Direccion"
                                v-model="usuario.direccion">
                            </v-text-field>
                        </v-col>
                        <v-col cols="10" xs="10" sm="10" md="3" lg="3" xl="3">
                            <v-text-field  single-line outlined 
                            :error-messages="errors.errors.ciudad ? errors.errors.ciudad[0] : null" 
                            v-model="usuario.ciudad" 
                            label="Localidad" 
                            :counter="60"
                            required></v-text-field>
                        </v-col>
                        <v-col cols="10" xs="10" sm="10" md="2" lg="2" xl="2">
                            <v-select  single-line outlined 
                                :error-messages="errors.errors.provincia_id ? errors.errors.provincia_id[0] : null"  
                                :items="provincias" 
                                item-value="id" 
                                item-text="nombre" 
                                label="Provincia" 
                                v-model="usuario.provincia_id"> 
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row dense>
                        <v-col class="offset-1" cols="10" xs="10" sm="10" md="4" lg="4" xl="4">
                            <v-text-field  single-line outlined 
                            :error-messages="errors.errors.email ? errors.errors.email[0] : null" 
                            v-model="usuario.email" 
                            label="Email" 
                            required></v-text-field>
                        </v-col>
                        <v-col cols="10" xs="10" sm="10" md="6" lg="6" xl="6">
                            <v-text-field  single-line outlined 
                            :error-messages="errors.errors.telefono ? errors.errors.telefono[0] : null" 
                            v-model="usuario.telefono" 
                            :rules="[rules.number_rule]" 
                            counter 
                            maxlength="9"
                            label="Teléfono" 
                            required></v-text-field>
                        </v-col>
                      
                    </v-row>
                    <v-row dense>
                        <v-col class="offset-1" cols="10" xs="10" sm="10" md="10" lg="10" xl="10">
                            <v-text-field  single-line outlined 
                            :error-messages="errors.errors.cuenta ? errors.errors.cuenta[0] : null" 
                            v-model="usuario.cuenta" 
                            label="Cuenta Bancaria" 
                            counter maxlength="20" minlength="20" 
                            required></v-text-field>
                        </v-col>
                    </v-row>

                </v-form>

            </v-container>    
        </div>
    </div>

<!-- 
      <v-form class="mt-5">
            <v-row>
                <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                </v-col>
                 <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                </v-col>
                 <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                </v-col>
                 <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                  <img style="border-radius:50%" 
                  :src="imagePreview[0]" 
                  v-if="imagePreview.length > 0" 
                  class="img-thumbnails" 
                  width="50%" height="100">

                   <img style="border-radius:50%" src="/default.png" v-if="imagePreview.length == 0 && this.editMode == false" class="img-thumbnails" width="50%" height="100">


                   <img style="border-radius:50%" src="/default.png" 
                   v-if="!usuario.avatar && this.editMode == true && imagePreview.length == 0" 
                   class="img-thumbnails" width="50%" height="100">


                   <img 
                        v-if="this.editMode == true && usuario.avatar && imagePreview.length == 0"
                        style="border-radius:50%" 
                        :src="uri + '/storage/users/userId_' + usuario.id + '/' + usuario.avatar" 
                        class="img-thumbnails" 
                        width="50%" 
                        height="100">
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-text-field filled 
                    :error-messages="errors.errors.name ? errors.errors.name[0] : null" 
                    v-model="usuario.name" 
                    label="Nombre" 
                    required></v-text-field>
                </v-col>

                 <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-text-field filled 
                    :error-messages="errors.errors.nombre_fiscal ? errors.errors.nombre_fiscal[0] : null"
                     v-model="usuario.nombre_fiscal" 
                     label="Nombre Fiscal" 
                     required></v-text-field>
                </v-col>

                <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-text-field filled  
                    :error-messages="errors.errors.cif ? errors.errors.cif[0] : null" 
                    v-model="usuario.cif" 
                    label="CIF" 
                    required></v-text-field>
                </v-col>

                <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <file-input  
                                       :files="files" 
                                       v-on:file-change="setFiles" 
                                       file-clear="clearFiles"
                                       id="inputFile">
                                       
                                   </file-input>
                </v-col>
            </v-row>

            <v-row dense>
                <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-text-field filled 
                    :error-messages="errors.errors.email ? errors.errors.email[0] : null" 
                    v-model="usuario.email" 
                    label="Email" 
                    required></v-text-field>
                </v-col>
                 <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-text-field filled 
                    :error-messages="errors.errors.telefono ? errors.errors.telefono[0] : null" 
                    v-model="usuario.telefono" 
                    :rules="[rules.number_rule]" 
                    counter 
                    maxlength="9"
                    label="Teléfono" 
                    required></v-text-field>
                </v-col>

                <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-text-field filled 
                    :error-messages="errors.errors.ciudad ? errors.errors.ciudad[0] : null" 
                    v-model="usuario.ciudad" 
                    label="Ciudad" 
                    :counter="60"
                    required></v-text-field>
                </v-col>

              <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                    <v-select filled 
                        :error-messages="errors.errors.provincia_id ? errors.errors.provincia_id[0] : null"  
                        :items="provincias" 
                        item-value="id" 
                        item-text="nombre" 
                        label="Provincia" 
                        v-model="usuario.provincia_id"> 
                    </v-select>
                </v-col> 
            </v-row>

             <v-row dense>
                
                 <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12">
                    <v-textarea filled 
                        :error-messages="errors.errors.direccion ? errors.errors.direccion[0] : null"  
                        label="Direccion"
                        v-model="usuario.direccion">

                    </v-textarea>
                </v-col>
               

               
             </v-row>
             

            <v-row class="mt-3">
                <v-col cols="12">
                   

                     <v-btn 
                       
                        rounded
                        depressed 
                        @click="updateUsuario" 
                        :disabled="isloading" 
                        color="success" 
                        class="white--text">Actualizar</v-btn>

                </v-col>
            </v-row>

        </v-form> -->

    </v-container>
	</div>
</template>
<script type="text/javascript">
	 import FileInput from '../../../global_components/FileInput.vue'

     export default {
          components: {
               'file-input' : FileInput
          },
        data() {
            return {
               editMode: false,
                usuario: {
                    id: null,
                    provincia_id: '',
                    name: '',
                    nombre_fiscal: '',
                    cif: '',
                    telefono: '',
                    ciudad:'',
                    email: '',
                    password: '',
                    role: null,
                    direccion: '',
                    cuenta:'',
                },
                rules: {
                    number_rule: value => /^\d+$/.test(value) || 'Campo numérico'
                },
                roles:[
                    {
                        id: 1,
                        role: 'Administrador',
                    },
                     {
                        id: 2,
                        role: 'Cliente',
                    }
                ],
                provincias:[],
                files: [],
                imagePreview: [],
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                existeDatosEmpres :true,
            }
        },
        created() {
        	let user_id = localStorage.user_id

            this.getUsuarioById(user_id)
        },
        methods: {
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
             updateUsuario() {

                let formDataUpdate = new FormData()

                for (let fileSave of this.files) {
                    formDataUpdate.append('imagen[]', fileSave, fileSave.name)

                }
                formDataUpdate.append('usuario', JSON.stringify(this.usuario))
                formDataUpdate.append('existeDatosEmpres', true)



                axios.post('api/update-usuario/'+ this.usuario.id, formDataUpdate).then(res => {
                    this.$toast.sucs('Usuario actualizado con éxito')

                    // this.$router.push('/lista-usuario')
                }, res => {              
                    this.$toast.error('Error guardando usuario')
                })
            },
            getUsuarioById(usuario_id) {
                axios.get(`api/get-usuario-by-id/${usuario_id}`).then(res => {
                    this.usuario = res.data.user
                    this.provincias =  res.data.provincias
                }, res => {
                    this.$toast.error('Error consultando Perfil')
                })
            },
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
            errors() {
                return this.$store.getters.geterrors
            },
             uri(){
                return window.location.origin
            },
            idUser(){
                return localStorage.user_id
            }
        }
    };
</script>