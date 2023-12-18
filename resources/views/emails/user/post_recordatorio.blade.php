@component('mail::message')

    Estimado {{ $user->nombre_comercial }},

    Espero que te encuentres bien.

    Me pongo en contacto contigo para informarte que dentro de nuestra plataforma hay uno o varios post(s) de redes sociales
    pendientes de revisión por tu parte. Es esencial que revises y apruebes (o realices los cambios necesarios) estos
    post(s) para garantizar que su contenido sea el adecuado y se publiquen en tiempo y forma.

    @component('mail::button', ['url' => url('#/social-cliente?token=' . $user->token_redes)])
        Ver
    @endcomponent

    Gracias!!!<br>

@endcomponent
