<template>
    <div>
        <gestion-admin v-if="returnLocalRole == 1"></gestion-admin>
        <gestion-documentos v-if="returnLocalRole == 2"></gestion-documentos>
    </div>
</template>
<script>
    import GestionAdmin from './GestionAdmin.vue'
    import GestorDocumentos from './GestorDocumentos.vue'
     export default {
        components:{
            'gestion-admin': GestionAdmin,
            'gestion-documentos': GestorDocumentos
        },
        props: {
          
        },
        data() {
            return {
              
            }
        },
        computed:{
            returnLocalRole(){
                return localStorage.role
            }
        },
        created() {
           
           
        },
        methods:{
            
        }
     };
    
</script>