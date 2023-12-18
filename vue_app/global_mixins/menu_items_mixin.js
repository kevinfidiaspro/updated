export const menu_items_mixin = {
    data() {
        return {
            items: [
                {
                    path: `/`,
                    icon: "mdi-home-outline",
                    text: "Inicio",
                    user: [1, 3, 6, 5, 7, 8],
                },
                {
                    path: `/lista-potenciales`,
                    icon: "mdi-file-powerpoint",
                    text: "Potenciales",
                    user: [1, 5, 7, 6, 8, 9],
                },
                {
                    path: `/calendario-seguimiento`,
                    icon: "mdi-clock",
                    text: "Seguimiento Potenciales",
                    user: [1, 5, 8, 9],
                },
                {
                    path: `/calendario-seguimiento-cliente`,
                    icon: "mdi-clock",
                    text: "Seguimiento Clientes",
                    user: [1, 5, 8, 9],
                },
                {
                    path: ``,
                    icon: "mdi-account-tie",
                    text: "Clientes",
                    user: [1, 5, 6, 7, 8, 9],
                    hijo: true,
                    children: [
                        {
                            path: `/lista-clientes`,
                            icon: "mdi-account-tie",
                            text: "Clientes",
                            user: [1, 5, 6, 7, 8, 9],
                        },
                        {
                            path: `/lista-proyectos-cliente`,
                            icon: "mdi-file-cabinet",
                            text: "Proyectos",
                            user: [1, 5, 6, 7, 8],
                        },
                        {
                            path: `/consumo-proyecto`,
                            icon: "mdi-clock",
                            text: "Comsumo Proyecto",
                            user: [1],
                        },
                    ],
                },
                {
                    path: `/calendario-reuniones`,
                    icon: "mdi-clock",
                    text: "Reuniones",
                    user: [1, 6, 5, 7, 8],
                },
                {
                    path: `/lista-producto`,
                    icon: "mdi-shape-plus",
                    text: "Productos",
                    user: [1, 5, 7, 8, 9],
                },
                {
                    path: `/tickets`,
                    icon: "mdi-ticket-account",
                    text: "Tickets",
                    user: [1, 6, 7],
                },
                {
                    path: `/tareas`,
                    icon: "mdi-file-tree",
                    text: "Tareas",
                    user: [1, 3, 6, 7],
                },
                {
                    path: `/lista-plantilla`,
                    icon: "mdi-whatsapp",
                    text: "Plantillas",
                    user: [1, 5, 8],
                },
                {
                    path: ``,
                    icon: "mdi-account-tie-outline",
                    text: "Administración",
                    user: [1, 3, 5, 6, 7, 8, 9],
                    hijo: true,
                    children: [
                        {
                            path: `/lista-gastos`,
                            icon: "mdi-calculator-variant",
                            text: "Gastos",
                            user: [1],
                        },
                        {
                            path: `/lista-facturas`,
                            icon: "mdi-file-table-outline",
                            text: "Facturas",
                            user: [1, 5, 7],
                        },
                        {
                            path: `/lista-facturas-pro`,
                            icon: "mdi-file-table-outline",
                            text: "Facturas Proforma",
                            user: [1, 5, 7],
                        },
                        {
                            path: `/lista-ingresos`,
                            icon: "mdi-calculator-variant",
                            text: "Ingresos",
                            user: [1],
                        },

                        {
                            path: `/lista-fichajes`,
                            icon: "mdi-card-account-details-outline",
                            text: "Fichajes",
                            user: [1, 3, 5, 6, 7, 8, 9],
                        },
                        {
                            path: "/calendario-vacaciones",
                            icon: "mdi-calendar-clock",
                            text: "Vacaciones",
                            user: [1, 3, 5, 6, 7, 8, 9],
                        },
                    ],
                },
                {
                    path: ``,
                    icon: "mdi-billboard",
                    text: "Marketing",
                    user: [1, 6, 7],
                    hijo: true,
                    children: [
                        {
                            path: `/lista-promociones`,
                            icon: "mdi-bullhorn",
                            text: "Promociones",
                            user: [1],
                        },
                        {
                            path: `/lista-social`,
                            icon: "mdi-instagram",
                            text: "Contenido Redes",
                            user: [1, 6, 7],
                        },
                        {
                            path: `/lista-paginas-facebook`,
                            icon: "mdi-facebook",
                            text: "Campañas Facebook",
                            user: [1, 7],
                        },
                    ],
                },
                {
                    path: ``,
                    icon: "mdi-controller-classic",
                    text: "Cuadro de Mando",
                    user: [1, 3, 5, 7, 8],
                    hijo: true,
                    children: [
                        {
                            path: ``,
                            icon: "mdi-teach",
                            text: "TFG",
                            user: [1, 3, 5, 6, 7, 8],
                            user_tfg: [1, 2, 3, 5, 6, 4, 7],

                            hijo: true,
                            children: [
                                {
                                    path: `/lista-gastos-tfg`,
                                    icon: "mdi-cash",
                                    text: "Gastos",
                                    user: [1, 3, 5, 6, 7],
                                    user_tfg: [1],
                                },
                                {
                                    path: `/lista-potenciales-tfg`,
                                    icon: "mdi-account-alert",
                                    text: "Potenciales",
                                    user: [1, 3, 5, 7, 8, 9],
                                    user_tfg: [1, 3, 2],
                                },

                                {
                                    path: `/lista-ventas-tfg`,
                                    icon: "mdi-point-of-sale",
                                    text: "Ventas Diarias",
                                    user: [1, 3, 5, 6, 7, 8],
                                    user_tfg: [5, 1, 2, 4],
                                },
                                /*{
                                    path: `/lista-ventas-resumen-tfg`,
                                    icon: "mdi-chart-arc",
                                    text: "Ventas Resumen",
                                    user: [1, 3,6],
                                    user_tfg: [1],
                                },*/
                                {
                                    path: `/lista-marketing-tfg`,
                                    icon: "mdi-chart-arc",
                                    text: "Marketing",
                                    user: [1, 3, 5, 6, 7],
                                    user_tfg: [1, 3, 6],
                                },
                            ],
                        },
                        {
                            path: ``,
                            icon: "mdi-domain",
                            text: "FIDIAS",
                            user: [1, 5, 6, 7, 8],

                            hijo: true,
                            children: [
                                {
                                    path: `/lista-ventas`,
                                    icon: "mdi-point-of-sale",
                                    text: "Ventas Diarias",
                                    user: [1, 5, 8],
                                },
                                /*{
                                    path: `/lista-ventas-resumen`,
                                    icon: "mdi-podium-gold",
                                    text: "Resumen Ventas Diarias",
                                    user: [1],
                                },*/
                                {
                                    path: `/lista-ventas-kit`,
                                    icon: "mdi-point-of-sale",
                                    text: "Ventas Kit",
                                    user: [1, 5, 7, 8],
                                },
                                {
                                    path: `/lista-seguimiento-campana`,
                                    icon: "mdi-clock",
                                    text: "SVD",
                                    user: [1, 6, 7],
                                },
                                {
                                    path: `/lista-marketing`,
                                    icon: "mdi-chart-arc",
                                    text: "Marketing",
                                    user: [1, 6, 7],
                                },
                            ],
                        },
                        {
                            path: "/estadisticas",
                            icon: "mdi-chart-line-stacked",
                            text: "Estadísticas",
                            user: [1],
                        },
                    ],
                },
                {
                    path: ``,
                    icon: "mdi-office-building",
                    text: "Empresa",
                    user: [1],

                    hijo: true,
                    children: [
                        {
                            path: "/lista-empresas",
                            icon: "mdi-office-building-outline",
                            text: "Empresas",
                            user: [1],
                        },
                        {
                            path: "/lista-usuario",
                            icon: "mdi-account-supervisor-circle",
                            text: "Usuarios",
                            user: [1],
                        },
                        {
                            path: `/lista-passwords`,
                            icon: "mdi-form-textbox-password",
                            text: "Contraseñas",
                            user: [1],
                        },
                    ],
                },
            ],
            items_old: [
                /* {
                    path: `/chat`,
                    icon: "mdi-home-outline",
                    text: "Prueba Chat",
                    user: [1],
                },
                {
                    path: `/lista-chats`,
                    icon: "mdi-home-outline",
                    text: "Prueba Lista Chats",
                    user: [1],
                },*/

                //

                {
                    path: `/lista-reservas`,
                    icon: "mdi-calendar-clock",
                    text: "Reserva de Sala",
                    user: [1, 6, 5, 7, 8],
                },

                /*{
                    path: `/lista-seguimiento-campana`,
                    icon: "mdi-clock",
                    text: "Seguimiento",
                    user: [1],
                    hijo: true,
                    children: [
                        {
                            path: `/lista-seguimiento-campana`,
                            icon: "mdi-clock",
                            text: "SVG",
                            user: [1],
                        },
                    ],
                },
                */
                {
                    path: `/contabilidad`,
                    icon: "mdi-calculator-variant",
                    text: "Contabilidad",
                    user: [1],
                    hijo: true,
                    children: [
                        /*{
                            path: `/lista-gastos`,
                            icon: "mdi-calculator-variant",
                            text: "Gastos",
                            user: [1],
                        },
                        {
                            path: `/tipos-gasto`,
                            icon: "mdi-calculator-variant",
                            text: "Tipos Gastos",
                            user: [1],
                        },*/
                    ],
                },
            ],
        };
    },

    computed: {
        user() {
            return this.$store.getters.getuser;
        },
        computedheaders: function () {
            if (this.user.role != 0) {
                const self = this;
                function filter(x) {
                    let condition = x.user.some(
                        (userole) => userole == self.user.role
                    );
                    if (x.user_tfg != null) {
                        condition =
                            condition &
                            x.user_tfg.some(
                                (userole) => userole == self.user.rol_tfg
                            );
                    }

                    return condition;
                }
                let filtrao = self.items.filter(filter);
                filtrao.forEach((element) => {
                    if (element.children != null) {
                        element.children = element.children.filter(filter);

                        element.children.forEach((child) => {
                            if (child.children != null) {
                                child.children = child.children.filter(filter);
                            }
                        });
                        console.log(element);
                        if (element.children.length == 1) {
                            element.path = element.children[0].path;
                            element.icon = element.children[0].icon;
                            element.text = element.children[0].text;
                            element.children = element.children[0].children;
                        }
                        console.log(element);
                    }
                });
                return filtrao;
            }
        },
    },
};
