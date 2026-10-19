/**
 * Formato chileno para los números: coma decimal y punto de miles (ej.: 1.234,5).
 *
 * Los formularios usan <input type="number">, que el navegador muestra con punto decimal y sin miles.
 * Este script convierte esos campos en campos de texto que:
 *  - muestran el número con coma decimal y, al salir del campo, con punto de miles;
 *  - mientras se escribe, la tecla "." también pone coma (el punto queda solo para los miles);
 *  - entregan el número con punto ("1234.5") a los cálculos en JavaScript (input.value) y al servidor,
 *    así que los cálculos que ya existían, la validación de Laravel y la base de datos no cambian.
 */
(function () {
    // El "value" original del navegador, para leer y escribir el texto tal como se ve en pantalla
    const native = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
    const read = (input) => native.get.call(input);
    const write = (input, text) => native.set.call(input, text);

    // "1.234,5" -> "1234.5"
    function toMachine(text) {
        return text.replace(/\./g, '').replace(',', '.');
    }

    // "1234.5" -> "1.234,5" (mantiene los decimales tal como vienen, sin redondear)
    function toDisplay(value, withThousands) {
        const text = String(value ?? '').trim();
        if (!/^-?\d*(\.\d*)?$/.test(text)) return text; // no es un número escrito con punto: se deja igual

        let [integer, decimals] = text.split('.');
        if (withThousands) integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return decimals === undefined ? integer : integer + ',' + decimals;
    }

    // Reemplaza la validación de min y max que hacía el navegador con type="number"
    function validate(input) {
        const text = read(input);
        const number = Number(toMachine(text));
        let message = '';

        if (text !== '' && Number.isNaN(number)) {
            message = 'Ingresa un número válido.';
        } else if (text !== '' && input.dataset.integer !== undefined && !Number.isInteger(number)) {
            message = 'Este campo solo acepta números enteros.';
        } else if (text !== '' && input.dataset.min !== undefined && number < Number(input.dataset.min)) {
            message = 'El valor debe ser mayor o igual a ' + toDisplay(input.dataset.min, true) + '.';
        } else if (text !== '' && input.dataset.max !== undefined && number > Number(input.dataset.max)) {
            message = 'El valor debe ser menor o igual a ' + toDisplay(input.dataset.max, true) + '.';
        }
        input.setCustomValidity(message);
    }

    function setup(input) {
        const initial = read(input);
        const step = input.getAttribute('step');
        const min = input.getAttribute('min');
        const max = input.getAttribute('max');
        // Sin step (o con step entero) el campo solo aceptaba enteros, igual que ahora
        const acceptsDecimals = step === 'any' || (step !== null && !Number.isInteger(Number(step)));
        const acceptsNegative = min === null || Number(min) < 0;

        input.type = 'text';
        input.inputMode = acceptsDecimals ? 'decimal' : 'numeric';
        input.classList.add('decimal-input');
        if (!acceptsDecimals) input.dataset.integer = '';
        if (min !== null) input.dataset.min = min;
        if (max !== null) input.dataset.max = max;

        // Lo que leen y escriben los cálculos (input.value o $(input).val()) va siempre con punto
        Object.defineProperty(input, 'value', {
            configurable: true,
            get() {
                return toMachine(read(this));
            },
            set(value) {
                write(this, toDisplay(value, document.activeElement !== this));
                validate(this);
            },
        });
        input.value = initial;

        // Al entrar al campo se quitan los puntos de miles para editar cómodo; al salir se vuelven a poner
        input.addEventListener('focus', () => write(input, read(input).replace(/\./g, '')));
        input.addEventListener('blur', () => write(input, toDisplay(input.value, true)));

        input.addEventListener('input', () => {
            const raw = read(input);
            // La tecla "." pone coma, y se borra todo lo que no sea dígito, coma o signo menos.
            // (En un campo de enteros la coma se deja y validate() avisa, en vez de borrarla y cambiar el número.)
            let text = raw.replace(/\./g, ',').replace(/[^\d,-]/g, '');
            text = acceptsNegative ? text.replace(/(?!^)-/g, '') : text.replace(/-/g, '');

            const comma = text.indexOf(',');                                  // una sola coma
            if (comma !== -1) text = text.slice(0, comma + 1) + text.slice(comma + 1).replace(/,/g, '');

            if (text !== raw) {
                const caret = Math.max(0, input.selectionStart - (raw.length - text.length));
                write(input, text);
                input.setSelectionRange(caret, caret);
            }
            validate(input);
        });
    }

    // Los campos internos del calendario (flatpickr: año, hora, minutos) se dejan como están
    document.querySelectorAll('input[type="number"]').forEach((input) => {
        if (!input.closest('.flatpickr-calendar')) setup(input);
    });

    // Al enviar un formulario, el servidor recibe los números con punto ("1234.5")
    document.addEventListener('formdata', function (event) {
        const fields = Array.from(event.target.elements)
            .filter((el) => el.classList.contains('decimal-input') && el.name && !el.disabled);

        new Set(fields.map((el) => el.name)).forEach((name) => {
            event.formData.delete(name);
            fields.filter((el) => el.name === name).forEach((el) => event.formData.append(name, el.value));
        });
    }, true);
})();
