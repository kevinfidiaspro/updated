<template>
    <v-dialog @click:outside="closeDialog" v-model="email_dialog" width="600">

        <v-card>
            <v-card-title class="headline primary white--text" dark primary-title>
                Enviar email
            </v-card-title>

            <v-card-text class="px-3 py-3">

                <loader v-if="isloading"></loader>

                <v-row>
                    <v-col cols="12" sm="12" md="12" lg="12" xl="12">
                        <small>Ingrese varios destinatarios. Pulse "ENTER" o "TAB" para separarlos.</small>
                        <v-combobox
                         v-model="form.emails"
                          :items="items"
                          label="Destinatarios"
                          multiple
                          chips>
                          <template v-slot:selection="{ attrs, item, parent, selected}">
                            <v-chip
                              :key="JSON.stringify(item)"
                              v-bind="attrs"
                              :input-value="selected"
                              
                              @click:close="parent.selectItem(item)"
                            >
                              <v-avatar
                                class="accent white--text"
                                left
                                v-text="item.slice(0, 1).toUpperCase()"
                              ></v-avatar>
                              {{ item }}
                              <v-icon
                                small
                                @click="parent.selectItem(item)"
                              >
                                $delete
                              </v-icon>
                            </v-chip>
                          </template>
                        </v-combobox>
                         <v-col cols="12" md="10">
                        <v-textarea v-model="form.descripcion" hide-details filled label="Descripción"></v-textarea>
                    </v-col>
                    </v-col>

                    <v-col cols="12" md="10">
                        
                    </v-col>
                </v-row>
                
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-btn color="blue" class="white--text" @click="sendEmail">Enviar Correo</v-btn>

                <v-spacer></v-spacer>

                <v-btn color="red" text @click="closeDialog">cerrar</v-btn>

            </v-card-actions>

        </v-card>

    </v-dialog>

</template>

<script>
    export default {
        props: ['email_dialog', 'isloading', 'modelFactura'],

        data() {
            return {
                items : [],
                form:{
                    emails :'',
                    descripcion : '',
                    tipo_factura : ''
                }
               
            }
        },

        methods: {
            sendEmail() {

                //validaciones de email
                this.form.tipo_factura = this.modelFactura

                axios.post(`api/enviar-lote-facturas/${localStorage.getItem('user_id')}`, {
                    form : this.form
                }).then(res => {
                    this.$toast.sucs('Email enviado con exito')
                    this.form = {
                        emails :'',
                        descripcion : '',
                        tipo_factura : ''
                    }
                    this.closeDialog()
                }, res => {
                    this.$toast.error('Fallo envio de email')
                })
            },

            closeDialog() {
                this.$emit('close_dialog')
            },

        },
        computed : {
            objetoUser(){
                let user = {
                    nombre : localStorage.user_name,
                    email : localStorage.user_email,

                }
                return user
            }
        }

    }
</script>