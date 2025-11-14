@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </h4>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Estadísticas del Usuario -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas
                            </h5>
                            <div class="row">
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center">
                                            <h3 class="text-primary mb-1">{{ $stats['total_snippets'] }}</h3>
                                            <small class="text-muted">Snippets</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center">
                                            <h3 class="text-primary mb-1">{{ $stats['total_categories'] }}</h3>
                                            <small class="text-muted">Categorías</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center">
                                            <h3 class="text-primary mb-1">{{ $stats['total_languages'] }}</h3>
                                            <small class="text-muted">Lenguajes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center">
                                            <h3 class="text-primary mb-1">{{ $stats['member_since'] }}</h3>
                                                <small class="text-muted">Tiempo como miembro</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información del Perfil -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0 text-primary">
                                        <i class="fas fa-user-circle me-2"></i>Información Personal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-3">
                                            <label for="name" class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', $user->name) }}" 
                                                   required
                                                   maxlength="255">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email', $user->email) }}" 
                                                   required
                                                   maxlength="255">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary" id="submitProfile">
                                                <i class="fas fa-save me-1"></i>Actualizar Información
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Cambiar Contraseña -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0 text-primary">
                                        <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-3">
                                            <label for="current_password" class="form-label fw-semibold">Contraseña Actual <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('current_password') is-invalid @enderror" 
                                                       id="current_password" 
                                                       name="current_password" 
                                                       required
                                                       minlength="8">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label fw-semibold">Nueva Contraseña <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password" 
                                                       required
                                                       minlength="8">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">Mínimo 8 caracteres</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="password_confirmation" 
                                                       name="password_confirmation" 
                                                       required
                                                       minlength="8">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Indicador de fortaleza de contraseña -->
                                        <div class="mb-3">
                                            <div class="progress" style="height: 5px; display: none;" id="passwordStrengthBar">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="form-text text-muted" id="passwordStrengthText"></small>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-warning" id="submitPassword">
                                                <i class="fas fa-key me-1"></i>Cambiar Contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Respaldo de Datos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0 text-primary">
                                        <i class="fas fa-download me-2"></i>Respaldo de Datos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        Descarga un respaldo completo de tus datos en formato Seeder (archivos PHP). 
                                        Incluye snippets, categorías, lenguajes y tu información de usuario.
                                    </p>
                                    
                                    <form method="POST" action="{{ route('profile.backup') }}" id="backupForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="backup_type" class="form-label fw-semibold">Tipo de Respaldo</label>
                                                <select class="form-select" id="backup_type" name="backup_type">
                                                    <option value="snippets">Solo Snippets</option>
                                                    <option value="full">Respaldo Completo</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="include_code" class="form-label fw-semibold">Incluir Código</label>
                                                <select class="form-select" id="include_code" name="include_code">
                                                    <option value="1">Sí, incluir código</option>
                                                    <option value="0">No, solo metadatos</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                                <button type="submit" class="btn btn-success w-100" id="generateBackup">
                                                    <i class="fas fa-file-download me-1"></i>Generar Respaldo
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Información del respaldo -->
                                        <div class="alert alert-info mt-3" id="backupInfo" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <span id="backupMessage"></span>
                                                </div>
                                                <div class="spinner-border spinner-border-sm ms-2" id="backupSpinner" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6 class="text-primary mb-2">
                                            <i class="fas fa-info-circle me-2"></i>¿Qué incluye el respaldo?
                                        </h6>
                                        <ul class="mb-0">
                                            <li><strong>Snippets:</strong> Todos tus fragmentos de código</li>
                                            <li><strong>Categorías:</strong> Las categorías que utilizas</li>
                                            <li><strong>Lenguajes:</strong> Los lenguajes de programación que usas</li>
                                            <li><strong>Usuario:</strong> Tu información de perfil (en respaldo completo)</li>
                                            <li><strong>Archivos README:</strong> Instrucciones de instalación</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Cuenta -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Información de la Cuenta
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Miembro desde:</strong></p>
                                            <p class="text-muted">{{ $user->created_at->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Última actualización:</strong></p>
                                            <p class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Estado:</strong></p>
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Email Verificado
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Email No Verificado
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Volver -->
                    <div class="d-flex justify-content-start mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Validación de fortaleza de contraseña
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');

    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let text = '';
            let barColor = '';

            if (password.length > 0) {
                strengthBar.style.display = 'block';
                
                // Validaciones de fortaleza
                if (password.length >= 8) strength += 25;
                if (/[a-z]/.test(password)) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                
                // Determinar texto y color
                if (strength <= 25) {
                    text = 'Muy débil';
                    barColor = 'bg-danger';
                } else if (strength <= 50) {
                    text = 'Débil';
                    barColor = 'bg-warning';
                } else if (strength <= 75) {
                    text = 'Buena';
                    barColor = 'bg-info';
                } else {
                    text = 'Muy fuerte';
                    barColor = 'bg-success';
                }
                
                strengthBar.querySelector('.progress-bar').style.width = strength + '%';
                strengthBar.querySelector('.progress-bar').className = 'progress-bar ' + barColor;
                strengthText.textContent = text;
            } else {
                strengthBar.style.display = 'none';
                strengthText.textContent = '';
            }
        });
    }

    // Manejo del formulario de respaldo
    const backupForm = document.getElementById('backupForm');
    const backupInfo = document.getElementById('backupInfo');
    const backupMessage = document.getElementById('backupMessage');
    const backupSpinner = document.getElementById('backupSpinner');
    const generateBackup = document.getElementById('generateBackup');

    if (backupForm) {
        backupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar información de procesamiento
            backupInfo.style.display = 'block';
            backupSpinner.style.display = 'inline-block';
            generateBackup.disabled = true;
            
            const backupType = document.getElementById('backup_type').value;
            const includeCode = document.getElementById('include_code').value;
            
            let message = 'Generando respaldo ';
            message += backupType === 'full' ? 'completo' : 'de snippets';
            message += includeCode === '1' ? ' con código...' : ' sin código...';
            
            backupMessage.textContent = message;
            
            // Enviar formulario via AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.blob();
            })
            .then(blob => {
                // Crear URL para descarga
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                
                // Generar nombre de archivo por defecto
                const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
                const filename = `snippets_backup_${timestamp}.zip`;
                a.download = filename;
                
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                // Actualizar mensaje de éxito
                backupSpinner.style.display = 'none';
                backupMessage.textContent = 'Respaldo generado y descargado correctamente.';
                backupInfo.className = 'alert alert-success mt-3';
                
                // Restablecer después de 3 segundos
                setTimeout(() => {
                    backupInfo.style.display = 'none';
                    backupInfo.className = 'alert alert-info mt-3';
                    generateBackup.disabled = false;
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                backupSpinner.style.display = 'none';
                backupMessage.textContent = 'Error al generar el respaldo: ' + error.message;
                backupInfo.className = 'alert alert-danger mt-3';
                
                // Restablecer después de 5 segundos
                setTimeout(() => {
                    backupInfo.style.display = 'none';
                    backupInfo.className = 'alert alert-info mt-3';
                    generateBackup.disabled = false;
                }, 5000);
            });
        });
    }

    // Prevenir envío múltiple de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        if (form.id !== 'backupForm') {
            form.addEventListener('submit', function() {
                const submitButtons = this.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';
                });
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.toggle-password {
    border-left: 0;
}

.toggle-password:hover {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.progress {
    border-radius: 3px;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.form-label {
    font-size: 0.9rem;
}

.btn {
    border-radius: 6px;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush