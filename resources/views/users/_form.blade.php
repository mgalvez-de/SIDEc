{{-- Campos compartidos por crear y editar usuario. $user es null al crear. --}}
<div class="mb-3">
    <label for="name" class="form-label">Nombre completo</label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $user->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Correo (se usa para iniciar sesión)</label>
    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email', $user->email ?? '') }}" required>
    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="role" class="form-label">Rol</label>
    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
        <option value="">Selecciona un rol</option>
        @foreach ($roles as $value => $label)
            <option value="{{ $value }}" {{ old('role', isset($user) ? $user->getRoleNames()->first() : '') === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="password" class="form-label">
            {{ isset($user) ? 'Nueva contraseña' : 'Contraseña' }}
        </label>
        <input type="password" id="password" name="password" autocomplete="new-password"
            class="form-control @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @isset($user)
            <div class="form-text">Déjala en blanco para no cambiarla.</div>
        @endisset
    </div>
    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Repetir contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
            class="form-control" {{ isset($user) ? '' : 'required' }}>
    </div>
</div>
