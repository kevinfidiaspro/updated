<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Presupuesto</title>
    <style>
        * body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        /*div table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }*/
    </style>
</head>

<body>
    @php
    $subtotal = 0;
    @endphp
    <table>
        <tr>
            <td>
                <div>
                    <img src="data:image/png;base64,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" />
                </div>
            </td>
            <td>
                <div style="border:1px solid grey; background-color:#f1eae280; ">
                    <table>
                        <tr>
                            <td>
                                <span style="font-size:45px;font-weight:200;margin-left: 15px">Presupuesto</span><br>
                                <span style="color:grey;margin-left: 15px">Número:</span> {{date('y')}}-{{$report_pdf['nro_presupuesto']}}<br>
                                <span style="color:grey;margin-left: 15px">Fecha: </span>{{ date('d-m-Y', strtotime($report_pdf['fecha'])) }}
                            </td>

                            <td style="text-align: center;">
                                <br>
                                <span style="font-weight:200;">Fidias Technology S.L.</span><br>
                                <span style="color:grey;">C/Donadores 64 local 4.</span><br>
                                <span style="color:grey;">03160 Almoradí</span><br>
                                <span style="color:grey;">B01807064</span><br>
                                <span style="color:grey;">info@fidiaspro.com</span><br>
                                <span style="color:grey;">Telf: 722 81 12 81</span><br>
                            </td>

                            <td style="text-align: left;">
                                <img src="https://fidiaspro.com/wp-content/uploads/2022/03/Logo-Fidias-Pro-sin-fondo-Negro-e1646664499686.png" height="80" width="240">
                            </td>
                        </tr>
                    </table>
                    <table style="border:1px solid grey;width:95%;margin: 0 auto;border-collapse: collapse;background-color:white;">
                        <tr>
                            <td style="width:20px;">
                                <span style="color:grey; margin-left:10px;">Cliente:</span>
                            </td>
                            <td style="text-transform: uppercase;">
                                {{$proyecto['usuario']['nombre_fiscal']}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width:20px;">
                                <span style="color:grey; margin-left:10px;">CIF/DNI:</span>
                            </td>
                            <td style="text-transform: uppercase;">
                                {{$proyecto['usuario']['cif']}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width:20px;">
                                <span style="color:grey; margin-left:10px;">Domicilio:</span>
                            </td>
                            <td style="text-transform: uppercase;">
                                {{$proyecto['usuario']['direccion']}} -- C.P.
                                {{$proyecto['usuario']['codigo_postal']}}
                                {{$proyecto['usuario']['localidad']}}
                                {{$proyecto['usuario']['provincia']['nombre']}}
                            </td>
                        </tr>
                    </table>
                    </br>
                    <table style="border:1px solid grey;width:95%;margin: 0 auto;background-color:white;">
                        <tr>
                            <td>
                                <span style="color:grey; margin-left:10px;">Descripción-notas:</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="margin-left:15px;">
                            </td>
                        </tr>
                    </table>
                    </br>
                    <div>
                        <table style="border:1px solid grey;width:95%;margin: 0 auto;background-color:white;">
                            <tr>
                                <th>DESCRIPCIÓN</th>
                                <th style="text-align: center;">CANTIDAD</th>
                                <th style="text-align: center;">PRECIO</th>
                                <th style="text-align: right;">IMPORTE</th>
                            </tr>
                            @foreach($report_pdf['items_presupuesto'] as $item)
                            <tr>
                                <td>{{ $item['descripcion'] }}</td>
                                <td style="text-align: center;">{{ number_format( ($item['cantidad']), 0, ',', '.') }}</td>
                                <td style="text-align: center;">{{ number_format( ($item['precio']), 2, ',', '.') }} €</td>
                                <td style="text-align: right;">{{ number_format( ($item['importe']), 2, ',', '.') }} €</td>
                                @php $subtotal = $subtotal + $item['importe'] @endphp
                            </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">
                                    <div style="margin-top:6px">

                                        Subtotal:
                                    </div>
                                    @if($report_pdf['descuento'] > 0)
                                    <div style="margin-top:6px">
                                        Descuento {{$report_pdf['descuento']}} %:
                                    </div>
                                    @endif
                                    <div style="margin-top:6px">
                                        Base Imponible:
                                    </div>
                                    <div style="margin-top:6px">
                                        I.V.A. 21%:
                                    </div>
                                    <div style="margin-top:6px">
                                        <b>Total Presupuesto:</b>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <div style="margin-top:6px">
                                        {{ number_format( $subtotal, 2, ',', '.') }} €
                                    </div>
                                    @if($report_pdf['descuento'] > 0)
                                    <div style="margin-top:6px">
                                        - {{ number_format( (($subtotal)*($report_pdf['descuento'])/100), 2, ',', '.')  }} €
                                    </div>
                                    @endif
                                    <div style="margin-top:6px">
                                        {{ number_format( ($subtotal)-((($subtotal)*($report_pdf['descuento'])/100)), 2, ',', '.') }} €
                                    </div>
                                    <div style="margin-top:6px">
                                        {{ number_format(($report_pdf['status_iva']), 2, ',', '.') != 0 ? number_format((($subtotal - ($subtotal * $report_pdf['descuento']/100)) * 0.21), 2, ',', '.') : '0.00' }} €
                                    </div>
                                    <div style="margin-top:6px;">
                                        <b> {{ number_format( $report_pdf['total'], 2, ',', '.')}} € </b>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-top:30px;">
                        <table style="border:1px solid grey;width:95%;margin: 0 auto;background-color:white;">
                            <tr>
                                <td>
                                    <span style="color:grey;margin-left:10px;">Forma de Pago:</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    Pago mediante trasferencia bancaria
                                    <br>
                                    <span style="font-weight:200;">Banco: </span>{{$report_pdf['nombre_banco']}} · <span style="font-weight:200;">CUENTA: </span> {{$report_pdf['cuenta']}}
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-top:10px;margin-bottom:10px;">
                        <table style="border:1px solid grey;width:95%;margin: 0 auto;background-color:white;">
                            <tr>
                                <td style="text-align: justify;">
                                    <span style="color:grey;margin-left:10px;font-size:10px;">De conformidad con el Reglamento Europeo de Protección de Datos (UE) 679/2016, le comunicamos que los datos objeto de este
                                        tratamiento en la presente presupuesto son responsabilidad de Fidias Texhnology S.L. Le informamos que los datos que nos facilita se precisan
                                        para prestarle el servicio solicitado y realizar la facturación de este. Los datos se conservarán mientras se mantenga la relación comercial o
                                        durante los años necesarios para cumplir con las obligaciones legales. Los datos no se cederán a terceros salvo en los casos en que exista una
                                        obligación legal. Usted puede ejercer el derecho de acceso, rectificación, cancelación, supresión y portabilidad, dirigiéndose por escrito a C/Donadores 64 local 4, 19, BAJO D. Almoradí (03160) Alicante.</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>


    </table>

</body>

</html>