<tr>
    <td class="header">
        <a href="https://fidiaspro.com" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="{{ URL::asset('https://fidiaspro.com/wp-content/uploads/2022/03/Logo-Fidias-Pro-sin-fondo-Negro-e1646664499686.png') }}"
                    alt="Fidias Pro">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
