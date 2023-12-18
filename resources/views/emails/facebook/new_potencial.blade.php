@component('mail::message')
    Ha entrado un nuevo cliente potencial, por un lead de facebook
    su correo electrónico {{ $cliente->email }}

    Datos:
    Formulario: <strong>{{ $form->name }}</strong><br>
    Usuario: <strong>{{ $cliente->email }}</strong><br>
    Nombre: <strong>{{ $cliente->name }}</strong>


    Gracias!!!<br>
@endcomponent
