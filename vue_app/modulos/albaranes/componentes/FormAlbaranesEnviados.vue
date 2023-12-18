<template>
	<div>
		<div class="background my-container col-10 offset-1 mt-2">
            <v-container>
            	<v-custom-title text="Crear Albaran"></v-custom-title>
				<loader v-if="isloading"></loader>
            	<v-row>
            		<v-col cols="12" sm="12" md="6" lg="6" xl="6">
            			<v-select label="Cliente" v-model="form.cliente" :items="clientes" item-text="nombre" return-object></v-select>
            		</v-col>
            		<v-col cols="12" sm="12" md="6" lg="6" xl="6">
            	        <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="form.fecha" transition="scale-transition" offset-y min-width="290px"> 
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" v-model="form.fecha"
                                    label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                                </v-text-field>
                            </template>
                            <v-date-picker v-model="form.fecha" no-title scrollable>
                                <v-spacer></v-spacer>
                                <v-btn text color="red" @click="menu = false"><strong>Cancel</strong></v-btn>
                                <v-btn text color="success" @click="$refs.menu.save(form.fecha)"><strong>OK</strong></v-btn>
                            </v-date-picker>
                        </v-menu>
            		</v-col>
            	</v-row>
            	<v-row>
            		<v-col cols="12" sm="12" md="4" lg="4" xl="4">
            			<v-text-field label="Descripción" v-model="form.descripcion"></v-text-field>            		
            		</v-col>
            		<v-col cols="12" sm="12" md="2" lg="2" xl="2">
            			<v-text-field label="Cantidad" @change="validateCantidad" v-model="form.cantidad"></v-text-field>            		
            		</v-col>
            		<v-col cols="12" sm="12" md="3" lg="3" xl="3">
            			<v-text-field label="Precio"  @change="validatePrecio" v-model="form.precio"></v-text-field>            		
            		</v-col>
            		<v-col cols="12" sm="12" md="3" lg="3" xl="3">
            			<v-text-field label="Importe" v-model="form.importe" readonly></v-text-field>            		
            		</v-col>
            		<v-btn rounded color="blue" class="white--text" @click="addAenviado()">Agregar</v-btn>
            	</v-row>
            	<v-row v-if="arrayAgregados.length >0">
					<v-col cols="12" md="12">
						<v-data-table :headers="headers" :items="arrayAgregados" disable-pagination 
                            hide-default-footer item-key="id" class="elevation-1">
							<template v-slot:item.action="{ item }">
								<v-icon @click="setItem(item)" small class="mr-2" color="blue">mdi-pencil</v-icon>
								<v-icon @click="deleteEnvi(item)" small class="mr-2" color="red">mdi-trash-can</v-icon>
							</template>
						</v-data-table>
					</v-col>
            	</v-row>
                <v-row v-if="seleccionConvertirFactura">
                        <v-col cols="12" sm="12" md="6" lg="6" xl="6">
                            <v-checkbox v-model="ivaIncluir" @change="changeIva" label="Incluir Iva"></v-checkbox>
                            <v-checkbox v-model="descuentoIncluir" @change="changeCheckboxDescuento" label="Incluir Descuento"></v-checkbox>
                            <v-row>
                                <v-col cols="12" sm="12" md="4" lg="4">
                                    <v-text-field v-if="descuentoIncluir" @change="changeDescuento" label="Descuento" v-model="descuento"></v-text-field> 
                                </v-col>                        
                            </v-row>
                        </v-col>
                        <v-col cols="12" sm="12" md="6" lg="6" xl="6" >
                            <p style="text-align:right;" ><strong>Subtotal: {{ subtotal }}€</strong></p>
                            <p style="text-align:right;" v-if="ivaIncluir"><strong>Iva 21%: {{ montoIva }}€</strong></p>
                            <p style="text-align:right;" v-if="descuentoIncluir && ivaIncluir"><strong>Descuento: {{ descuento }}€</strong></p>
                        </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="12">
                        <p style="text-align:right;"><strong>Total : {{parseFloat(total).toFixed(2)}}€</strong></p>
                    </v-col>
                </v-row>
            	<v-row>
            		<v-progress-linear v-if="!visualizador" indeterminate color="yellow darken-2"></v-progress-linear>
            	</v-row>
                    <v-btn rounded depressed outlined color="blue" class="white--text mb-4" v-if="urlFactura != false"
                        :to="'/lista-albaranes-enviados/' +userId ">Volver
                    </v-btn>
            	<v-row v-if="arrayAgregados.length >0 && visualizador != false">
                    <v-col cols="12" sm="12" md="12" lg="12">
                        <v-btn rounded depressed color="success" class="white--text" v-if="convertidoANotaOFactura == false"
                            @click="guardar()">Guardar y generar pdf
                        </v-btn>
                        <v-btn rounded depressed color="orange" class="white--text mx-1" v-if="urlFactura !=false" target="_blank"
                            :href="'/storage/albaranes/enviados/' +urlFactura ">Ver Albaran PDF
                        </v-btn>
                    </v-col>
            		<v-col cols="12" sm="12" md="12" lg="12">
                        <v-btn rounded depressed color="blue" class="white--text mr-1" v-if="urlFactura != false && generar_factura == false && convertidoANotaOFactura == false"
                            @click="choiseConvertirFactura">Generar Factura
                        </v-btn>            
                        <v-btn rounded depressed color="blue" class="white--text mx-1" v-if="urlFactura != false && generar_factura == true && convertidoANotaOFactura == false"
                            @click="facturaConfirm">Convertir a Factura
                        </v-btn>
                        <v-btn rounded depressed color="orange" class="white--text mx-1" v-if="factura_generada != false" target="_blank"
                            :href="'/storage/recibos/' + factura_generada "> Ver Factura
                        </v-btn>
                        <v-btn rounded depressed color="blue" class="white--text mx-1" v-if="urlFactura != false && convertidoANotaOFactura == false" 
                            @click="notaConfirm">Convertir a Nota
                        </v-btn>
                        <v-btn rounded depressed color="orange" class="white--text mx-1" v-if="nota_generada != false" target="_blank"
                            :href="'/storage/recibos/' + nota_generada "> Ver Nota
                        </v-btn>
                    </v-col>                    
            	</v-row>
                <modal-confirm :itemPdf="itemPdf" :modalConfirm="modalConfirm" :closeModalConfirmFactura="closeModalConfirmFactura"
                    :convertirFactura="convertirFactura" :convertirNota="convertirNota">
                </modal-confirm>
            </v-container>
        </div>
	</div> 
