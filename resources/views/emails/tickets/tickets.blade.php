@component('mail::message')
    @if ($string == 'create')
        Se ha creado un nuevo ticket

        Datos: <br>

        Nombre del cliente: <strong>{{ $ticket->user->nombre }}</strong><br>
        N°. Ticket: <strong>{{ $ticket->id }}</strong><br>
        Departamento: <strong>{{ $ticket->departamento->descripcion }}</strong><br>
        Urgencia: <strong>{{ $ticket->urgencia->descripcion }}</strong><br>
        Descripción: <strong>{{ $ticket->descripcion }}</strong>


        Gracias!!!<br>
    @else
        Hola {{ $ticket->user->nombre }}, tu ticket está siendo gestionado por nuestro equipo, la fecha prevista de
        finalización es el {{ $ticket->fecha_finalizacion }}.

        Gracias

        Un saludo
    @endif



    {{ config('app.name') }}
@endcomponent
