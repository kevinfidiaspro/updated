export const servicios_mixin = {
  data(){
    return {
      headers: 
      [
          { text: 'Descripción', align: 'left', value: 'descripcion' },
          { text: 'Cantidad', value: 'cantidad' },
          { text: 'Precio', value: 'precio' },
          { text: 'Importe', value: 'importe' },
          { text: 'Acciones', value: 'action', sortable: false },
      ],
    }
  },
  methods:{
    addService() {
        // Si el servicio a introducir esta vacio da error si no lo introduce en la lista 
        if (this.servicio.descripcion !='' && this.servicio.precio !='') {
            //  albaran
            if (this.servicio.descripcion.substr(0, 7) == 'Albarán')
            {
                this.recibo.servicios.push(this.servicio)
                this.resetServicio()
                this.$toast.sucs('Albaran Añadido')
            }
            // servicio manual
            else if (this.servicio.descripcion.substr(0, 7) != 'Albarán' && this.servicio.precio !='')
            {
                this.recibo.servicios.push(this.servicio)
                this.resetServicio()
                this.$toast.sucs('Servicio Añadido')
            }
            else
            {
                this.$toast.error('Error')
            }            
        }
        else
        {
            this.$toast.error('Introduzca: DESCRIPCION, CANTIDAD y PRECIO CORRECTOS')
        } 
    },

    obtenerServicio(checkbox){
        for (let index = 0; index < this.recibo.servicios.length; index++) {
            let albaranEliminar = 'Albaran_' + checkbox;  
            if ((this.recibo.servicios[index].descripcion) == albaranEliminar){
                return this.recibo.servicios[index];
            }                        
        }
    },

    deleteItem(servicio,checkbox) {
        // Verificamos si el elemento a elminar es temporal o esta en BD
        let tipo = String(servicio.id).substr(0,1);  
        // Si es temporal lo eliminamos
        if (tipo == 't') {
            if (servicio) {
                let data = servicio.descripcion.substr(8, 5);        
                for (let index = 0; index < checkbox.length; index++) {
                    if (data == checkbox[index]) {
                        checkbox[index] = undefined
                    }            
                }
            }
            //  Eliminamos el servicio de la lista de servicios a facturar
            let index = this.recibo.servicios.indexOf(servicio)
            this.recibo.servicios.splice(index, 1)
            // Restauramos los campos de datos de servicio para poder almacenar mas
            this.resetServicio()
            // Calculamos los totales de la factura
            this.calcularTotales(this.recibo.servicios)
        }

        // Si no es temporal realizamos cambios en BD
        else
        {
        //  Eliminamos el servicio de la lista de servicios a facturar
        let index = this.recibo.servicios.indexOf(servicio)
        this.recibo.servicios.splice(index, 1)
        // Restauramos los campos de datos de servicio para poder almacenar mas
        this.resetServicio()
        // Calculamos los totales de la factura
        this.calcularTotales(this.recibo.servicios)
        // Almacenamos el servicio nombre para comprobar si es albaran 
        // let elementoname = servicio.descripcion.substr(0, 7)
        let elementoname = servicio.descripcion
        // Enviamos para la eliminacion el nombre y las id de servicio y de recibo
        this.removeContabilizado(elementoname, servicio.id, this.recibo.id);
        }
    },

    removeContabilizado(elemento, idServicio, idRecibo) {
        axios.get(`api/remove-contabilizado/${elemento}/${idServicio}/${idRecibo}`).then(res => {
            // almacenamos el resultado de la eliminacion y lo mostramos en el listado
            this.resultado = res.data
            //  Si eliminamos todos los servicios mostramos la linea nula
            if (this.recibo.servicios.length==0) {
                this.recibo.servicios.push(this.resultado)
            }
            this.$toast.sucs('Actualizando datos ....')
            // Almacenamos la factura y creamos el pdf y refrescamos listado albaranes disponibles
            this.saveFactura();
            this.dataGet();
        }, res => {
            this.$toast.error('Error eliminando estado contabilizado')
        })
    },
    setItem(servicio) {
        this.servicio = servicio
        if (servicio.descripcion.substr(0, 7) == 'Albaran') {
            this.setAlbaran()
        } 
    },
    setAlbaran() {
        this.addService()
    },
    resetServicio() {
        this.servicio = {
            recibo_id: null,
            id: `temp-${new Date().getTime()}`,
            descripcion: '',
            cantidad: '',
            precio: '',
            importe: ''
        }
    },
  }
}