</template>
<script type="text/javascript">
    import Confirmar from "./Confirmar"
	export default {
        components :{
            'modal-confirm' : Confirmar
        },
		data() {
            return {
                subtotal: 0,
                generar_factura: false,
                descuento: 0,
                seleccionConvertirFactura: false,
                ivaIncluir : true,
                descuentoIncluir : false,
                montoIva :  0,
				headers: 
                [
                    { text: 'Descripción',align: 'left',value: 'descripcion' },
					{ text: 'Cantidad', value: 'cantidad' },
					{ text: 'Precio', value: 'precio' },
					{ text: 'Importe', value: 'importe' },
					{ text: 'Acciones', value: 'action', sortable: false },
				],
            	menu: false,
               	form : {
                    id: `temp-${new Date().getTime()}`,
               		cliente : '',
               		fecha : new Date().toISOString().substr(0, 10),
               		descripcion: '',
               		cantidad :'',
               		precio : '',
               		importe:''
               },
               total: 0,
               arrayAgregados:[],
               clientes: [],
               visualizador : true,
               urlFactura : false,               
               fecha_emision: new Date().toISOString().substr(0, 10),
               servicios : {
                    cantidad: 1,
                    descripcion: "dwqdwq",
                    id: "temp-1626799959740",
                    importe: 0,
                    precio: 0,
                    recibo_id: null,
                    user_id: "9",
               },
                recibo :  {
                    cliente_id: '',
                    cliente: {
                        id:'',
                        email: null
                    },
                    fecha: new Date().toISOString().substr(0, 10),
                    sub_total: 0,
                    iva: 0,
                    porcentaje_descuento: 0,
                    total_descuento: 0,
                    total: 0,
                    factura_url: null,
                    nota_url: null,
                    has_iva: true,
                    servicios: [],
                    user_id: localStorage.getItem('user_id')
                },
                factura_generada : false,
                nota_generada : false,
                errorCantidad : '',
                errorPrecio : '',
                albaranId : '',
                modalConfirm : false,
                itemPdf : '',
                convertidoANotaOFactura : false            
            }
        },
        watch: {
        'form.cantidad'(n) {
            console.log("Entra en cantidad")
            this.form.importe = parseFloat(n * this.form.precio).toFixed(2)
        },
        'form.precio'(n) {
            console.log("Entra en precio")
            this.form.importe = parseFloat(n * this.form.cantidad).toFixed(2)
        },
        'recibo.servicios'(n) {
            if (n.length > 0) {
                this.calcularTotales(n)
            }
        }
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
            errors() {
                return this.$store.getters.geterrors
            },
            userId(){
            	return localStorage.user_id
            }
        },
        created(){
        	this.getClientes()
        },
        methods:{
            closeModalConfirmFactura(){
                this.modalConfirm = false
                 this.itemPdf = ''
            },
            facturaConfirm(){
                this.modalConfirm = true
                this.itemPdf = 'Factura'

            },
             notaConfirm(){
                this.modalConfirm = true
                this.itemPdf = 'Nota'
            },
            changeCheckboxDescuento(value){
                if(value == false)
                {
                    this.calculoTotal()
                    if(this.montoIva != 0){
                        this.changeIva(true)
                    }
                }
                else
                {                  
                    if(this.montoIva != 0){
                        this.changeIva(true)
                    }
                    this.total = (this.total - this.descuento)
                }
            },           
            changeDescuento(value){               
                var RE = /^\d*\.?\d*$/;
                if (RE.test(value))
                {
                this.descuento=0
                this.errorPrecio = ''
                this.descuento=1*value
                }
                else
                {
                this.errorPrecio = 'Inserte un numero entero o decimal'
                this.descuento= 0
                return
                }
                this.calculoTotal()
                if(this.descuentoIncluir == true)
                {
                    if(this.montoIva != 0)
                    {
                        this.changeIva(true)
                    }
                    this.total = parseFloat(1*this.total -  value).toFixed(2)
                }
                else
                {
                    this.changeIva(true)
                }
            },
            changeIva(value){
                this.calculoTotal()
                let vm = this.total
                if(value == true)
                {
                    this.montoIva = vm * 21/100
                    this.total = 1*this.total + 1*this.montoIva
                    console.log(this.total)
                }
                else
                {
                    this.calculoTotal()
                    this.montoIva = 0
                }
            },
            choiseConvertirFactura(){
                this.seleccionConvertirFactura = true
                this.generar_factura = true
                this.changeIva(true)
            },
            convertirFactura(){
                this.modalConfirm = false
                this.visualizador = false
                let tipo = 'factura'
                let convertir_factura = true
                let idClient = this.form.cliente.id
                let calculoDescuento = 0
                let enviarIva = false
                if(this.descuentoIncluir == true)
                {
                    calculoDescuento = this.descuento
                }
                if(this.ivaIncluir == true)
                {
                    enviarIva = true
                }
                let commit = {
                    albaran_id : this.albaranId,
                    cliente_id : idClient,
                    fecha: new Date().toISOString().substr(0, 10),
                    sub_total: this.subtotal,
                    iva: 1*this.montoIva,
                    porcentaje_descuento: 0,
                    total_descuento: 1*calculoDescuento,
                    total: 1*this.total,
                    factura_url: null,
                    nota_url: null,
                    has_iva: enviarIva,
                    servicios: this.arrayAgregados,
                    user_id: localStorage.getItem('user_id')
                }
                axios.post(`api/save-factura-albaran`, commit).then(res => {
                    this.visualizador = true
                    this.convertidoANotaOFactura = true
                    this.factura_generada = res.data.data.recibo.factura_url
                }, res => {
                    this.$toast.error('Error guardando recibo')
                })
            },
            convertirNota(){
                this.modalConfirm = false
                this.visualizador = false
                let tipo = 'nota'
                let convertir_factura = true
                let idClient = this.form.cliente.id
                let calculoDescuento = 0
                let enviarIva = false
                if(this.descuentoIncluir == true){
                    calculoDescuento = this.descuento
                }
                if(this.ivaIncluir == true){
                    enviarIva = true
                }
                let commit = {
                    albaran_id : this.albaranId,
                    cliente_id : idClient,
                    fecha: new Date().toISOString().substr(0, 10),
                    sub_total: this.subtotal,
                    iva: 1*this.montoIva,
                    porcentaje_descuento: 0,
                    total_descuento: 1*calculoDescuento,
                    total: 1*this.total,
                    factura_url: null,
                    nota_url: null,
                    has_iva: enviarIva,
                    servicios: this.arrayAgregados,
                    user_id: localStorage.getItem('user_id')
                }
                axios.post(`api/save-nota-albaran`, commit).then(res => {
                    this.visualizador = true
                    this.convertidoANotaOFactura = true
                    this.nota_generada= res.data.data.recibo.nota_url
                }, res => {
                    this.$toast.error('Error guardando recibo')
                })
            },
        	guardar(){
        		this.visualizador = false
        		let formData = new FormData()
        		formData.append('enviados', JSON.stringify(this.arrayAgregados))
                formData.append('fecha_emision',this.fecha_emision)
        		axios.post('api/save-albaran-enviado', formData).then(res => {
        			this.urlFactura = res.data.albaran.url
                    this.generar_factura = false
                    this.$toast.sucs('Albaran guardado con exito')
                    // this.$router.push('/lista-albaranes-enviados/' + this.userId)
                    this.visualizador = true
                    this.albaranId = res.data.albaran.id
                }, res => {
                    this.$toast.error('Error guardando cliente')
                    this.visualizador = true
                })
        	},
        	deleteEnvi(item){
        		const indice = this.arrayAgregados. indexOf(item);
                this.arrayAgregados.splice(indice, 1)
                this.calculoTotal()
        	},
            resetAlbaranForm() {
                this.form = 
                {
                    id: `temp-${new Date().getTime()}`,
                    cliente : this.form.cliente,
                    fecha : this.form.fecha,
                    descripcion: '',
                    cantidad: '',
                    precio: '',
                    importe: ''
                }
            },
        	addAenviado(){
                if (this.form.descripcion !='' && this.form.cantidad !='' && this.form.precio !='') {
                    let index = this.arrayAgregados.findIndex(element => element.id == this.form.id);
                    if (index > -1) {
                        this.$set(this.arrayAgregados, index, this.form);
                        this.calculoTotal();
                        this.resetAlbaranForm();
                        return null;
                    }
                    this.arrayAgregados.push(this.form);
                    this.calculoTotal();
                    this.resetAlbaranForm();
                }
                else
                {
                    this.$toast.error('Introduzca: DESCRIPCION, CANTIDAD y PRECIO CORRECTOS')
                }
        	},
            calculoTotal(){
                this.total = 0
                this.subtotal = 0
                for (var i = 0; i < this.arrayAgregados.length; i++) {
                    this.total = parseFloat(1*this.total + 1*this.arrayAgregados[i].importe).toFixed(2)
                     this.subtotal = parseFloat(1*this.subtotal + 1*this.arrayAgregados[i].importe).toFixed(2)
                }
            },
        	getClientes() {
                axios.get(`api/get-clientes/${localStorage.getItem('user_id')}`).then(res => {
                    this.clientes = res.data
                }, res => {
                    this.$toast.error('Error consultando Clientes')
                })
            },
            validateCantidad(valor) {
                this.form.cantidad=''
                
                var RE = /^\d*\.?\d*$/;
                if (RE.test(valor)) {
                    this.errorCantidad = ''
                    this.form.cantidad=valor
                } else {
                    this.errorCantidad = 'Inserte un numero entero '
                    this.form.cantidad= 0
                }               
            },
            validatePrecio(valor){
                this.form.precio=''
                var RE = /^\d*\.?\d*$/;
                if (RE.test(valor)) {
                    this.errorPrecio = ''
                    this.form.precio=valor
                } else {
                    this.errorPrecio = 'Inserte un numero entero o decimal'
                    this.form.precio= 0
                }
            },
            validateDescuento(valor){
                this.descuento=0
                var RE = /^\d*\.?\d*$/;
                if (RE.test(valor)) {
                    this.errorPrecio = ''
                    this.descuento=1*valor
                } else {
                    this.errorPrecio = 'Inserte un numero entero o decimal'
                    this.descuento= 0
                }
            },
            setItem(item) {
                this.form = item
                // this.deleteEnvi(item)
            },
        }
	};
</script>