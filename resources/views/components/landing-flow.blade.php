@php
    $en = app()->getLocale() === 'en';
    $sources = [
        ['id' => 1, 'y' => 50, 'label' => $en ? 'Data' : 'Datos', 'event' => $en ? 'New data' : 'Dato nuevo'],
        ['id' => 2, 'y' => 130, 'label' => $en ? 'Documents' : 'Documentos', 'event' => $en ? 'Document' : 'Documento'],
        ['id' => 3, 'y' => 210, 'label' => $en ? 'Question' : 'Consulta', 'event' => $en ? 'New question' : 'Nueva pregunta'],
    ];
    $steps = $en
        ? ['Understand', 'Reason', 'Decide', 'Act']
        : ['Entiende', 'Razona', 'Decide', 'Actúa'];
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
                        <circle cx="4" cy="9" r="2.2"></circle>
                        <circle cx="14" cy="4" r="2.2"></circle>
                        <circle cx="14" cy="14" r="2.2"></circle>
                        <path d="M6 8.2 L12 5.2M6 10 L12 13"></path>
                    @elseif($source['id'] === 2)
                        <path d="M4 1h7l4 4v12H4z"></path>
                        <path d="M11 1v4h4M7 9h5M7 12h4"></path>
                    @else
                        <path d="M2 3h12a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H8l-4 3v-3H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
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
            <g transform="translate(286 86)" fill="none" stroke="#22d3ee" stroke-width="1.5" stroke-linejoin="round">
                <path d="M8 0.5l1.1 3.2h3.4L9.8 5.7l1 3.2L8 7l-2.8 1.9 1-3.2L3.5 3.7h3.4z"></path>
            </g>
            <text class="fa-centrado fa-titulo" x="358" y="97">{{ $en ? 'AI agent' : 'Agente de IA' }}</text>
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
            <text class="fa-centrado fa-titulo" x="548" y="108">{{ $en ? 'Prediction' : 'Predicción' }}</text>
            @foreach([[502, 146, 30], [530, 132, 44], [558, 118, 58]] as $i => $bar)
                <g>
                    <rect class="fa-barra" x="{{ $bar[0] }}" y="{{ $bar[1] }}" width="20" height="{{ $bar[2] }}" rx="3"></rect>
                    <rect class="fa-barra-rel fa-b{{ $i + 1 }}" @if($i === 2) data-bump @endif x="{{ $bar[0] }}" y="{{ $bar[1] }}" width="20" height="{{ $bar[2] }}" rx="3"></rect>
                </g>
            @endforeach
            <text class="fa-centrado fa-num" x="548" y="204" data-count>128</text>
            <text class="fa-centrado fa-sub" x="548" y="217">{{ $en ? 'answers' : 'respuestas' }}</text>
        </g>

        <g data-tokens></g>

        <g class="fa-badge" data-badge>
            <rect x="170" y="272" width="300" height="52" rx="26"></rect>
            <circle cx="198" cy="298" r="8"></circle>
            <path d="M194 298 l3 3.2 l5.2 -6.4"></path>
            <text x="216" y="294">{{ $en ? 'Autonomous decision · 3 s' : 'Decisión autónoma · 3 s' }}</text>
            <text x="216" y="312" class="fa-badge-sub">{{ $en ? 'No manual work' : 'Sin intervención manual' }}</text>
        </g>
    </svg>
</figure>
