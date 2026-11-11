{{-- Páginas de error en español. No usa el menú del sistema: tiene que funcionar aunque algo esté fallando. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · SIDEc</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f8f9fa;
            color: #1f2937;
            font-family: Nunito, "Segoe UI", system-ui, sans-serif;
        }

        .error-box {
            max-width: 480px;
            text-align: center;
        }

        .error-code {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1;
            color: #D12421;
        }

        h1 {
            font-size: 1.5rem;
            margin: 12px 0 8px;
        }

        p {
            color: #4b5563;
            line-height: 1.5;
        }

        .error-actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid #D12421;
            color: #D12421;
            font-weight: 600;
            text-decoration: none;
        }

        a.primary {
            background: #D12421;
            color: #fff;
        }

        small {
            display: block;
            margin-top: 32px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <main class="error-box">
        <div class="error-code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <div class="error-actions">
            <a href="{{ url('/home') }}" class="primary">Volver al inicio</a>
            <a href="javascript:history.back()">Página anterior</a>
        </div>
        <small>SIDEc · Sistema de Información del Departamento de Ecotoxicología</small>
    </main>
</body>
</html>
