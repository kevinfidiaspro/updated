<template>
    <div class="contnedor_calendario">
        <div class="container-wrap">
            <div
                class="color-container"
                :style="` ${
                    filters.p2 == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.p2 = !filters.p2"
            >
                <div class="circle" style="background-color: #7eaa92"></div>
                <div>Llamada P2</div>
            </div>
            <div
                class="color-container"
                :style="` ${
                    filters.p5 == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.p5 = !filters.p5"
            >
                <div class="circle" style="background-color: #900c3f"></div>
                <div>Llamada P5</div>
            </div>
            <div
                class="color-container"
                @click="filters.falso = !filters.falso"
                :style="` ${
                    filters.falso == false ? 'opacity:0.5 !important' : ''
                }`"
            >
                <div class="circle" style="background-color: #f44336"></div>
                <div>Falso, Infiel, No Contesta,No Interesado</div>
            </div>
            <div
                class="color-container"
                :style="` ${
                    filters.gestionado == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.gestionado = !filters.gestionado"
            >
                <div class="circle" style="background-color: #ff6600"></div>
                <div>Gestionado</div>
            </div>
            <div
                class="color-container"
                :style="` ${
                    filters.potencial == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.potencial = !filters.potencial"
            >
                <div class="circle" style="background-color: #fb8c00"></div>
                <div>Potencial</div>
            </div>
            <div
                class="color-container"
                :style="` ${
                    filters.resolucion == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.resolucion = !filters.resolucion"
            >
                <div class="circle" style="background-color: #4caf50"></div>
                <div>Esperando Resolución Kit</div>
            </div>
            <div
                class="color-container"
                :style="` ${
                    filters.aprobado == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.aprobado = !filters.aprobado"
            >
                <div class="circle" style="background-color: #0b0069"></div>
                <div>Kit Digital Aprobado</div>
            </div>
            <div
                class="color-container"
                :style="` ${
                    filters.otros == false ? 'opacity:0.5 !important' : ''
                }`"
                @click="filters.otros = !filters.otros"
            >
                <div class="circle" style="background-color: #343434"></div>
                <div>Otros</div>
            </div>
        </div>
        <div class="picker-header">
            <button type="button" @click="previousWeek()">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="picker-header-arrow"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>

            <h2>
                {{ calculateMes() }}
            </h2>
            <button type="button" @click="nextWeek()">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="picker-header-arrow"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>
        </div>

        <div class="calendario">
            <div style="border-bottom: 1px solid rgb(216, 216, 216)"></div>
            <div class="dias" v-for="(dia, index) in semana" :key="index">
                <div
                    class="titulo"
                    style="border-bottom: 1px solid rgb(216, 216, 216)"
                >
                    <div class="dia">
                        {{ dias[index] }}
                    </div>
                    <div class="dia amountButton-0">
                        {{ dia.split("-")[2] }}
                    </div>
                </div>
            </div>
            <template v-for="(hora, index) in horas">
                <div class="time date-container">
                    {{ hora | hora_formated }}
                </div>
                <template v-for="(dia, index) in semana">
                    <div class="cita date-container">
                        <template v-for="(elemento, index) in items">
                            <router-link
                                v-if="
                                    elemento.fecha == dia &&
                                    elemento.hora == hora &&
                                    elemento.proyecto != null
                                "
                                :to="{
                                    path: `/guardar-potencial?id=${
                                        elemento.id_proyecto
                                    }&${
                                        elemento.potencial
                                            ? 'seguimiento'
                                            : 'seguimiento_cliente'
                                    }=1&fecha=${elemento.fecha}`,
                                }"
                                class="action-buttons"
                            >
                                <div
                                    class="dias__visita"
                                    :style="` background-color:${getColor(
                                        elemento
                                    )}; `"
                                    :key="index"
                                    @click=""
                                >
                                    <div
                                        style="
                                            border-bottom: 1px solid
                                                rgb(216, 216, 216);
                                            width: 100%;
                                            text-align: center;
                                            padding-bottom: 0.25rem;
                                        "
                                    >
                                        <div
                                            style="
                                                display: flex;
                                                align-items: center;
                                                justify-content: space-evenly;
                                            "
                                        >
                                            <span>{{
                                                elemento.proyecto.usuario.nombre
                                            }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        {{ elemento.comentario }}
                                    </div>
                                </div>
                            </router-link>
                        </template>
                    </div>
                </template>
            </template>
        </div>
    </div>
</template>

<style>
.container-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    cursor: pointer;
    opacity: 1;
}
.color-container:hover {
    opacity: 0.5;
}

.color-container {
    display: flex;
    justify-content: start;
    align-items: center;
    transition: opacity 0.3s;
}
.circle {
    border-radius: 1000000px;
    width: 1rem;
    height: 1rem;
    margin: 1rem;
}
.date-container {
    border-bottom: 1px solid rgb(216, 216, 216);
}
.time {
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    min-height: 2rem;
}
.warning-icon {
    width: 1.5rem;
}
.amountButton-0 {
    min-width: none;
    display: flex;

    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    background-color: #b4b4b4;
}
.dia {
    font-weight: bold;
    font-family: "Roboto", sans-serif !important;

    text-align: center;
    margin: 0.75rem;

    padding: 0.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.picker-header-arrow {
    color: gray;
    width: 2rem;
}
.picker-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    padding: 1rem;
}
.contnedor_calendario {
    border-radius: 0.5rem;
    margin-top: 1rem;
    border: 1px solid #dddddd;
}
.cita {
    display: flex;
    flex-direction: column;
}
.calendario {
    overflow-x: auto;
    width: 100%;
    display: grid;
    grid-template-columns: 9% repeat(7, 13% [col-start]);
}

.header {
    display: flex;
    justify-content: center;
    padding: 0.5rem;
}

.mes__titulo {
    text-align: center;
    padding: 0.5rem;
    color: #868ba1;
}
.titulo {
    display: flex;
    flex-direction: column;
}
.dias {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.dias__titulos,
.dias__fecha {
    padding: 0.5rem;
    background-color: #0866c6;
    color: white;
    text-align: center;
}

.dias__visita {
    flex-direction: column;
    min-width: none;
    display: flex;
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
    padding: 0.25rem;
    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    background-color: #343434;
    color: white;
    min-width: 85%;
    min-height: 2rem;
}
</style>

<script>
export default {
    props: ["value"],

    created() {
        if (this.$route.query.fecha != null) {
            this.currentDate = new Date(this.$route.query.fecha);
        }

        this.getWeek();
        this.generateHoras();
    },

    watch: {
        value: function (val) {
            console.log("aja");
            console.log(val);
        },
    },
    filters: {
        hora_formated(val) {
            let str = val.toString().split(".");
            if (str.length > 1) {
                return str[0].toString().padStart(2, "0") + ":30";
            }
            return val.toString().padStart(2, "0") + ":00";
        },
    },
    computed: {
        items() {
            return this.value.filter((item) => {
                if (item.proyecto) {
                    if (item.proyecto.id_estado_potencial == 11) {
                        return this.filters.p2;
                    } else if (item.proyecto.id_estado_potencial == 12) {
                        return this.filters.p5;
                    } else if (
                        [4, 7, 8, 13].includes(
                            item.proyecto.id_estado_potencial
                        )
                    ) {
                        return this.filters.falso;
                    } else if (item.proyecto.id_estado_potencial == 3) {
                        return this.filters.gestionado;
                    } else if (item.proyecto.id_estado_potencial == 20) {
                        return this.filters.resolucion;
                    } else if (item.proyecto.id_estado_potencial == 21) {
                        return this.filters.aprobado;
                    } else {
                        if (item.proyecto.usuario.role == 4) {
                            return this.filters.potencial;
                        } else {
                            return this.filters.otros;
                        }
                    }
                }
                return true;
            });
        },
    },
    methods: {
        getColor(item) {
            //343434 negro
            //verde 4caf50
            //fb8c00 naranja
            if (item.proyecto) {
                if (item.proyecto.id_estado_potencial == 11) {
                    return `#7EAA92 `;
                } else if (item.proyecto.id_estado_potencial == 12) {
                    return "#900C3F";
                } else if (
                    [4, 7, 8, 13].includes(item.proyecto.id_estado_potencial)
                ) {
                    return "#F44336";
                } else if (item.proyecto.id_estado_potencial == 3) {
                    return "#ff6600";
                } else if (item.proyecto.id_estado_potencial == 20) {
                    return "#4caf50";
                } else if (item.proyecto.id_estado_potencial == 21) {
                    return "#0b0069";
                } else {
                    if (item.proyecto.usuario.role == 4) {
                        return "#fb8c00";
                    } else {
                        return "#343434";
                    }
                }
            }
            return "#343434";
        },
        generateHoras() {
            for (let i = 9; i <= 19; i += 0.5) {
                this.horas.push(i);
            }
        },

        calculateMes() {
            return this.meses[
                parseInt((this.semana[0] ?? "0-1-0").split("-")[1]) - 1
            ];
        },
        //BOTONES DE MOVER SEMANA
        nextWeek() {
            this.currentDate.setDate(this.currentDate.getDate() + 7);
            this.getWeek();
        },

        previousWeek() {
            this.currentDate.setDate(this.currentDate.getDate() - 7);
            this.getWeek();
        },

        //OBTENER SEMANA ACTUAL
        getWeek() {
            var week = new Array();
            let currentDate = new Date(this.currentDate);
            // Starting Monday not Sunday

            currentDate.setDate(
                currentDate.getDate() - currentDate.getDay() + 1
            );

            for (var i = 0; i < 7; i++) {
                week.push(new Date(currentDate).toISOString().split("T")[0]);
                currentDate.setDate(currentDate.getDate() + 1);
            }

            console.log(new Date(this.currentDate).toISOString());
            this.semana = week;
            console.log(week);
            this.$emit("CambioFecha", week);
        },
    },

    data() {
        return {
            horas: [],
            meses: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
            ],
            semana: [],
            dias: ["Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            currentDate: new Date(),
            mes: "",
            filters: {
                p2: true,
                p5: true,
                falso: true,
                gestionado: true,
                pdte: true,
                potencial: true,
                otros: true,
                resolucion: true,
                aprobado: true,
            },
        };
    },
};
</script>
