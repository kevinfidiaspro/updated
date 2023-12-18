@component('mail::message')
    Se han creado las facturas mensuales correctamente para los siguientes clientes:
    @foreach ($exitosos as $exitoso)
        {{ $exitoso['nombre'] }}<br>
    @endforeach

    @if (count($fallidos) > 0)
        y los siguientes contaron con un error en su creacion:
        @foreach ($fallidos as $exitoso)
            {{ $exitoso['nombre'] }}<br>
        @endforeach
    @endif

@endcomponent
