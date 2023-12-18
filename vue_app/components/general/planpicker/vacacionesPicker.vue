<template>
    <div>
        <h3>Dias Disponibles: {{ vacaciones_dias }}</h3>

        <v-card>
            <v-card-title> Normas </v-card-title>
            <v-card-text>
                <div>1. Se disfrutan vacaciones en periodos de 7 días</div>
                <div>2. Las semanas de vacaciones se empiezan LUNES</div>
                <div>
                    3. No puedes coincidir con nadie con tus mismas funciones
                </div>
                <div>4. No se pueden disfrutar más de 2 semanas seguidas.</div>
                <div>
                    5. Se recomienda disfrutar dos semanas en los meses de Julio
                    y Agosto.
                </div>
            </v-card-text>
        </v-card>
        <v-card>
            <v-card-title>Usuarios</v-card-title>
            <v-card-text>
                <div class="container-wrap">
                    <div
                        class="color-container"
                        v-for="hora in horas"
                        @click="changeHora(hora)"
                    >
                        <div
                            class="circle"
                            :style="`
                            ${
                                hora.show == false
                                    ? 'opacity:0.5 !important'
                                    : ''
                            }
                            ;background-color:${hora.color ?? '#000000'}`"
                        ></div>
                        <div>{{ hora.nombre }}</div>
                    </div>
                </div>
            </v-card-text>
        </v-card>
        <div class="contnedor_calendario">
            <div class="picker-header">
                <button type="button" @click="previousMonth()">
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
                    {{ currentYear }}
                </h2>
                <button type="button" @click="nextMonth()">
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
            <v-row>
                <v-col cols="12" md="6" lg="4" v-for="mes_data in meses_data">
                    <div
                        style="
                            display: flex;
                            justify-content: center;
                            flex-direction: column;
                        "
                    >
                        <h2 style="text-align: center">
                            {{ mes_data.month }}
                        </h2>
                        <div
                            class="calendario"
                            :style="`grid-template-columns: repeat(7, 14.28% [col-start]);`"
                        >
                            <div class="day_label">Lu</div>
                            <div class="day_label">Ma</div>
                            <div class="day_label">Mi</div>
                            <div class="day_label">Ju</div>
                            <div class="day_label">Vi</div>
                            <div class="day_label">Sa</div>
                            <div class="day_label">Do</div>
                            <div
                                v-for="dia in mes_data.days"
                                class="day_number"
                            >
                                <template v-if="dia.day != null">
                                    <div>{{ dia.day }}</div>
                                    <template v-if="value[dia.date] != null">
                                        <template
                                            v-for="color_user in value[
                                                dia.date
                                            ]"
                                        >
                                            <div
                                                v-if="
                                                    usershow[
                                                        color_user.id_usuario
                                                    ]
                                                "
                                                :style="`background-color: ${color_user.color};`"
                                                class="color-dia"
                                            >
                                                <span class="tooltiptext">{{
                                                    color_user.nombre
                                                }}</span>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </div>
    </div>
</template>

<style scoped>
.calendario {
    overflow: visible;
    display: grid;
}
.color-dia {
    position: relative;
    display: inline-block;
    cursor: pointer; /* Change cursor to pointer on mobile */
}

.color-dia .tooltiptext {
    position: absolute;
    visibility: hidden;
    background-color: #555;
    color: white;
    text-align: center;
    border-radius: 6px;
    padding: 5px 10px;
    z-index: 10;
    top: 100%;
    left: 50%;
    transform: translate(-50%, -110%); /* Position tooltip above the element */
}

.color-dia:hover .tooltiptext {
    visibility: visible;
}

@media (max-width: 767px) {
    /* For mobile devices */
    .color-dia .tooltiptext {
        top: 15px;
        left: 0;
        transform: translateX(-50%);
    }

    .color-dia {
        cursor: default; /* Change cursor back to default on mobile */
    }

    .color-dia:hover .tooltiptext {
        visibility: visible;
    }

    .color-dia:active .tooltiptext {
        visibility: visible; /* Make tooltip visible on tap */
    }
}
.color-dia {
    width: 80%;

    height: 5px;
    border-radius: 5px;
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
    border-radius: 100000;
    background-color: #b4b4b4;
}
.day_label {
    font-weight: bold;
    font-family: "Roboto", sans-serif !important;
    text-align: center;
}
.day_number {
    font-family: "Roboto", sans-serif !important;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
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
.dias__visita:hover {
    opacity: 0.75;
}
</style>

<script>
export default {
    props: ["value"],

    created() {
        const date = new Date();
        this.year = date.getFullYear();
        //this.getDaysOfMonth();
        this.generateHoras();
        this.getMonthsArray();
        this.getVacaciones();
    },

    watch: {
        value: function (val) {},
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
        usershow() {
            let result = {};
            this.horas.forEach((element) => {
                result[element.id] = element.show;
            });
            console.log(result);
            return result;
        },
    },
    methods: {
        changeHora(hora) {
            hora.show = !hora.show;
            const horas = JSON.parse(JSON.stringify(this.horas));
            this.horas = [];
            this.horas = horas;
        },
        getMonthsArray() {
            const year = this.year;
            const months = [
                { name: "Enero", number: 0 },
                { name: "Febrero", number: 1 },
                { name: "Marzo", number: 2 },
                { name: "Abril", number: 3 },
                { name: "Mayo", number: 4 },
                { name: "Junio", number: 5 },
                { name: "Julio", number: 6 },
                { name: "Agosto", number: 7 },
                { name: "Septiembre", number: 8 },
                { name: "Octubre", number: 9 },
                { name: "Noviembre", number: 10 },
                { name: "Diciembre", number: 11 },
            ];
            const dayOfWeekStr = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"];

            this.meses_data = months.map((month) => {
                const daysInMonth = new Date(
                    year,
                    month.number + 1,
                    0
                ).getDate();
                let daysArray = [];
                // Calculate the first day of the month
                const firstDay = new Date(year, month.number, 1).getDay();
                // Add empty JSON objects for days before the first day of the month
                for (let i = 0; i < firstDay; i++) {
                    daysArray.push({});
                }
                // Add days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month.number, day);
                    const dayOfWeek = dayOfWeekStr[date.getDay()];
                    daysArray.push({
                        str: dayOfWeek,
                        day: day,
                        date: date.toISOString().split("T")[0],
                    });
                }
                return { month: month.name, days: daysArray };
            });
            console.log(this.meses_data);
        },
        getColor(item) {
            if (item.color != null) return item.color;
            //343434 negro
            //verde 4caf50
            //fb8c00 naranja

            return "#343434";
        },
        generateHoras() {
            const me = this;
            axios.post("api/get-usuarios-empleados-all").then((res) => {
                me.horas = res.data.users;
                me.horas.forEach((user) => {
                    user.show = true;
                });
                console.log(me.horas);
            });
        },
        calculateMes() {
            return this.meses[this.currentDate.getMonth() - 1];
        },
        //BOTONES DE MOVER SEMANA
        nextMonth() {
            this.currentYear++;
            this.getVacaciones();
        },

        previousMonth() {
            this.currentYear--;
            this.getVacaciones();
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
            this.$emit("getWeek", this.semana);
        },
        getVacaciones() {
            this.$emit("getWeek", this.currentYear);
        },
        getDaysOfMonth() {
            let days = [];

            let year = this.currentDate.getFullYear();
            let month = this.currentDate.getMonth();
            let dayDate = new Date(year, month, 1);
            const dayStrings = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"];

            while (dayDate.getMonth() === month) {
                days.push({
                    date: dayDate.toISOString().split("T")[0],
                    day: ("0" + dayDate.getDate()).slice(-2),
                    str: dayStrings[dayDate.getDay()],
                });
                dayDate.setDate(dayDate.getDate() + 1);
            }
            this.$emit("getWeek", days);

            this.dias = days;
            console.log(this.dias);
        },
    },

    data() {
        return {
            meses_data: [],
            vacaciones_dias: 0,
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
            dias: [],
            currentYear: 2023,
            mes: "",
        };
    },
};
</script>
