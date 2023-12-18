@component('mail::message')
    ¡Atención! El proyecto
    {{ $proyecto->nombre }}

    ha sobrepasado las horas estimadas.

    Minutos estimados {{ $minutos['semanal'] == 1 ? 'esta semana' : ($minutos['semanal'] == 2 ? 'este mes' : '') }}:
    {{ $minutos['asignados'] }}
    @if (isset($minutos['pasado']))
        @if ($minutos['pasado'] != 0)
            Minutos {{ $minutos['semanal'] == 1 ? 'semana pasada' : ($minutos['semanal'] == 2 ? 'mes pasado' : '') }}:
            {{ $minutos['pasado'] }}
        @endif
    @endif
    Minutos utilizados {{ $minutos['semanal'] == 1 ? 'esta semana' : ($minutos['semanal'] == 2 ? 'este mes' : '') }}:
    {{ $minutos['tiempo'] }}
@endcomponent
