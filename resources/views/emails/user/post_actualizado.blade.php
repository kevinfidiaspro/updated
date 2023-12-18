@component('mail::message')
    El post para redes sociales ha sido {{ $aceptado ? 'Aceptado' : 'Rechazado' }}

    Nombre: <strong>{{ $user->nombre }}</strong><br>
    Nombre Comercial: <strong>{{ $user->nombre_comercial }}</strong>
    Nombre Fiscal: <strong>{{ $user->nombre_fiscal }}</strong>

    Gracias!!!<br>
@endcomponent
