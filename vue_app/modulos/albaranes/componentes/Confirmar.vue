<template>
	<div>
	<v-row justify="center">
	<v-dialog v-model="modalConfirm" max-width="500">
           <v-card >

           	 <v-card-title class="text-h6 aviso">
	          Aviso
	        </v-card-title>

	        <v-card-text>
           	
                <h2>¿Estás seguro que deseas pasar a {{itemPdf}}?</h2>
                
           </v-card-text>
           <v-card-actions class="pt-3">
                     <v-spacer></v-spacer>
                     <v-btn 
                     	color="error" 
                     	large 
                     	@click="closeModalConfirmFactura">Cancelar</v-btn>
                     <v-btn 
                     	color="success" 
                     	large 
                     	@click="convertirFactura"
                     	v-if="itemPdf == 'Factura'">Confirmar</v-btn>

                     <v-btn 
                     	color="success" 
                     	large 
                     	@click="convertirNota"
                     	v-if="itemPdf == 'Nota'">Confirmar</v-btn>
                     <v-spacer></v-spacer>
                </v-card-actions>
           </v-card>
      </v-dialog>
 </v-row>
</div>
</template>

<script type="text/javascript">
	
	export default{
		props: {
			itemPdf: String,
			modalConfirm: Boolean,
			closeModalConfirmFactura: Function,
			convertirFactura: Function,
			convertirNota: Function
			
		}
	};
</script>
<style type="text/css">
	.stylesDelete{
		height: 10vw;
    	text-align: center;
	}
	.aviso{
		background: #a27af3;
		padding: 5px;
		color:white!important;
	}
	
</style>