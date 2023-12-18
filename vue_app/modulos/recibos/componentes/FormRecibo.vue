<template>
<div class="background my-container col-9 offset-2 mt-2">
    <v-container>
        <v-custom-title text="Guardar / Editar"></v-custom-title>
        <loader v-if="isloading"></loader>
        <v-form class="mt-5 mb-4">
            <v-row dense>
                <v-col cols="12" md="12">
                    <p><strong>DATOS DEL CLIENTE Y FECHA</strong></p>
                </v-col>
                <v-col cols="12" md="3">
                    <v-autocomplete filled v-model="recibo.cliente_id" :error-messages="errors.errors.cliente_id ? errors.errors.cliente_id[0] : null"
                        :items="clientes" item-text="nombres" item-value="id" label="Cliente">
                    </v-autocomplete>
                </v-col>
                <v-col cols="12" md="2">
                    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" offset-y min-width="290px"
                        :return-value.sync="recibo.fecha" transition="scale-transition">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" 
                                v-model="recibo.fecha" label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                            </v-text-field>
                        </template>
                        <v-date-picker v-model="recibo.fecha" no-title scrollable>
                            <v-spacer></v-spacer>
                            <v-btn text color="red" @click="menu = false"><strong>Cancel</strong></v-btn>
                            <v-btn text color="success" @click="$refs.menu.save(recibo.fecha)"><strong>OK</strong></v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="12" md="12">
                    <p><strong>DATOS DEL SERVICIO A FACTURAR</strong></p>
                </v-col>
                <v-col cols="12" md="4">
                    <v-text-field hide-details filled v-model="servicio.descripcion" label="Descripción" required></v-text-field>
                </v-col>
                <v-col cols="12" md="2">
                    <v-text-field hide-details filled type="number" step="0.01" v-model="servicio.cantidad" label="Cantidad" required></v-text-field>
                </v-col>
                <v-col cols="12" md="2">
                    <v-text-field hide-details filled type="number" step="0.01" v-model="servicio.precio" label="Precio" required></v-text-field>
                </v-col>
                <v-col cols="12" md="2">
                    <v-text-field hide-details filled readonly type="number" step="0.01" v-model="servicio.importe" label="Importe" required></v-text-field>
                </v-col>
                <v-col class="mt-1" cols="12">
                    <v-btn rounded  @click="addService" :disabled="isloading" color="blue" class="white--text mb-3">agregar servicio</v-btn>
                    <v-btn v-if="this.validadorUrl=='#/guardar-recibo?tipo=factura' || this.validadorUrl==`#/guardar-recibo?id=${recibo.id}&tipo=factura`"
                        rounded outlined color="purple" @click="modalAddAlbaranesFuncion()" :disabled="isloading" class="white--text mb-3">
                        Añadir albaranes
                    </v-btn>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="12" md="12">
                    <v-data-table :headers="headers" :items="recibo.servicios" disable-pagination hide-default-footer item-key="id" class="elevation-1">
                        <template v-slot:item.action="{ item }">
                            <v-icon v-if="item.descripcion.substr(0, 7) != 'Albarán'" @click="setItem(item)" small class="mr-2" color="blue">mdi-pencil</v-icon>
                            <!-- <v-icon @click="setItem(item)" small class="mr-2" color="blue">mdi-pencil</v-icon> -->
                            <v-icon @click="deleteItem(item,checkbox)" small class="mr-2" color="red">mdi-trash-can</v-icon>
                            
                        <pre style="font-size:10px !important;">{{item}}</pre>
                        </template>
                    </v-data-table>
                </v-col>
                <!-- <v-row><pre style="font-size:10px !important;">{{recibo.servicios}}</pre></v-row> -->
            </v-row>
            <v-row dense class="mt-3">
                <v-col cols="12" md="12">
                    <v-row>
                        <v-col cols="12" md="12">
                            <p><strong>DESCUENTOS Y TOTALES</strong></p>
                        </v-col>
                        <!-- caja  de descuento -->
                        <v-col cols="12" md="2" v-if="tipo != 'parte-trabajo'">
                            <v-text-field filled type="number" step="0.01" min="0" max="100" v-model="recibo.porcentaje_descuento" label="Descuento" hide-details></v-text-field>
                        </v-col>
                        <!-- select de presupuestos -->
                        <v-col cols="12" md="6" v-if="tipo == 'parte-trabajo'">
                            <v-select label="Presupuesto" v-model="presupuesto" return-object :items="presupuestos" item-text="nro_presupuesto" item-value="id"></v-select>
                            <p><strong>Importe:</strong> {{this.presupuesto.total}}€ <strong class="pl-2">Beneficio:</strong> {{beneficio}} </p>
                        </v-col>
                        <v-spacer></v-spacer>
                        <!-- componentes de totales -->
                        <display-totales v-if="tipo == 'presupuesto' || tipo == 'factura' || tipo == 'parte-trabajo'" :recibo="totales" :tipo="tipo" :has_iva="recibo.has_iva"></display-totales>
                        <display-total v-if="tipo == 'nota'" :total="recibo.total"></display-total>
                    </v-row>
                </v-col>
            </v-row>
            <v-row v-if="errors.errors.servicios" class="mt-3">
                <v-col cols="12" md="4">
                    <v-alert outlined color="warning">{{errors.errors.servicios[0]}}</v-alert>
                </v-col>
            </v-row>
            <botones-factura v-if="tipo == 'factura'" :isloading="isloading" :tipo="tipo" :recibo="recibo" v-on:save_factura="saveFactura"></botones-factura>
            <botones-presupuesto v-if="tipo == 'presupuesto'" :isloading="isloading" :tipo="tipo" :recibo="recibo"
                v-on:save_presupuesto="savePresupuesto" v-on:convertir_factura="convertirFactura" v-on:convertir_nota="convertirNota">
            </botones-presupuesto>
            <v-row>
                <v-btn rounded outlined @click="volver" :disabled="isloading" color="blue" class="white--text ml-3  ">Volver</v-btn>
            </v-row>
            <v-row v-if="tipo == 'factura' || tipo == 'parte-trabajo'" class="mt-3">
                <v-col cols="12">
                    <v-checkbox v-model="recibo.has_iva" label="Incluir IVA" />
                </v-col>
            </v-row>            
            <v-row v-if="tipo == 'nota'" class="mt-3 mb-4">
                <v-col cols="12">                    
                    <v-btn :disabled="isloading" rounded depressed color="success" class="white--text mx-1" 
                        @click="saveRecibo('nota')">guardar nota
                    </v-btn>
                    <v-btn :disabled="isloading" rounded depressed color="blue" class="white--text mx-1"
                        v-if="recibo.id && recibo.factura_url == null" @click="convertirNotaFactura">convertir a factura
                    </v-btn>
                    <v-btn :disabled="isloading" rounded depressed color="orange" class="white--text mx-1"
                        v-if="recibo.nota_url != null && tipo == 'nota'" target="_blank" :href="`storage/recibos/${recibo.nota_url}`">ver recibo pdf
                    </v-btn>
                    <v-btn :disabled="isloading" rounded depressed color="orange" class="white--text mx-1"
                        v-if="recibo.factura_url != null && tipo == 'factura'"  target="_blank" :href="`storage/recibos/${recibo.factura_url}`">ver recibo pdf
                    </v-btn>
                </v-col>
            </v-row>
            <botones-parte-trabajo v-if="tipo == 'parte-trabajo'" :isloading="isloading" :tipo="tipo" :recibo="recibo"
                v-on:save_parte_trabajo="saveParteTrabajo" v-on:convertir_factura="convertirFactura" v-on:convertir_nota="convertirNota">
            </botones-parte-trabajo>
            <v-row v-if="recibo.id != null && tipo != 'parte-trabajo'">
                <v-col cols="12">
                    <v-btn :disabled="isloading" rounded depressed outlined color="blue" @click="email_dialog = true">enviar</v-btn>
                </v-col>
            </v-row>
        </v-form>
        <email-content-dialog :isloading="isloading" v-on:close_dialog="closeDialog" :email_dialog="email_dialog" 
            :url_files="url_files" :tipo="tipo" :email="recibo.cliente.email">
        </email-content-dialog>

        <AddAlbaranes :modalAddAlbaranes="modalAddAlbaranes" :closeModalAlbaranes="closeModalAlbaranes" 
            :albaranesEnviados="albaranesEnviados" :closeModalAlbaranesListo="closeModalAlbaranesListo" 
            :addAlbaranAlaLista="addAlbaranAlaLista" :deleteItem="deleteItem" :checkbox="checkbox"
            :obtenerServicio="obtenerServicio"/>
        <Confirmar :modalConfirm="modalConfirm" :convertirFacturaConfirmado="convertirFacturaConfirmado" 
            :itemPdf="itemPdf" :closeModal="closeModal" :convertirNotaConfirmado="convertirNotaConfirmado" 
            :ConvertirPresupuestoAFactura="ConvertirPresupuestoAFactura"/>
    </v-container>
