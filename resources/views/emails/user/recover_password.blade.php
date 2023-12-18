@component('mail::message')
    # Contraseña Reestablecida

    Su contraseña ha sido cambiada

    Contraseña: <strong>{{ $password }}</strong>

    @component('mail::button', ['url' => Request::root()])
        Ingresar
    @endcomponent

    Gracias!!!
    <br>

    {{ config('app.name') }}
@endcomponent
