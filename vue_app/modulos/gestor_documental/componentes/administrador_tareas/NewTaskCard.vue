<template>
	<v-card class="mb-4 pa-1" v-if="draggable.newTask">
          <v-card-title class="pa-1">
                <!-- Titulo -->
              <v-col sm="12" md="12" lg="12" xl="12" class="pa-2">
                <v-text-field class="mt-1" v-model="task.titulo" label="Escriba el nombre de la tarea" :counter="30"></v-text-field> 
              </v-col>
              <!-- Descripcion -->
              <v-col sm="12" md="12" lg="12" xl="12" class="pa-2">
                <small style="font-size:9">Descripción</small>
                <!-- Editor -->
                <ckeditor style="cursor:none" :editor="editor" v-model="task.descripcion" :config="editorConfig"></ckeditor>
              </v-col>
              <!-- Asignacion a -->
              <v-col sm="12" md="12" lg="12" xl="12" class="pa-2">
                <v-select class="mt-1" v-model="selectAdmin" item-text="name" :items="administradores"
                  label="Asignar tarea a" return-object multiple>
                  <template v-slot:prepend-item>
                    <v-list-item ripple @click="toggle">
                      <v-list-item-action>
                        <v-icon :color="selectAdmin.length > 0 ? 'indigo darken-4' : ''">
                          {{ icon }}
                        </v-icon>
                      </v-list-item-action>
                      <v-list-item-content>
                        <v-list-item-title> Seleccionar a todos </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                    <v-divider class="mt-2"></v-divider>
                  </template>
                </v-select>
              </v-col>
          </v-card-title>
           <v-card-actions>
               <v-btn x-small color="success" @click="saveTask">
                  <v-icon color="white">mdi-plus</v-icon>Añadir
               </v-btn>
               <v-btn x-small color="red" class="white--text mx-2" @click="closeNewTaskCard(draggable, i)">
                   <v-icon color="white">mdi-close</v-icon> Cancelar
               </v-btn>
           </v-card-actions>
     </v-card>
</template>
<script>
    

    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
	   export default {
        props: {
        	
          task: Object,
          newTaskCard: Boolean,
          closeNewTaskCard: Function,
          list: Array,
          i: Number,
          draggable: Object,
          captureUriId: Number,
          administradores: Array

        },
      	data () {
           	return {
                    selectAdmin: [],
                    show: false,
                    editor: ClassicEditor,
                    editorData: '<p>Escriba la descripcion para esta tarea.</p>',
                    editorConfig: {
                    toolbar: {
                          items: [
                              'heading',
                              'bold',
                              'italic',
                              'bulletedList',
                              'numberedList',
                              'link',
                              'inserttable',
                              // 'imageInsert',
                          ],
                    },
                      // extraPlugins: null,
              },
           	}
      	},
        computed: {
          likesAllFruit () {
            return this.selectAdmin.length === this.administradores.length
          },
          likesSomeFruit () {
            return this.selectAdmin.length > 0 && !this.likesAllFruit
          },
          icon () {
            if (this.likesAllFruit) return 'mdi-close-box'
            if (this.likesSomeFruit) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
          },
        },
        mounted(){
          for (var i = 0; i < this.administradores.length; i++) {
              if ( this.administradores[i].id == localStorage.user_id) {
                  this.administradores[i].name = "Yo"
              }
          }
        },
      	methods:{
              toggle () {
                this.$nextTick(() => {
                  if (this.likesAllFruit) {
                    this.selectAdmin = []
                  } else {
                    this.selectAdmin = this.administradores.slice()
                  }
                })
              },
               saveTask(){
                    
                    if (this.task.titulo == "" || this.task.titulo == null) {
                        alert("El campo título no puede estar vacío")
                        return
                    }
                    if (this.task.titulo.length <= 10 ) {
                        alert("Consejo: Para crear una tarea descriptiva, Asegurese de que el título sea mayor a 10 caracteres ")
                        return
                    }
                    if (this.task.descripcion == "" || this.task.descripcion == null) {
                        alert("El campo descripcion no puede estar vacío")
                        return
                    }

                    let objNewTask =  new FormData()
                    objNewTask.append('task', JSON.stringify(this.task))
                    objNewTask.append('user_id', this.captureUriId)
                    objNewTask.append('draggable', JSON.stringify(this.draggable))
                    objNewTask.append('administradores', JSON.stringify(this.selectAdmin))

                    axios.post(`api/save-tasks`, objNewTask).then(res => {
                         if(res.status*1 == 200){
                              this.selectAdmin = []
                              this.$toast.sucs('Tarea Creada')
                              this.list.unshift(res.data.task)
                              
                              this.closeNewTaskCard(this.draggable, this.i)
                              

                         }    
                    }, err => {
                         this.$toast.error('Error al guardar tareas')
                    })
               },
               
      	}
    };
</script>