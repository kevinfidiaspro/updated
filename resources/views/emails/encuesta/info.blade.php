@component('mail::message')

<h3>Email: {{$email}}</h3>
<h3>Resultado: {{$t_resultado}}</h3>


@component('mail::table')

| Item | Resultado |
| :--------| ---:|
@foreach($items as $item)
| {{$item['text']}} | {{$item['value']}}
@endforeach


@endcomponent

@endcomponent