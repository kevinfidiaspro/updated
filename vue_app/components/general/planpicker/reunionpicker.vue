<template>
    <div class="contnedor_calendario">
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
                        <template v-for="(elemento, index) in value">
                            <router-link
                                v-bind:key="elemento.id"
                                v-if="
                                    elemento.fecha == dia &&
                                    elemento.hora_desde <= hora &&
                                    elemento.hora_hasta >= hora
                                "
                                :to="{
                                    path: `/guardar-reuniones?id=${elemento.id}`,
                                }"
                                class="action-buttons"
                            >
                                <div
                                    :class="`dias__visita ${
                                        checkIfUser(elemento)
                                            ? 'green-background'
                                            : ''
                                    }`"
                                    :key="index"
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
                                            <span v-if="elemento.organizador">{{
                                                elemento.organizador.nombre
                                            }}</span>
                                        </div>
                                    </div>

                                    <div class="cut-text">
                                        {{ elemento.comentario }}
                                    </div>
                                </div>
                                <div></div>
                            </router-link>
                        </template>
                    </div>
                </template>
            </template>
        </div>
    </div>
</template>

<style>
.cut-text {
    text-overflow: ellipsis;
    overflow: hidden;
    width: 100%;
    max-height: 8rem;
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
.green-background {
    background-color: #4caf50 !important;
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
    methods: {
        checkIfUser(item) {
            return item.asistentes.find(
                (element) => element.id_asistente == this.user_id
            );
        },
        getColor(item) {
            if (item.proyecto) {
                if (item.proyecto.estado_id == 2) {
                    return "#343434";
                } else {
                    if (item.proyecto.activo == 0) {
                        return "#fb8c00";
                    } else {
                        return "#4caf50";
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
        },
    },
    computed: {
        user_id() {
            return localStorage.getItem("user_id");
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
        };
    },
};
</script>
