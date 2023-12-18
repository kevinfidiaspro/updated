<template>
     <div class="background my-container">
          <v-custom-title text="Administrador de Tareas"></v-custom-title>
          <loader v-if="isloading"></loader>
          <v-btn x-small color="success" @click="openNewDrag">
               <v-icon color="white">mdi-plus</v-icon >Nueva Sección 
          </v-btn>
          <v-btn x-small color="orange" class="white--text mx-2" @click="getTasks()">               
                   <v-icon color="white">mdi-refresh</v-icon> REFRESH</v-btn>
          <v-row>
               <v-col md="4">
                    <new-drag :drag="drag" :dragOn="dragOn" :closeNewDrag="closeNewDrag"
                         :dragglables="dragglables" :captureUriId="captureUriId">
                    </new-drag>
               </v-col>
          </v-row>
          <v-row class="flex-nowrap"  style="overflow-x: auto!important;">
               <v-col cols="12" xs="12" sm="12" md="4" lg="4" xl="4"  v-for="(draggable, i) in dragglables" :key="i"  class="elevation-4">
                    <v-row>
                         <!-- titulo -->
                         <v-col cols="12" md="9">
                              <h4 v-if="draggable.editModeDrag == false">{{ draggable.drag }}</h4>
                              <!-- Edicion de titulo y botones ocultos -->
                              <v-text-field v-if="draggable.editModeDrag == true" v-model="draggable.drag" label="Editar Sección"></v-text-field>                                             
                              <v-btn x-small v-if="draggable.editModeDrag == true" color="success" class="white--text mx-2 mb-4" 
                                   @click="updateDrag(draggable)"><v-icon color="white">mdi-autorenew</v-icon> Actualizar
                              </v-btn>
                              <v-btn x-small v-if="draggable.editModeDrag == true" color="red" class="white--text mx-2 mb-4" 
                                   @click="cancelEditDrag(draggable)"><v-icon color="white">mdi-close</v-icon> Cancelar
                              </v-btn>
                         </v-col>
                         <!-- + añadir -->
                         <v-col cols="12" md="3">                             
                              <v-icon  @click="openNewTaskCard(draggable, i)" color="primary">mdi-plus</v-icon>
                              <v-menu offset-y>
                                   <template v-slot:activator="{ on, attrs }">
                                        <v-icon style="cursor:pointer!important" v-bind="attrs" v-on="on"
                                             v-if="draggable.drag != 'Pendientes' && 
                                                   draggable.drag != 'En Progreso' && 
                                                   draggable.drag != 'Finalizadas'">
                                             mdi-microsoft-xbox-controller-menu
                                        </v-icon>
                                   </template>
                              <v-list>
                                   <v-list-item v-for="(item, index) in opciones" :key="index">
                                   <v-list-item-title @click="opcionesDrag(item, draggable)" style="cursor:pointer">
                                        {{ item }}
                                   </v-list-item-title>
                                 </v-list-item>
                               </v-list>
                             </v-menu>
                         </v-col>
                         <!-- desplegable tarea -->
                         <new-task  class="elevation-4 col-10 offset-1"
                              :task="task"
                              :draggable="draggable" 
                              :i="i"
                              :newTaskCard="newTaskCard"
                              :closeNewTaskCard="closeNewTaskCard"
                              :list="draggable.list"
                              :captureUriId="captureUriId"
                              :administradores="administradores">
                         </new-task>
                    </v-row>

                    <draggable class="dragArea list-group dragDivScroll" :id="draggable.drag" 
                       :list="draggable.list" group="people" :move="checkMove">
                         <div class="list-group-item" v-for="element in draggable.list" :key="element.id">
                              <v-card class="mb-4 card-move styles">
                                   <v-card-title>
                                        <v-row>
                                             <v-col cols="12" md="9"><h5>{{element.titulo}}</h5></v-col>
                                             <v-col cols="12" md="3">
                                                  <v-menu offset-y>
                                                       <template v-slot:activator="{ on, attrs }">
                                                            <v-icon  style="cursor:pointer!important; " v-bind="attrs" v-on="on">mdi-microsoft-xbox-controller-menu</v-icon>
                                                       </template>
                                                       <v-list>
                                                            <v-list-item v-for="(item, index) in opciones" :key="index" class="hoverableOptions">
                                                                 <v-list-item-title @click="opcionClick(item, draggable, element)">{{ item }}</v-list-item-title>
                                                            </v-list-item>
                                                       </v-list>
                                                  </v-menu>
                                             </v-col>
                                        </v-row>
                                   </v-card-title>
                                   <v-card-text>
                                        <small v-html="element.descripcion.substring(0, 100)"></small>
                                        <small>Asignar colaboradores</small>
                                        <span v-for="tas,o in tareasAminis" :key="o">
                                             <span v-if="tas.tarea_id == element.id">
                                                  <span v-for="admin,l in administradores " :key="l">
                                                       <span v-if="admin.id == tas.user_id">
                                                            <v-tooltip left>
                                                                 <template v-slot:activator="{ on, attrs }">
                                                                 <v-btn  icon rounded :style="'background:' + colors[tas.user_id]"  v-bind="attrs" v-on="on">
                                                                      {{admin.name.charAt(0)}}
                                                                 </v-btn>
                                                                 </template>
                                                                 <span>
                                                                      <span v-if="storageId == admin.id">Yo</span>
                                                                      <span v-else>{{ admin.name}} , {{admin.email}}</span>
                                                                 </span>
                                                            </v-tooltip>                                                  
                                                       </span>
                                                  </span>                                             
                                             </span>
                                        </span>
                                   </v-card-text>  
                              </v-card>
                         </div>
                    </draggable>
               </v-col>
          </v-row>
          <dialog-update-task 
               :dialogEditTask="dialogEditTask" 
               :closeDialogUpdateTask="closeDialogUpdateTask"
               :captureTask="captureTask"
               :captureUriId="captureUriId"
               :administradores="administradores"
               :tareasAminis="tareasAminis">
          </dialog-update-task>
          <dialog-delete-file     
               :dialogDeleteTask="dialogDeleteTask"
               :closedialogDeleteTask="closedialogDeleteTask"
               :captureTask="captureTask">
          </dialog-delete-file>
     </div>
