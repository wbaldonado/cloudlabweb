@php
    $en = app()->getLocale() === 'en';
    $sources = [
        ['id' => 1, 'y' => 50, 'label' => 'Excel', 'event' => $en ? 'New file' : 'Archivo nuevo'],
        ['id' => 2, 'y' => 130, 'label' => $en ? 'Email' : 'Correos', 'event' => $en ? 'New email' : 'Nuevo correo'],
        ['id' => 3, 'y' => 210, 'label' => $en ? 'PDF invoices' : 'Facturas PDF', 'event' => $en ? 'Invoice received' : 'Factura recibida'],
    ];
    $steps = $en
        ? ['Extract data', 'Validate fields', 'Record', 'Notify']
        : ['Extrae datos', 'Valida campos', 'Registra', 'Notifica'];
    $routes = [
        1 => 'M174 72 C 215 72, 215 152, 250 152',
        2 => 'M174 152 C 205 152, 220 152, 250 152',
        3 => 'M174 232 C 215 232, 215 152, 250 152',
        4 => 'M440 152 L 480 152',
    ];
@endphp
<figure class="fa" data-flow role="img" aria-label="{{ __('messages.landing.flow_label') }}">
    <svg viewBox="0 0 640 340" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        @foreach($routes as $n => $d)
            <path class="fa-linea fa-l{{ $n }}" data-line="{{ $n }}" d="{{ $d }}"></path>
        @endforeach

        @foreach($sources as $source)
            <g class="fa-in fa-f{{ $source['id'] }} fa-fuente" data-source="{{ $source['id'] }}" data-y="{{ $source['y'] }}" data-event="{{ $source['event'] }}">
                <rect class="fa-caja" x="24" y="{{ $source['y'] }}" width="150" height="44" rx="10"></rect>
                <g transform="translate(38 {{ $source['y'] + 13 }})" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    @if($source['id'] === 1)
                        <rect x="1" y="1" width="16" height="16" rx="2.5"></rect>
                        <path d="M1 7h16M1 12h16M7 1v16"></path>
                    @elseif($source['id'] === 2)
                        <rect x="1" y="3" width="16" height="12" rx="2.5"></rect>
                        <path d="m2 5 7 5.5L16 5"></path>
                    @else
                        <path d="M4 1h7l4 4v12H4z"></path>
                        <path d="M11 1v4h4M7 10h5M7 13h5"></path>
                    @endif
                </g>
                <text x="66" y="{{ $source['y'] + 27 }}">{{ $source['label'] }}</text>
            </g>
        @endforeach

        <g class="fa-evento" data-event-banner transform="translate(0 23)">
            <rect x="37" y="0" width="124" height="20" rx="10"></rect>
            <text x="99" y="14" class="fa-centrado"></text>
        </g>

        <g class="fa-in fa-centro">
            <rect class="fa-pulso" x="250" y="70" width="190" height="164" rx="14"></rect>
            <rect class="fa-caja fa-caja-fuerte" x="250" y="70" width="190" height="164" rx="14"></rect>
            <text class="fa-centrado fa-titulo" x="345" y="97">Power Automate</text>
            @foreach($steps as $i => $label)
                @php $y = 126 + $i * 28; @endphp
                <g class="fa-paso" data-step>
                    <circle cx="272" cy="{{ $y }}" r="8.5"></circle>
                    <path d="M268 {{ $y }} l3 3.2 l5.2 -6.4"></path>
                    <text x="290" y="{{ $y + 4.5 }}">{{ $label }}</text>
                </g>
            @endforeach
        </g>

        <g class="fa-in fa-bi">
            <rect class="fa-caja" x="480" y="82" width="136" height="144" rx="14"></rect>
            <text class="fa-centrado fa-titulo" x="548" y="108">Power BI</text>
            @foreach([[502, 146, 30], [530, 132, 44], [558, 118, 58]] as $i => $bar)
                <g>
                    <rect class="fa-barra" x="{{ $bar[0] }}" y="{{ $bar[1] }}" width="20" height="{{ $bar[2] }}" rx="3"></rect>
                    <rect class="fa-barra-rel fa-b{{ $i + 1 }}{{ $i === 2 ? '' : '' }}" @if($i === 2) data-bump @endif x="{{ $bar[0] }}" y="{{ $bar[1] }}" width="20" height="{{ $bar[2] }}" rx="3"></rect>
                </g>
            @endforeach
            <text class="fa-centrado fa-num" x="548" y="204" data-count>128</text>
            <text class="fa-centrado fa-sub" x="548" y="217">{{ $en ? 'records' : 'registros' }}</text>
        </g>

        <g data-tokens></g>

        <g class="fa-badge" data-badge>
            <rect x="170" y="272" width="300" height="52" rx="26"></rect>
            <circle cx="198" cy="298" r="8"></circle>
            <path d="M194 298 l3 3.2 l5.2 -6.4"></path>
            <text x="216" y="294">{{ $en ? 'No manual work · 3 s' : 'Sin intervención manual · 3 s' }}</text>
            <text x="216" y="312" class="fa-badge-sub">{{ $en ? '≈ 5 h less per person each week' : '≈ 5 h menos por persona a la semana' }}</text>
        </g>
    </svg>
</figure>
