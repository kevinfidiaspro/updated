<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title
                    >Guardar / Editar Factura
                    {{ tipo == 2 ? "Proforma" : "" }}</v-toolbar-title
                >
            </v-toolbar>
            <v-card-text>
                <div class="font-weight-bold mb-3 black--text">
                    DATOS DEL CLIENTE Y FECHA
                </div>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-autocomplete
                            dense
                            outlined
                            v-model="id_cliente"
                            :items="usuarios"
                            item-value="id"
                            item-text="nombres"
                            label="Cliente"
                        ></v-autocomplete>
                    </v-col>
                    <!-- v-col cols="12" md="4">
            <v-autocomplete dense outlined v-model="id_proyecto" :items="proyectos" item-value="id" item-text="nombre" label="Proyecto"></v-autocomplete>
          </>
          <v-col cols="12" md="4">
            <v-text-field v-model="cliente" outlined dense label="Cliente"></v-text-field>
          </v-col> -->
                    <v-col cols="12" md="4">
                        <v-menu
                            v-model="menu2"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="fecha"
                                    outlined
                                    dense
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="fecha"
                                @input="menu2 = false"
                            ></v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea
                            outlined
                            label="Descripcion-notas"
                            v-model="notas"
                        ></v-textarea>
                    </v-col>
                </v-row>
                <div class="font-weight-bold mb-3 black--text">
                    DATOS DEL SERVICIO A FACTURAR
                </div>
                <v-row>
                    <v-col cols="12" md="3">
                        <v-text-field
                            v-model="description"
                            outlined
                            dense
                            label="Descripcion"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-text-field
                            type="number"
                            v-model="quantity"
                            outlined
                            dense
                            label="Cantidad"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-text-field
                            type="number"
                            v-model="price"
                            outlined
                            dense
                            label="Precio"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-text-field
                            type="number"
                            v-model="imp"
                            outlined
                            dense
                            label="Importe"
                            readonly
                        ></v-text-field>
                    </v-col>
                </v-row>
                <div class="mb-5">
                    <v-btn
                        v-if="editing == null"
                        @click="HandlerAdd()"
                        color="primary"
                        class="white--text"
                        rounded
                        >Agregar servicio</v-btn
                    >
                    <v-btn
                        v-else
                        @click="HandlerAdd()"
                        color="primary"
                        class="white--text"
                        rounded
                        >Actualizar servicio</v-btn
                    >
                    <v-btn
                        @click="LimpiarServicio()"
                        color="red"
                        class="white--text"
                        rounded
                        >Limpiar servicio</v-btn
                    >
                </div>
                <v-data-table
                    dense
                    :headers="headers"
                    :items="items"
                    disable-pagination
                    hide-default-footer
                    class="elevation-1"
                >
                    <template v-slot:item.action="{ item }">
                        <!-- <v-icon @click="edit(item)" color="blue">mdi-pencil</v-icon> -->
                        <v-icon @click="editItem(item)" color="primary"
                            >mdi-pencil</v-icon
                        >
                        <v-icon @click="deleteItem(item)" color="red"
                            >mdi-delete</v-icon
                        >
                    </template>
                </v-data-table>

                <div class="font-weight-bold mb-3 black--text my-3">
                    DESCUENTOS Y TOTALES
                </div>
                <v-row justify="space-between">
                    <v-col cols="12" md="2">
                        <v-text-field
                            type="numeric"
                            v-model="desc"
                            outlined
                            dense
                            label="Descuento %"
                            @change="calcula_total()"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-autocomplete
                            dense
                            outlined
                            v-model="cuenta"
                            :items="cuentas"
                            item-value="id"
                            item-text="nombre_banco"
                            label="Banco"
                            readonly
                        ></v-autocomplete>
                        <v-autocomplete
                            dense
                            outlined
                            v-model="cuenta"
                            :items="cuentas"
                            item-value="id"
                            item-text="cuenta"
                            label="Cuenta bancaria"
                        ></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-card class="px-2 py-2">
                            <span class="font-weight-bold"
                                >Sub Total:
                                {{ parseFloat(subTotal()).toFixed(2) }}€</span
                            ><br />
                            <span class="font-weight-bold"
                                >Descuento:
                                {{ parseFloat(descuento()).toFixed(2) }}€</span
                            ><br />
                            <span class="font-weight-bold"
                                >Iva:
                                {{
                                    parseFloat(HandlerIva(false)).toFixed(2)
                                }}€</span
                            ><br />
                            <span class="font-weight-bold"
                                >Total:
                                {{ parseFloat(this.total).toFixed(2) }}€</span
                            >
                        </v-card>
                    </v-col>
                </v-row>
                <v-row>
                    <v-checkbox
                        label="incluir IVA 21%"
                        color="primary"
                        v-model="incl_iva"
                        @change="HandlerIva(true)"
                    ></v-checkbox>
                </v-row>
            </v-card-text>
        </v-card>
        <v-row class="mt-3">
            <!-- Botones Navegacion -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="
                                $router.push(
                                    tipo == 1
                                        ? '/lista-facturas'
                                        : '/lista-facturas-pro'
                                )
                            "
                            :loading="isloading"
                            :disabled="isloading"
                            color="blue"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text"
                                >mdi-arrow-left-bold-outline</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Volver</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="guardarFactura()"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text"
                                >mdi-content-save-all</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Guardar Factura</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            v-if="$route.query.id != null"
                            @click="duplicarFactura()"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-6 h-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"
                                />
                            </svg>
                        </v-btn>
                    </template>
                    <span>Duplicar</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            v-if="$route.query.id != null && tipo == 2"
                            @click="ChangeFactura()"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-6 h-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"
                                />
                            </svg>
                        </v-btn>
                    </template>
                    <span>Cambiar Tipo</span>
                </v-tooltip>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import { parseArgs } from "util";