</template>

<script>
     import draggable from 'vuedraggable'
     import NewDrag from './NewDrag'
     import NewTaskCard from './NewTaskCard'
     import DialogUpdateTask from './DialogUpdateTask'
     import dialogDeleteTask from './dialogDeleteTask.vue'

     let idGlobal = 9;

     export default {
          components: {
          draggable,
          'new-drag' : NewDrag,
          'new-task' : NewTaskCard,
          'dialog-update-task' : DialogUpdateTask,
          'dialog-delete-file': dialogDeleteTask,
          },

          data () {
               return {
                    colors :['#EF9A9A', '#CE93D8', '#FF4081', '#90CAF9', '#009688'],
                    allTasks: [],
                    tareasAminis : [],
                    administradores : [],
                    newTaskCard : false,
                    tab: null,
                    opciones: ['Editar','Eliminar'],
                    task : {titulo: '',descripcion: ''},
                    pendienteList: [],
                    progresoList: [],
                    finalizadasList: [],
                    dialogUpdateTask: false,
                    captureTask: {},                    
                    drag:{ drag: '' },
                    dragOn : false,
                    dragglables : [],
                    dialogEditTask:false,                    
                    editModeDrag: false,
                    dialogDeleteTask: false,      
               }
          },
          computed:{
               orderListPendinete(){                    
                    return this.pendienteList.sort()
               },

               captureUriId(){
                    let hashUri =  window.location.hash
                    let splithash = hashUri.split('/')
                    let resUri =  splithash[2]
                    return resUri*1
               },

               isloading: function() {
                    return this.$store.getters.getloading
               },

               storageId(){
                    return localStorage.user_id
               }               
          },

          mounted(){
               this.getTasks()
          },

          methods:{
               cancelEditDrag(drag){
                    drag.editModeDrag = false
               },

               updateDrag(drag){
                    axios.put(`api/update-drag/`+ drag.id, drag).then(res => {
                         if(res.status*1 == 200){
                             drag.editModeDrag = false
                         }
                    }, err => {
                         this.$toast.error('Error eliminar')
                    }) 
               },

               opcionesDrag(opcion, drag){
                    if (opcion == 'Editar') {
                         drag.editModeDrag = true                        
                    }
                    if (opcion == 'Eliminar') {                       
                        for (var i = 0; i < this.dragglables.length; i++) {
                              if (this.dragglables[i].id == drag.id) {                                 
                                   const indice =   this.dragglables.indexOf(drag)
                                   let confirmF = confirm('¿Estás seguro de eliminar esta sección?')
                                   if (confirmF == true) {
                                        axios.delete(`api/delete-drag/`+ drag.id).then(res => {
                                             if(res.status*1 == 200){
                                                  this.dragglables.splice(indice, 1)
                                                  this.$toast.sucs('Sección Eliminada')
                                             }
                                        }, err => {
                                             this.$toast.error('Error al eliminar')
                                        }) 
                                   }
                                   break
                              }
                         }                        
                    }                    
               },

               openNewDrag(){
                    this.dragOn = true
               },

               closeNewDrag(){
                   this.dragOn = false 
                   this.drag.drag = ''
               },

               checkMove: function(card){                    
                    let objectMove = {
                         desde: card.from.id,
                         hasta: card.to.id,
                         task: card.draggedContext.element,
                    }
                    let administradores = []
                    for (var i = 0; i < this.allTasks.length; i++) {
                         if(this.allTasks[i].id == card.draggedContext.element.id){
                              for (var n = 0; n < this.tareasAminis.length; n++) {
                                   if (this.tareasAminis[n].tarea_id == card.draggedContext.element.id) {
                                        administradores[administradores.length] = this.tareasAminis[n]
                                   }
                              }
                         }
                    }
                    objectMove.task['listAdmins'] = administradores                    
                    axios.put(`api/update-tasks-status/`+ objectMove.task.id, objectMove).then(res => {
                         if(res.status*1 == 200){ }
                    }, err => {
                         this.$toast.error('Error al guardar tareas')
                    })          
               },

               openNewTaskCard(dragglable, i){
                    for (var i = 0; i < this.dragglables.length; i++) {
                      this.dragglables[i].newTask = false
                    }
                    this.task =  {titulo: '',descripcion: ''}                    
                    dragglable.newTask = true             
               },

               closeNewTaskCard(dragglable, i ){
                    dragglable.newTask = false
                    this.task =  {name: '',descripcion: ''}
                    this.getTasks()
               },

               closeDialogUpdateTask(){
                    this.dialogEditTask = false
                    this.getTasks()
               },

               opcionClick(opcion, dragg, task){
                    if (opcion == 'Editar') {
                         this.dialogEditTask = true;
                         this.captureTask = {'task' : task,'dragg' : dragg};
                    }
                    if (opcion == 'Eliminar') {      
                         this.dialogDeleteTask = true;
                         this.captureTask = {'task' : task,'dragg' : dragg};
                    }
               },
            
               closedialogDeleteTask(){
                    this.dialogDeleteTask = false;
                    this.getTasks();          
               },

               modalDelete(){ 
                    this.dialogDeleteTask = true
               },

               getTasks(){
                    axios.get(`api/get-tasks/` + this.captureUriId).then(res => {
                         this.allTasks = res.data.tasks
                         this.administradores = res.data.users
                         this.dragglables = res.data.dragglables
                         let taskForDrags = res.data.dragsTasks
                         for (var i = 0; i < this.dragglables.length; i++) {
                              for (var n = 0; n < taskForDrags.length; n++) {
                                   if (this.dragglables[i].id == taskForDrags[n].drag_id) {
                                       this.dragglables[i].list.push(taskForDrags[n].task) 
                                   }
                              }
                         }                        
                         this.tareasAminis = res.data.tareasAdmin
                    }, err => {
                    this.$toast.error('Error consultando tareas')
                    })
               },
              
               log(evt) { },
               onAdd(event){ },
               recibiendopendiente(e){ },
               cloneDog({ id, name }) {             
                return {id: id,name: name};
               }, 
          }
     };
</script>

<style type="text/css">
     .styles:hover{        
          transition: ease .5sg all;
          background: #E3F2FD!important;
     }

     .card-move{
          cursor: move;
     }

     .dragDivScroll{
          overflow-y: auto!important; height:70vh;
     }
     
     .hoverableOptions{
        cursor: pointer;
     }

     .hoverableOptions:hover{
        background: #E3F2FD;
        transition: ease all .3s
     }
</style>