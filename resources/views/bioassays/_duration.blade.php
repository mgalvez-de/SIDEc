{{--
    Duración del ensayo (reemplaza al temporizador).
    Recibe:
      $check  -> una etapa de $bioensayo->durationChecks() (etiqueta, estado, minutos transcurridos y exigidos)
      $start  -> name del campo de fecha y hora de inicio
      $end    -> name del campo de fecha y hora de término
    Se muestra calculado al abrir la página y se recalcula en el navegador cuando cambian las fechas.
--}}
@php
    $colores = ['ok' => 'success', 'short' => 'danger', 'long' => 'danger', 'incomplete' => 'secondary'];
@endphp

<div class="alert alert-{{ $colores[$check['status']] }} d-flex flex-wrap align-items-center gap-2 py-2 my-3 duration-check"
    data-start="{{ $start }}" data-end="{{ $end }}" data-minutes="{{ $check['expected'] }}">
    <i class="bi bi-clock"></i>
    <strong>{{ $check['label'] }}:</strong>
    <span class="duration-text">{{ \App\Support\TestDuration::describe($check) }}</span>
</div>

@once
    @push('scripts')
        <script>
            // Mismo cálculo que App\Support\TestDuration, pero en el navegador, para avisar antes de guardar
            (function () {
                const TOLERANCIA = {{ \App\Support\TestDuration::TOLERANCE_MINUTES }};

                function leerFecha(valor, inicio) {
                    valor = (valor || '').trim();
                    let m = valor.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{1,2}):(\d{2})/);
                    if (m) {
                        return new Date(+m[1], m[2] - 1, +m[3], +m[4], +m[5]);
                    }
                    // Solo hora (HH:MM): mismo día del inicio, o el siguiente si es más temprano
                    m = valor.match(/^(\d{1,2}):(\d{2})$/);
                    if (m && inicio) {
                        const fin = new Date(inicio);
                        fin.setHours(+m[1], +m[2], 0, 0);
                        if (fin < inicio) fin.setDate(fin.getDate() + 1);
                        return fin;
                    }
                    return null;
                }

                function formato(minutos) {
                    const signo = minutos < 0 ? '-' : '';
                    minutos = Math.abs(minutos);
                    const dias = Math.floor(minutos / 1440);
                    if (dias >= 7) {
                        return signo + dias + ' d ' + Math.floor((minutos % 1440) / 60) + ' h';
                    }
                    const horas = Math.floor(minutos / 60);
                    return signo + (horas > 0 ? horas + ' h ' : '') + (minutos % 60) + ' min';
                }

                function actualizar(caja) {
                    const zona = caja.closest('form') || document;
                    const inicio = leerFecha(zona.querySelector(`[name="${caja.dataset.start}"]`)?.value);
                    const fin = leerFecha(zona.querySelector(`[name="${caja.dataset.end}"]`)?.value, inicio);
                    const esperado = parseInt(caja.dataset.minutes, 10);
                    const exigido = formato(esperado) + ' ± ' + TOLERANCIA + ' min';
                    const texto = caja.querySelector('.duration-text');

                    caja.classList.remove('alert-success', 'alert-danger', 'alert-secondary');

                    if (!inicio || !fin) {
                        caja.classList.add('alert-secondary');
                        texto.textContent = 'Ingresa la fecha y hora de inicio y de término para calcular la duración (debe ser ' + exigido + ').';
                        return;
                    }

                    const transcurrido = Math.round((fin - inicio) / 60000);
                    const diferencia = transcurrido - esperado;

                    if (Math.abs(diferencia) <= TOLERANCIA) {
                        caja.classList.add('alert-success');
                        texto.textContent = formato(transcurrido) + ': dentro del margen (' + exigido + ').';
                    } else {
                        caja.classList.add('alert-danger');
                        texto.textContent = formato(transcurrido) + (diferencia < 0 ? ': duró menos' : ': duró más')
                            + ' de lo exigido (' + exigido + ').';
                    }
                }

                function actualizarTodas() {
                    document.querySelectorAll('.duration-check').forEach(actualizar);
                }

                document.addEventListener('change', actualizarTodas);
                document.addEventListener('input', actualizarTodas);
            })();
        </script>
    @endpush
@endonce
