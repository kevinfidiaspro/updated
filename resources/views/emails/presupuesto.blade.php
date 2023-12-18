@component('mail::message')
    {{ $presupuesto->descripcion }}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
