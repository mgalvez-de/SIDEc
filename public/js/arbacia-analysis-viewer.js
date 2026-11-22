/**
 * Visor de un análisis de API_larvas (detalle del análisis de imagen, planillas de Arbacia).
 *
 * Dibuja en SVG, sobre la fotografía original, el polígono de cada individuo detectado
 * (o su caja si no tiene polígono), con un color fijo por clase. Permite filtrar por clase,
 * resaltar los que tocan el borde y ver clase, confianza y morfometría al pasar el mouse.
 * No depende de las clases: dibuja las que traiga el modelo cargado en la API.
 */
(function () {
    const viewer = document.getElementById('analysisViewer');
    const source = document.getElementById('analysisInstances');
    if (!viewer || !source) return;

    const NS = 'http://www.w3.org/2000/svg';
    const svg = viewer.querySelector('svg');
    const tooltip = viewer.querySelector('.analysis-tooltip');
    const instances = JSON.parse(source.textContent || '[]');

    // Color estable por clase (mismo nombre => mismo color en todas las páginas)
    function colorFor(label) {
        let hash = 0;
        for (const char of String(label)) hash = (hash * 31 + char.charCodeAt(0)) >>> 0;
        return 'hsl(' + (hash % 360) + ', 80%, 45%)';
    }

    function number(value, decimals) {
        if (value === null || value === undefined) return '—';
        return Number(value).toLocaleString('es-CL', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    }

    function tooltipHtml(instance) {
        const m = instance.morphometry || {};
        const escape = (text) => String(text).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
        const rows = [
            '<strong>#' + instance.instance_id + ' · ' + escape(instance.label) + '</strong>',
            'Confianza: ' + number(instance.confidence * 100, 1) + ' %',
            instance.touching_border ? '<span style="color:#ffb3b8">Toca el borde</span>' : 'Completo (no toca el borde)',
            'Área: ' + number(m.area_px, 0) + ' px' + (m.area_um2 != null ? ' · ' + number(m.area_um2, 0) + ' µm²' : ''),
            'Perímetro: ' + number(m.perimeter_px, 1) + ' px',
            'Circularidad: ' + number(m.circularity, 3) + ' · Solidez: ' + number(m.solidity, 3),
            'Razón de aspecto: ' + number(m.aspect_ratio, 2),
        ];
        return rows.join('<br>');
    }

    instances.forEach(function (instance) {
        const color = colorFor(instance.label);
        const group = document.createElementNS(NS, 'g');
        group.dataset.label = instance.label;
        group.dataset.border = instance.touching_border ? '1' : '0';

        let shape;
        if (Array.isArray(instance.polygon_xy) && instance.polygon_xy.length >= 3) {
            shape = document.createElementNS(NS, 'polygon');
            shape.setAttribute('points', instance.polygon_xy.map((p) => p[0] + ',' + p[1]).join(' '));
        } else {
            const [x1, y1, x2, y2] = instance.bbox_xyxy;
            shape = document.createElementNS(NS, 'rect');
            shape.setAttribute('x', x1);
            shape.setAttribute('y', y1);
            shape.setAttribute('width', Math.max(0, x2 - x1));
            shape.setAttribute('height', Math.max(0, y2 - y1));
        }
        shape.setAttribute('fill', color);
        shape.setAttribute('stroke', color);
        group.appendChild(shape);

        group.addEventListener('mouseenter', function () {
            tooltip.innerHTML = tooltipHtml(instance);
            tooltip.classList.remove('d-none');
        });
        group.addEventListener('mousemove', function (event) {
            const box = viewer.getBoundingClientRect();
            let left = event.clientX - box.left + 14;
            let top = event.clientY - box.top + 14;
            if (left + tooltip.offsetWidth > box.width) left = event.clientX - box.left - tooltip.offsetWidth - 14;
            if (top + tooltip.offsetHeight > box.height) top = event.clientY - box.top - tooltip.offsetHeight - 14;
            tooltip.style.left = Math.max(0, left) + 'px';
            tooltip.style.top = Math.max(0, top) + 'px';
        });
        group.addEventListener('mouseleave', function () {
            tooltip.classList.add('d-none');
        });

        svg.appendChild(group);
    });

    // Muestras de color junto a cada filtro
    document.querySelectorAll('.analysis-swatch').forEach(function (swatch) {
        swatch.style.backgroundColor = colorFor(swatch.dataset.label);
    });

    // Filtro por clase
    document.querySelectorAll('.js-class-filter').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            svg.querySelectorAll('g').forEach(function (group) {
                if (group.dataset.label === checkbox.value) {
                    group.style.display = checkbox.checked ? '' : 'none';
                }
            });
        });
    });

    // Resaltar los que tocan el borde
    const highlight = document.getElementById('highlightBorder');
    if (highlight) {
        highlight.addEventListener('change', function () {
            viewer.classList.toggle('highlight-border', highlight.checked);
        });
    }
})();