</div>
</template>

<script>
    import Confirmar from "./Confirmar.vue"
    import { saveAs } from 'file-saver';
    import { total_mixin } from '../mixins/total_mixin'
    import { servicios_mixin } from '../mixins/servicios_mixin'
    import displayTotales from './displayTotales'
    import displayTotal from './displayTotal'
    import emailContentDialog from './emailContentDialog'
    import botonesFactura from './botonesFactura'
    import botonesPresupuesto from './botonesPresupuesto'
    import botonesParteTrabajo from './botonesParteTrabajo'
    import AddAlbaranes from './AddAlbaranes'
    export default {
        components: {
            displayTotales,
            displayTotal,
            emailContentDialog,
            botonesFactura,
            botonesPresupuesto,
            botonesParteTrabajo,
            AddAlbaranes,
            Confirmar
        },

        mixins: [total_mixin, servicios_mixin],

        data() {
            return {
                itemPdf : '',
                modalConfirm : false, 
                albaranesEnviados: [],
                checkbox: [],
                albaranesSeleccionados : [],
                modalAddAlbaranes : false,
                menu: false,
                email_dialog: false,
                presupuestos: [],
                presupuesto: {
                    id: null,
                    total: 0,
                    nro_presupuesto: null,
                    user_id: localStorage.getItem('user_id')
                },
                clientes: [],
                iva: true,
                calcular_iva: true,
                tipo: 'factura',
                recibo: {
                    id: null,
                    cliente_id: '',
                    cliente: {
                        id: null,
                        email: null
                    },
                    fecha: new Date().toISOString().substr(0, 10),
                    sub_total: 0,
                    iva: 0,
                    porcentaje_descuento: 0,
                    total_descuento: 0,
                    total: 0,
                    presupuesto_url: null,
                    factura_url: null,
                    nota_url: null,
                    has_iva: true,
                    servicios: [],
                    user_id: localStorage.getItem('user_id'),
                    albaranes: []
                },
                servicio: {
                    recibo_id: null,
                    id: `temp-${new Date().getTime()}`,
                    descripcion: '',
                    cantidad: '',
                    precio: '',
                    importe: '',
                    user_id: localStorage.getItem('user_id')
                },
                validadorUrl : '',
                user_id: localStorage.getItem('user_id'),

            }
        },

        watch: {
            'servicio.cantidad'(n) {
                this.servicio.importe = parseFloat(n * this.servicio.precio).toFixed(2)
            },

            'servicio.precio'(n) {
                this.servicio.importe = parseFloat(n * this.servicio.cantidad).toFixed(2)
            },

            'recibo.servicios'(n) {
                if (n.length > 0) {
                    this.calcularTotales(n)
                }
            },

            'recibo.porcentaje_descuento'(n) {
                if (this.recibo.servicios.length > 0) {
                    this.calcularTotales(this.recibo.servicios)
                }
            },

            'recibo.has_iva'(n) {
                if (this.recibo.servicios.length > 0) {
                    this.calcularTotales(this.recibo.servicios)
                }
            }
        },

        created() {
            this.tipo = this.$route.query.tipo || 'presupuesto'
            this.getClientes()
            if (this.$route.query.id) {
                this.getReciboById(this.$route.query.id)
            }
        },

        mounted() {
            this.validadorUrl = window.location.hash
            this.dataGet()  // Obtenemos albaranes de cliente sin contabilidad en --> this.consulta

            this.tipo == 'parte-trabajo' ? this.getPrespuestos() : null
            if (this.tipo == 'nota') {
                this.recibo.has_iva = false
            }
            
            if (this.$route.query.id && this.tipo == 'parte-trabajo') {
                this.getPresupuestoAsociado(this.$route.query.id)
            }
        },

        methods: {
            openModalConfirmar(){
                this.modalConfirm = true    
            },
            closeModal(){
                this.modalConfirm = false
            },
            addAlbaranAlaLista(item){
                this.item = item                
                this.item.descripcion = 'Albaran_' + item.nro_factura;
                this.item.cantidad = 1;   
                this.item.precio = item.importe;
                this.item.id = `temp-${new Date().getTime()}`;       
                this.servicio = this.item
                this.addService()
                this.calcularTotales(this.recibo.servicios)
                this.saveFactura();
            },
            modalAddAlbaranesFuncion(){
                this.modalAddAlbaranes = true
            },
            closeModalAlbaranes(checkbox){
                let confirmar =  confirm("Al cerrar el cuadro los elementos seleccionados no se agregaran a tu Factura.¿Estás de acuerdo?")
                if (confirmar != false) {
                    for (let index = 0; index < checkbox.length; index++) {
                        if (checkbox[index] != null) {
                            for (let z = 0; z < this.recibo.servicios.length; z++) {
                                let albaranEliminar = 'Albaran_' + checkbox[index];
                                if ( (this.recibo.servicios[z].descripcion) == albaranEliminar){
                                    this.recibo.servicios.splice(z ,1)
                                }      
                            }
                            checkbox[index] = undefined
                        }
                        if(this.servicio.descripcion.length == 0)
                        {
                            this.recibo.sub_total = 0;
                            this.recibo.iva = 0;
                            this.recibo.total_descuento = 0;
                            this.recibo.total = 0;
                        }   
                        this.modalAddAlbaranes = false;
                        this.checkbox = [];
                        this.albaranesSeleccionados = [];
                    }
                }                
            },
            closeModalAlbaranesListo(){
                this.modalAddAlbaranes = false
            },
            volver(){
                this.$router.push(`/lista-facturas/${this.user_id}`)
            },
            dataGet(){
                axios.get(`api/get-data-albaranes/${localStorage.getItem('user_id')}`).then(res => {
                    this.albaranesEnviados = res.data.albaranesEnviados
                    this.consulta = res.data.albaranesEnviados
                }, res => {
                    this.$toast.error('Error consultando albaranes Enviados')
                })
            },            
            changeParams(tipo_recibo) {
                let current_path = this.$route.path
                let id = this.$route.query.id
                this.tipo = tipo_recibo
                this.$router.push({
                    path: current_path,
                    query: {
                        id: id,
                        tipo: tipo_recibo
                    }
                }).catch(() => {})
            },
            getClientes() {
                axios.get(`api/get-clientes/${localStorage.getItem('user_id')}`).then(res => {
                    this.clientes = res.data
                }, res => {
                    this.$toast.error('Error consultando Clientes')
                })
            },
            getReciboById(recibo_id) {
                axios.get(`api/get-recibo-by-id/${recibo_id}`).then(res => {
                    this.recibo = res.data
                }, res => {
                    this.$toast.error('Error consultando Factura')
                })
            },
            saveRecibo(tipo, convertir_factura = false) {
                if (tipo == 'parte-trabajo') {
                    this.recibo.nro_presupuesto_id = this.presupuesto.nro_presupuesto_id
                }
                this.recibo.albaranes = this.albaranesSeleccionados;
                let reciboalbaranes = this.recibo;
                reciboalbaranes.checkbox = this.checkbox;

                axios.post(`api/save-recibo/${tipo}/${convertir_factura}`, reciboalbaranes).then(res => {
                    this.recibo = res.data
                    this.changeParams(tipo)
                }, res => {
                    this.$toast.error('Error guardando recibo')
                })
                // this.$router.go();
            },
            saveFactura() {
                this.saveRecibo('factura', false)
            },
            savePresupuesto() {
                this.saveRecibo('presupuesto')
            },
            convertirFactura() {
                this.modalConfirm = true
                this.itemPdf = ', Factura'
            },
            ConvertirPresupuestoAFactura(){
                this.saveRecibo('factura', true)
                this.modalConfirm = false
                this.itemPdf = ''
            },
            saveParteTrabajo() {
                this.saveRecibo('parte-trabajo')
            },
            convertirNota() {
                this.modalConfirm = true
                this.itemPdf = 'Nota'
            },
            convertirNotaConfirmado(){
                this.recibo.iva = 0
                let total = this.recibo.sub_total - this.recibo.total_descuento
                this.recibo.total = parseFloat(total).toFixed(2)
                this.saveRecibo('nota', true)
                this.modalConfirm = false
                this.itemPdf = ''
            },
            convertirNotaFactura() {
                this.modalConfirm = true
                this.itemPdf = 'Factura' 
            },
            convertirFacturaConfirmado(){
                this.recibo.has_iva = true
                this.tipo = 'factura'
                this.recibo.has_iva == true ? this.calcularTotales(this.recibo.servicios) : null
                this.saveRecibo('factura', true)
                this.modalConfirm = false
                this.itemPdf = ''
            },
            closeDialog() {
                this.email_dialog = false
            },
            getPrespuestos() {
                axios.get(`api/get-presupuestos-for-parte-trabajo/${localStorage.getItem('user_id')}`).then(res => {
                    this.presupuestos = res.data
                }, res => {
                    this.$toast.error('Error cargando presupuestos')
                })
            },
            getPresupuestoAsociado(recibo_id) {
                axios.get(`api/get-presupuesto-asociado/${recibo_id}`).then(res => {
                    if (JSON.stringify(res.data) !== '{}') {
                        this.presupuesto = res.data
                    }
                }, res => {
                    this.$toast.error('Error cargando presupuesto')
                })
            }
        },
        computed: {
            isloading() {
                return this.$store.getters.getloading
            },
            errors() {
                return this.$store.getters.geterrors
            },
            totales() {
                return {
                    sub_total: this.recibo.sub_total,
                    iva: this.recibo.iva,
                    porcentaje_descuento: this.recibo.porcentaje_descuento,
                    total_descuento: this.recibo.total_descuento,
                    total: this.recibo.total
                }
            },
            url_files() {
                return {
                    presupuesto_url: this.recibo.presupuesto_url,
                    factura_url: this.recibo.factura_url,
                    nota_url: this.recibo.nota_url
                }
            },
            beneficio() {
                let total_recibo = this.recibo.has_iva ? this.recibo.total : this.recibo.sub_total
                let beneficio_not_parsed = parseFloat(this.presupuesto.total) - parseFloat(total_recibo)
                let beneficio_parsed = parseFloat(beneficio_not_parsed).toFixed(2)
                return `${beneficio_parsed}€`
            },
        }
    }
</script>