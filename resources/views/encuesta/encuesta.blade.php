<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Checklist no te conoce ni el tato</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>

<body>
    <div class="wrapper">
        @if (session('mensaje'))
            <script>
                M.toast({
                    html: "{{ session('mensaje') }}",
                    classes: 'red lighten-2'
                })
            </script>
        @endif
        <div class="container">
            <div class="row mb-0">
                <div class="col s12 l6 offset-l3">
                    <div class="card custom-card">

                        <h3 class="texto-lateral">¿Cómo lo hago en el entorno online?</h3>

                        <h4 class="title">
                            no temas a no marcar
                            <br>
                            casillas...
                            <br>
                            ¡aquí estas para mejorar!
                        </h4>
                        <form onsubmit="event.preventDefault();" id="encuesta_form"
                            action="{{ route('submit.encuesta') }}" method="post">
                            @csrf
                            <input id="input_user_email" type="hidden" name="user_email" value="">
                            @foreach ($items as $item)
                                <p>
                                    <label>
                                        <input name="{{ $item['value'] }}" type="checkbox" />
                                        <span class="white-text">
                                            {{ $item['text'] }}
                                        </span>
                                    </label>
                                </p>
                            @endforeach

                            <button id="btn_form" class="btn btn-small red lighten-2">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal_email" class="modal">
                <div class="modal-content">

                    <div class="row">
                        <div class="col s12">
                            <h6>¿A qué correo electrónico quieres que te enviemos los resultados?</h6>

                        </div>
                        <div class="input-field col s10">
                            <input placeholder="Email" id="input_form_email" type="email" required class="validate">
                            <label for="form_email">Email</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer left-align">
                    <div class="row">
                        <div class="col s12">
                            <button class="btn btn-small red lighten-2" id="btn_submit">Enviar Resultados</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elems = document.querySelectorAll('.modal');
            const instances = M.Modal.init(elems, {});

            const email_modal = document.querySelector('#modal_email');
            const instance = M.Modal.getInstance(email_modal);

            const btn_submit = document.getElementById('btn_submit')
            const btn_form = document.getElementById('btn_form')
            const modal = document.getElementById('modal_email')

            const encuesta_form = document.getElementById('encuesta_form')
            const input_form_email = document.getElementById('input_form_email')
            const input_user_email = document.getElementById('input_user_email')

            const validateEmail = email => {
                return String(email)
                    .toLowerCase()
                    .match(
                        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                        )
            }

            btn_form.addEventListener('click', e => {
                instance.open()
            })

            btn_submit.addEventListener('click', e => {
                if (!input_form_email.value) {
                    return M.toast({
                        html: "El campo email es necesario",
                        classes: 'red lighten-2'
                    })
                }
                if (!validateEmail(input_form_email.value)) {
                    return M.toast({
                        html: "Email incorrecto",
                        classes: 'red'
                    })
                }
                input_user_email.value = input_form_email.value
                encuesta_form.submit()
            })
        })
    </script>

    <style media="screen">
        .mb-0 {
            margin-bottom: 0;
        }

        .modal-content {
            padding-bottom: 0 !important;
        }

        .wrapper {
            min-height: 100vh;
            background-image: url("{{ URL::asset('fondo.png') }}");
            background-repeat: repeat;
            background-position: center;
            background-size: cover;
        }

        .custom-card {
            padding: 1.5rem 1rem 1rem 4rem !important;
            background-color: #feb130 !important;
            position: relative;
        }

        .title {
            font-size: 1.1rem !important;
            text-transform: uppercase;
            line-height: 140% !important;
            color: #393939 !important;
            letter-spacing: 1.4px;
            font-weight: 600;
            margin-bottom: 2.5rem;
        }

        [type="checkbox"]+span:not(.lever):before,
        [type="checkbox"]:not(.filled-in)+span:not(.lever):after {
            border: 2px solid #ffffff;
        }

        [type="checkbox"]:checked+span:not(.lever):before {
            border-top: 2px solid transparent;
            border-left: 2px solid transparent;
            border-right: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
        }

        input:not([type]):focus:not([readonly]),
        input[type=text]:not(.browser-default):focus:not([readonly]),
        input[type=password]:not(.browser-default):focus:not([readonly]),
        input[type=email]:not(.browser-default):focus:not([readonly]),
        input[type=url]:not(.browser-default):focus:not([readonly]),
        input[type=time]:not(.browser-default):focus:not([readonly]),
        input[type=date]:not(.browser-default):focus:not([readonly]),
        input[type=datetime]:not(.browser-default):focus:not([readonly]),
        input[type=datetime-local]:not(.browser-default):focus:not([readonly]),
        input[type=tel]:not(.browser-default):focus:not([readonly]),
        input[type=number]:not(.browser-default):focus:not([readonly]),
        input[type=search]:not(.browser-default):focus:not([readonly]),
        textarea.materialize-textarea:focus:not([readonly]) {
            border-bottom: 1px solid #e57373;
            -webkit-box-shadow: 0 1px 0 0 #e57373;
            box-shadow: 0 1px 0 0 #e57373;
        }

        input:not([type]),
        input[type=text]:not(.browser-default),
        input[type=password]:not(.browser-default),
        input[type=email]:not(.browser-default),
        input[type=url]:not(.browser-default),
        input[type=time]:not(.browser-default),
        input[type=date]:not(.browser-default),
        input[type=datetime]:not(.browser-default),
        input[type=datetime-local]:not(.browser-default),
        input[type=tel]:not(.browser-default),
        input[type=number]:not(.browser-default),
        input[type=search]:not(.browser-default),
        textarea.materialize-textarea {
            border-bottom: 1px solid #e57373 !important;
        }

        input:not([type]).validate+label,
        input[type=text]:not(.browser-default).validate+label,
        input[type=password]:not(.browser-default).validate+label,
        input[type=email]:not(.browser-default).validate+label,
        input[type=url]:not(.browser-default).validate+label,
        input[type=time]:not(.browser-default).validate+label,
        input[type=date]:not(.browser-default).validate+label,
        input[type=datetime]:not(.browser-default).validate+label,
        input[type=datetime-local]:not(.browser-default).validate+label,
        input[type=tel]:not(.browser-default).validate+label,
        input[type=number]:not(.browser-default).validate+label,
        input[type=search]:not(.browser-default).validate+label,
        textarea.materialize-textarea.validate+label {
            width: 80%;
            color: #e57373 !important;
        }

        input.valid:not([type]),
        input.valid:not([type]):focus,
        input.valid[type=text]:not(.browser-default),
        input.valid[type=text]:not(.browser-default):focus,
        input.valid[type=password]:not(.browser-default),
        input.valid[type=password]:not(.browser-default):focus,
        input.valid[type=email]:not(.browser-default),
        input.valid[type=email]:not(.browser-default):focus,
        input.valid[type=url]:not(.browser-default),
        input.valid[type=url]:not(.browser-default):focus,
        input.valid[type=time]:not(.browser-default),
        input.valid[type=time]:not(.browser-default):focus,
        input.valid[type=date]:not(.browser-default),
        input.valid[type=date]:not(.browser-default):focus,
        input.valid[type=datetime]:not(.browser-default),
        input.valid[type=datetime]:not(.browser-default):focus,
        input.valid[type=datetime-local]:not(.browser-default),
        input.valid[type=datetime-local]:not(.browser-default):focus,
        input.valid[type=tel]:not(.browser-default),
        input.valid[type=tel]:not(.browser-default):focus,
        input.valid[type=number]:not(.browser-default),
        input.valid[type=number]:not(.browser-default):focus,
        input.valid[type=search]:not(.browser-default),
        input.valid[type=search]:not(.browser-default):focus,
        textarea.materialize-textarea.valid,
        textarea.materialize-textarea.valid:focus,
        .select-wrapper.valid>input.select-dropdown {
            border-bottom: 1px solid #e57373;
            -webkit-box-shadow: 0 1px 0 0 #e57373;
            box-shadow: 0 1px 0 0 #e57373;
        }

        .white-text {
            color: #fff !important;
        }

        .modal {
            width: 35% !important;
        }

        .modal-footer {
            text-align: left !important;
            padding: 0 24px 8px 24px !important;
        }

        .texto-lateral {
            position: absolute;
            transform: rotate(-90deg) translate(-150%, -50%);
            top: 0;
            left: 0;
            font-size: 1.5rem;
            font-weight: 300;
            -webkit-transform-origin: top left;
            -moz-transform-origin: top left;
            -o-transform-origin: top left;
            transform-origin: top left;

        }

        @media (max-width:800px) {
            .modal {
                width: 90% !important;
            }
        }
    </style>

</body>

</html>