export default {
    data() {
        return {
            notas: "",
            tipo: 1,
            id_cliente: null,
            menu2: false,
            id_proyecto: "",
            cliente: "",
            fecha: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
                .toISOString()
                .substr(0, 10),
            description: "",
            quantity: 0,
            price: parseFloat(0).toFixed(2),
            imp: parseFloat(0).toFixed(2),
            desc: 0,
            iva: parseFloat(0).toFixed(2),
            usuarios: [],
            headers: [
                { text: "Descripcion", value: "descripcion", sortable: false },
                { text: "Cantidad", value: "cantidad", sortable: false },
                { text: "Precio", value: "precio", sortable: false },
                { text: "Importe", value: "importe", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            items: [],
            id: 0,
            total: parseFloat(0).toFixed(2),
            id_items_factura: [],
            incl_iva: true,
            proyectos: [],
            cuentas: [],
            cuenta: 1,
            nro_factura: 0,
            editing: null,
        };
    },
    created() {
        if (this.$route.path == "/registrr-facturas-pro") {
            this.tipo = 2;
        }
        if (this.$route.query.cliente != null) {
            this.id_cliente = parseInt(this.$route.query.cliente);
            console.log("cleinte_cambi");
            console.log(this.id_cliente);
        }
        if (this.$route.query.id != undefined) {
            this.getFacturaId(this.$route.query.id);
        }
        this.getActiveClientes();
        this.getAllCuentas();
    },
    watch: {
        id_cliente(val) {
            this.getProyectosByUser(val, 0);
        },
        quantity(n) {
            this.imp = parseFloat(n * this.price).toFixed(2);
        },
        price(n) {
            this.imp = parseFloat(n * this.quantity).toFixed(2);
        },
    },
    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
        errors() {
            return this.$store.getters.geterrors;
        },
    },
    methods: {
        ChangeFactura() {
            axios
                .post(`api/change-factura-type`, {
                    id: this.$route.query.id,
                    tipo: 1,
                })
                .then(
                    (res) => {
                        this.$router.push(`/registrr-facturas?id=${this.id}`);
                    },
                    (res) => {}
                );
        },
        getAllCuentas() {
            axios.get(`api/get-cuentas-banco`).then(
                (res) => {
                    this.cuentas = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando Cuentas Bancarias");
                }
            );
        },
        async getProyectoById(proyecto_id) {
            try {
                const response = await axios.get(
                    `api/get-proyecto-by-id/${proyecto_id}`
                );
                this.id_cliente = response.data.usuario_id;
            } catch (error) {
                console.log(error);
            }
        },
        getActiveClientes() {
            axios.get(`api/get-all-clientes-active-proyectos`).then(
                (res) => {
                    this.usuarios = res.data.users;
                    for (let i = 0; i < this.usuarios.length; i++) {
                        const element = this.usuarios[i];
                        element.created_at = new Date(
                            element.created_at
                        ).toLocaleDateString();
                    }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        getProyectosByUser(userId, tipo) {
            axios.get(`api/get-proyectos-by-user-id/${userId}/${tipo}`).then(
                (res) => {
                    this.proyectos = res.data;
                    if (this.$route.query.proyecto != undefined) {
                        this.id_proyecto = parseInt(this.$route.query.proyecto);
                    }
                },
                (res) => {
                    this.$toast.error("Error consultando proyectos");
                }
            );
        },
        async getAllProyectos() {
            try {
                const response = await axios.get(`api/get-all-proyectos`);
                this.proyectos = response.data.filter(
                    (index) => index.usuario !== null
                );
                if (this.$route.query.proyecto != undefined) {
                    this.id_proyecto = parseInt(this.$route.query.proyecto);
                }
            } catch (error) {
                console.log(error);
            }
        },
        async getFacturaId(id) {
            try {
                const response = await axios.get(`api/show-facturas/${id}`);
                this.nro_factura = response.data.nro_factura;
                this.items = response.data.items_factura;
                this.id_proyecto = response.data.id_proyecto;
                this.desc = response.data.descuento;
                this.incl_iva = response.data.status_iva;
                this.subTotal();
                this.cuenta = response.data.id_cuenta;
                this.notas = response.data.notas;
                this.id_cliente = response.data.id_cliente;
                if (response.data.id_cliente == null) {
                    this.id_cliente = response.data.cliente_real.id;
                }
                //this.getProyectoById(this.id_proyecto)
                this.calcula_total();
                this.fecha = response.data.fecha;
            } catch (error) {
                console.log(error);
            }
        },
        HandlerAdd() {
            if (
                this.description != "" &&
                this.quantity != "" &&
                this.price != "" &&
                this.imp != ""
            ) {
                const item = {
                    descripcion: this.description,
                    cantidad: this.quantity,
                    precio: this.price,
                    importe: this.imp,
                    id: Date.now() + "a",
                };
                if (this.editing != null) {
                    const linea = this.items.find((ele) => ele == this.editing);
                    const index = this.items.findIndex(
                        (ele) => ele == this.editing
                    );
                    console.log(index);
                    console.log(linea);
                    linea.descripcion = item.descripcion;
                    linea.cantidad = item.cantidad;

                    linea.precio = item.precio;

                    linea.importe = item.importe;
                    this.items[index] = linea;
                    console.log(this.items);
                } else {
                    this.items.push(item);
                }
                this.id = Date.now() + "a";
                this.description = "";
                this.quantity = "";
                this.price = "";
                this.imp = "";
                this.editing = null;
                this.calcula_total();
            }
        },
        LimpiarServicio() {
            this.editing = null;
            this.id = Date.now() + "a";
            this.description = "";
            this.quantity = "";
            this.price = "";
            this.imp = "";
        },
        edit(item) {
            this.description = item.description;
            this.quantity = item.quantity;
            this.price = item.price;
            this.imp = item.imp;
        },
        deleteItem(item) {
            const search = this.items.indexOf(item);
            if (search > -1) {
                this.items.splice(search, 1);
            }
        },
        duplicarFactura() {
            axios
                .get(
                    `api/duplicar-factura/${this.$route.query.id}/${this.tipo}`
                )
                .then((res) => {
                    this.$router.push(`/registrr-facturas?id=${this.id}`);
                });
        },
        editItem(item) {
            this.editing = item;
            let elemento_data = JSON.parse(JSON.stringify(item));

            this.description = elemento_data.descripcion;
            this.quantity = elemento_data.cantidad;
            this.price = elemento_data.precio;
            this.imp = elemento_data.importe;
            this.id = elemento_data.id;

            //this.deleteItem(item);
        },
        subTotal() {
            const total = this.items.reduce((acc, arr) => {
                acc = parseFloat(acc) + parseFloat(arr.importe);
                acc = parseFloat(acc).toFixed(2);
                return acc;
            }, 0);
            return total;
        },
        descuento() {
            let descuentoNeto = (this.subTotal() * this.desc) / 100;
            return descuentoNeto;
        },
        HandlerIva(calculatotal) {
            if (this.incl_iva == true) {
                const number =
                    parseFloat(this.subTotal()).toFixed(2) -
                    parseFloat(this.descuento()).toFixed(2);
                const mult = (number * 21) / 100;
                this.iva = mult;
                let ivacalc = parseFloat(mult).toFixed(2);
                if (calculatotal) {
                    this.calcula_total();
                }
                return mult;
            } else {
                this.iva = 0;
                if (calculatotal) {
                    this.calcula_total();
                }
                return 0;
            }
        },
        calcula_total() {
            let subtotal = this.subTotal();
            subtotal = parseFloat(subtotal).toFixed(2);
            let descuento = this.descuento();
            descuento = parseFloat(descuento).toFixed(2);
            let iva = this.HandlerIva(false);
            this.total = subtotal - descuento + iva;
            return parseFloat(this.total).toFixed(2);
        },
        async guardarFactura() {
            try {
                const request = {
                    id_proyecto:
                        (this.id_proyecto ?? 0) == ""
                            ? 0
                            : this.id_proyecto ?? 0,
                    total: this.calcula_total(),
                    descuento: this.desc,
                    fecha: this.fecha,
                    id_cuenta: this.cuenta,
                    items_factura: this.items,
                    status_iva: this.incl_iva,
                    nro_factura: this.nro_factura,
                    tipo: this.tipo,
                    id_cliente: this.id_cliente,
                    notas: this.notas,
                };
                // console.log("******datat*****", request);
                // console.log("params", this.$route.query.id);
                if (this.$route.query.id) {
                    // console.log('this.$route.query.id');
                    // console.log(this.$route.query.id);
                    // console.log('request');
                    // console.log(request);
                    // Editar
                    await axios.post(
                        "api/update-facturas/" + this.$route.query.id,
                        request
                    );
                    // Eliminar items
                    if (this.id_items_factura.length > 0) {
                        const request_item = {
                            id_items_factura: this.id_items_factura,
                        };
                        await axios.post("api/delete-items-facturas", request);
                    }
                    this.$toast.sucs("Editado con exito");
                } else {
                    // Registro
                    await axios.post("api/store-facturas", request);
                    this.$toast.sucs("Registrado con exito");
                }
                this.$router.push(
                    this.tipo == 1 ? "/lista-facturas" : "/lista-facturas-pro"
                );
            } catch (error) {
                const error_data = Object.values(error.response.data.error);
                const msg_error = error_data.flat();
                console.log(msg_error);
                let lt = `<ul>`;
                for (const iterator of msg_error) {
                    lt = lt + `<li>${iterator}</li>`;
                }
                lt = lt + `</ul>`;
                this.$toast.error("Error guardando la factura" + lt);
            }
        },
    },
};
</script>
<style>
.w-6 {
    width: 2rem;
    height: 2rem;
    color: white;
}
</style>
