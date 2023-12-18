<!DOCTYPE html>
<html lang="en" dir="ltr">
@php
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $año = date('Y', strtotime($fecha_inicio));
    $mes = $meses[intval(date('m', strtotime($fecha_inicio))) - 1];

@endphp

<head>
    <meta charset="utf-8">
    <title>Fichaje {{ $mes }} {{ $usuarios->first()['usuario']['nombre'] }}</title>
</head>

<body>
    @foreach ($usuarios as $key => $usuario)
        <div class="header">
            <h2 style="text-align:center">Fichajes {{ $mes }} {{ $año }}</h2>
            <table id="tabla_header">
                <tbody>
                    <tr>
                        <td>Empresa: {{ $empresa ? $empresa->nombre : 'Fidias Technology S.L' }}</td>
                        <td>Trabajador: {{ $usuario['usuario']['nombre'] }}</td>
                    </tr>
                    <tr>
                        <td>C.I.F: {{ $empresa ? $empresa->cif : 'B01807064' }}</td>
                        <td>N.I.F: {{ $usuario['usuario']['cif'] }}</td>
                    </tr>
                    <tr>
                        <td>C.C.C: {{ $empresa ? $empresa->ccc : '' }}</td>
                        <td>N.A.F: {{ $usuario['usuario']['naf'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tabla-container">
            <table>
                <thead>
                    <tr>
                        <th>Día</th>
                        @foreach ($usuario['headers'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="">

                    @foreach ($dates as $date)
                        @php
                            $day = $usuario['body'][$date] ?? null;
                            $hora_str = isset($usuario['horas'][$date]) ? $usuario['horas'][$date] : null;
                        @endphp
                        <tr>
                            <td>{{ $date }}</td>
                            @if (isset($usuario['vacaciones'][$date]))
                                @foreach ($usuario['rango_horas'] as $hora)
                                    <td>V</td>
                                @endforeach
                            @elseif($day != null)
                                @foreach ($usuario['rango_horas'] as $hora)
                                    <td>{{ isset($day[$hora - 1]['hora_fichaje']) ? $day[$hora - 1]['hora_fichaje'] : '' }}
                                    </td>
                                @endforeach
                            @else
                                @foreach ($usuario['rango_horas'] as $hora)
                                    <td></td>
                                @endforeach
                            @endif


                        </tr>
                    @endforeach

                </tbody>

            </table>
            <div></div>
            <div class="footer">
                <p> <span class="firma">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> de <span
                        class="firma">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    de
                    <span
                        class="firma">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                </p>
            </div>

        </div>

        @if (!$loop->last)
            <div class="page_break"></div>
        @endif
    @endforeach

    <style media="screen">
        html,
        body {
            color: #546e7a;
        }

        .footer {
            margin-top: 25px;
        }

        .firma {
            border: 0;
            border-bottom: 1px solid #000;
        }

        .page_break {
            page-break-before: always;
        }

        #tabla_header {
            margin-bottom: 20px;
        }

        #tabla_header tbody tr td {
            border: solid 1px #000;
            width: 49% !important;
            text-align: left;
            padding: 5px !important;
        }

        table {
            border: solid 1px #fff;
            border-collapse: collapse !important;
            width: 100%;
        }

        table tbody tr td {
            padding: 5px;
            text-align: center;
            border: solid 1px #000;
        }

        table tfoot tr td {
            padding: 5px 0;
        }

        .center-align {
            text-align: center;
        }

        .right-align {
            text-align: right !important;
        }

        .left-align {
            text-align: left !important;
        }
    </style>
</body>

</html>
