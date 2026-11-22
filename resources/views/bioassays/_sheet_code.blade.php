{{--
    Código, versión y vigencia de la hoja de registro oficial.
    Se muestran los datos guardados con el bioensayo (la hoja con que se registró).
    Si no tiene, se usan los de la hoja vigente en config/bioensayos.php.
    Uso: @include('bioassays._sheet_code', ['bioassay' => $registro])
--}}
@php
    $sheet = $bioassay->template
        ?? (object) collect(config('bioensayos'))->firstWhere('model', get_class($bioassay));
@endphp
<p class="text-center text-muted mb-4">{{ $sheet->code }} | Versión: {{ sprintf('%02d', $sheet->version) }} | Vigencia: {{ $sheet->validity }}</p>
