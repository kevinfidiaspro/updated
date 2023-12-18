<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Recibo</title>
</head>

<body>
    <div class="header">

        <div class="logo">
            <img width="200" height="200"
                src="{{ URL::asset('storage/users/userId_' . $userLog->id . '/' . $userLog->avatar) }}" alt="">
        </div>
        <div class="legal-info">
            <h1>{!! $userLog->nombre_fiscal !!}</h1>
            <p>
                {!! $userLog->direccion !!}<br>
                {!! $userLog->ciudad !!} {!! $userLog->provincia->nombre !!} <br>
                {!! $userLog->nombre !!} CIF: {!! $userLog->cif !!} <br>
                tel. {!! $userLog->telefono !!}
            </p>
            <span>{!! $userLog->email !!}</span>
        </div>
    </div>

    <div class="fecha-emision">
        <p><strong>FECHA DE EMISIÓN:</strong>{{ $recibo->fecha }}</p>
        @if ($tipo == 'factura')
            <p><strong>Nro.Factura:</strong>{{ str_pad($nro_factura, 4, '0', STR_PAD_LEFT) }}</p>
        @else
            <p><strong>Nro.Presupuesto:</strong>{{ str_pad($nro_factura, 4, '0', STR_PAD_LEFT) }}</p>
        @endif
    </div>

    <div class="cliente-info">
        <p><strong>CLIENTE:</strong>{{ $recibo->cliente->nombre }}</p>
        <p><strong>CIF/NIF:</strong>{{ $recibo->cliente->dni }}</p>
        <p><strong>DIRECCIÓN:</strong>{{ $recibo->cliente->direccion . ' ' . $recibo->cliente->codigo_postal . ' ' . $recibo->cliente->localidad . ' ' . $recibo->cliente->provincia['nombre'] }}
        </p>
        <p><strong>TELÉFONO:</strong>{{ $recibo->cliente->telefono }}</p>
    </div>

    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th class="left-align">Descripción</th>
                    <th>cantidad</th>
                    <th>precio</th>
                    <th class="right-align">importe</th>
                </tr>
            </thead>

            <tbody class="{{ $recibo['has_iva'] == true ? 'border-on-t-body' : '' }}">
                @foreach ($recibo->servicios as $servicio)
                    @if (
                        (float) $servicio['cantidad'] === floatval(0) ||
                            (float) $servicio['precio'] === floatval(0) ||
                            (float) $servicio['importe'] === floatval(0))
                        <tr>
                            <td>{{ $servicio['descripcion'] }}</td>
                            <td class="center-align"></td>
                            <td class="center-align"></td>
                            <td class="right-align"></td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $servicio['descripcion'] }}</td>
                            <td class="center-align">{{ $servicio['cantidad'] }}</td>
                            <td class="center-align">{{ $servicio['precio'] }}€ </td>
                            <td class="right-align">{{ $servicio['importe'] }}€</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>

            <tfoot>
                @if ($recibo['has_iva'] == false)
                    <tr>
                        <td class="{{ $recibo['has_iva'] == false ? 'border-on-t-body' : '' }}" colspan="3"
                            style="padding-top:35px;">
                            <p style="text-transform:uppercase; font-size:11px;">
                                el sujeto pasivo de la operación es el destinatario de las operaciones <br>
                                por la aplicación de las reglas de la inversión del sujeto pasivo contenidas <br>
                                en el articulo 84 apartadouno 2ªde la ley 37/92 de iva
                            </p>
                        </td>
                        <td class="{{ $recibo['has_iva'] == false ? 'border-on-t-body' : '' }}" colspan="1"></td>
                    </tr>
                @endif
                <tr>
                    <td style="padding-top:25px !important;" class="right-align" colspan="3">
                        <strong>SUBTOTAL</strong></td>
                    <td style="padding-top:25px !important;" class="right-align">{{ $recibo['sub_total'] }}€</td>
                </tr>

                <tr>
                    <td class="right-align" colspan="3"><strong>IVA 21%</strong></td>
                    <td class="right-align">
                        @if ($recibo['has_iva'] == true)
                            {{ $recibo['iva'] }}€
                        @else
                            0€
                        @endif
                    </td>
                </tr>

                <tr>
                    <td class="right-align" colspan="3"><strong>DESCUENTO</strong></td>
                    <td class="right-align">{{ $recibo['total_descuento'] > 0 ? $recibo['total_descuento'] : 0 }}€</td>
                </tr>
                <tr>
                    <td class="right-align" colspan="3"><strong>TOTAL</strong></td>
                    <td class="right-align">
                        @if ($recibo['has_iva'] == false)
                            {{ $recibo['sub_total'] }}€
                        @endif

                        @if ($recibo['has_iva'] == true)
                            {{ $recibo['total'] }}€
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="right-align" colspan="3"><strong>FORMA DE PAGO:</strong></td>
                    <td class="right-align">
                        {!! $userLog->cuenta !!}
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>

    <style media="screen">
        html,
        body {
            color: #546e7a;
        }

        .header div {
            display: inline-block;
            vertical-align: top;
        }

        .header .logo {
            padding: 10px 0 10px 0;
            width: 45%;

        }

        .header .logo img {
            width: 280px;
        }

        .header .legal-info {
            padding: 10px 0 10px 0;
            text-align: center;
            width: 52%;

        }

        .header .legal-info h1 {
            font-size: 18px;
            color: #448aff;
            text-transform: uppercase;
            margin-top: 0;
        }

        .header .legal-info p {
            margin-bottom: 5px;
            text-transform: capitalize;
        }

        .fecha-emision p strong {
            margin-right: 10px;
        }

        .fecha-emision p {
            margin-bottom: 8px;
            margin-top: 0;
        }

        .fecha-emision {
            border-bottom: solid 3px #448aff;
        }

        .cliente-info p {
            text-transform: uppercase;
            margin-bottom: 6px;
            margin-top: 0px;
        }

        .cliente-info p:first-child {
            margin-top: 6px;
        }

        .cliente-info p strong {
            margin-right: 15px;
            margin-bottom: 3px;
        }

        .tabla-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tabla-container table thead tr th {
            border-bottom: solid 1px #90a4ae;
            text-transform: uppercase;
            padding-bottom: 3px;
            text-align: center;
        }

        .border-on-t-body {
            border-bottom: solid 1px #90a4ae !important;
        }

        .border-on-f-body {
            border-bottom: solid 1px #90a4ae !important;
        }

        .tabla-container table tbody tr td {
            padding: 7px 0;
        }

        .tabla-container table tbody tr td:first-child {
            padding: 10px 0 !important;
        }

        .tabla-container table tfoot tr td {
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
