@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Snippet
                        </h4>
                        <a href="{{ route('snippets.index') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
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
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('snippets.update', $snippet->id) }}" method="POST" id="snippetForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Título -->
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-semibold">
                                        <i class="fas fa-heading me-1 text-warning"></i>Título del Snippet
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $snippet->title) }}" 
                                           placeholder="Ej: Función para validar email en PHP"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Un título descriptivo para tu snippet.</div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="fas fa-align-left me-1 text-warning"></i>Descripción (Opcional)
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Describe el propósito de este snippet...">{{ old('description', $snippet->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Breve descripción sobre qué hace este código.</div>
                                </div>

                                <!-- Código -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="code" class="form-label fw-semibold mb-0">
                                            <i class="fas fa-code me-1 text-warning"></i>Código
                                        </label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="formatCode">
                                                <i class="fas fa-broom me-1"></i>Formatear
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" id="copyCode">
                                                <i class="fas fa-copy me-1"></i>Copiar
                                            </button>
                                        </div>
                                    </label>
                                    </div>
                                    <div class="code-editor-container position-relative">
                                        <textarea class="form-control code-textarea @error('code') is-invalid @enderror" 
                                                  id="code" 
                                                  name="code" 
                                                  rows="15" 
                                                  placeholder="Pega tu código aquí..."
                                                  required>{{ old('code', $snippet->code) }}</textarea>
                                        <div class="code-preview bg-dark text-light rounded p-3 mt-2 d-none">
                                            <pre><code class="language-{{ $snippet->language->slug ?? 'plaintext' }}" id="codePreview"></code></pre>
                                        </div>
                                    </div>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text d-flex justify-content-between">
                                        <span>Usa el botón "Formatear" para limpiar el código</span>
                                        <span id="charCount">0 caracteres</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Información del Snippet -->
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">
                                            <i class="fas fa-info-circle me-2"></i>Información del Snippet
                                        </h6>
                                        <div class="small">
                                            <p class="mb-1"><strong>Creado:</strong></p>
                                            <p class="text-muted">{{ $snippet->created_at->format('d/m/Y H:i') }}</p>
                                            
                                            <p class="mb-1"><strong>Actualizado:</strong></p>
                                            <p class="text-muted">{{ $snippet->updated_at->format('d/m/Y H:i') }}</p>
                                            
                                            <p class="mb-1"><strong>Propietario:</strong></p>
                                            <p class="text-muted">{{ $snippet->user->name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Categoría -->
                                <div class="mb-4">
                                    <label for="category_id" class="form-label fw-semibold">
                                        <i class="fas fa-folder me-1 text-warning"></i>Categoría
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $snippet->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Organiza tu snippet en una categoría.</div>
                                </div>

                                <!-- Lenguaje -->
                                <div class="mb-4">
                                    <label for="language_id" class="form-label fw-semibold">
                                        <i class="fas fa-language me-1 text-warning"></i>Lenguaje de Programación
                                    </label>
                                    <select class="form-select @error('language_id') is-invalid @enderror" 
                                            id="language_id" 
                                            name="language_id" 
                                            required>
                                        <option value="">Selecciona un lenguaje</option>
                                        @foreach($languages as $language)
                                            <option value="{{ $language->id }}" 
                                                data-slug="{{ $language->slug }}"
                                                {{ old('language_id', $snippet->language_id) == $language->id ? 'selected' : '' }}
                                                {{ !$language->is_active ? 'disabled' : '' }}>
                                                {{ $language->name }}
                                                @if(!$language->is_active)
                                                    (Inactivo)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('language_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Selecciona el lenguaje para resaltado de sintaxis.</div>
                                </div>

                                <!-- Vista Previa del Lenguaje -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-palette me-1 text-warning"></i>Color del Lenguaje
                                    </label>
                                    <div class="d-flex align-items-center gap-2 p-2 rounded" 
                                         style="background-color: {{ $snippet->language->color ?? '#6c757d' }}20; 
                                                border-left: 4px solid {{ $snippet->language->color ?? '#6c757d' }};">
                                        <div class="color-preview rounded" 
                                             style="width: 20px; height: 20px; background-color: {{ $snippet->language->color ?? '#6c757d' }};"></div>
                                        <small class="fw-semibold">{{ $snippet->language->name ?? 'Sin lenguaje' }}</small>
                                    </div>
                                    <div class="form-text">El color identifica visualmente el lenguaje.</div>
                                </div>

                                <!-- Estadísticas del Código -->
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">
                                            <i class="fas fa-chart-bar me-2"></i>Estadísticas
                                        </h6>
                                        <div class="small">
                                            <p class="mb-1"><strong>Líneas de código:</strong></p>
                                            <p class="text-muted" id="lineCount">{{ substr_count($snippet->code, "\n") + 1 }}</p>
                                            
                                            <p class="mb-1"><strong>Tamaño:</strong></p>
                                            <p class="text-muted" id="sizeInfo">{{ strlen($snippet->code) }} caracteres</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('snippets.show', $snippet->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <i class="fas fa-save me-1"></i>Actualizar Snippet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast para mensajes -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                Código copiado al portapapeles
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeTextarea = document.getElementById('code');
    const codePreview = document.getElementById('codePreview');
    const previewContainer = document.querySelector('.code-preview');
    const languageSelect = document.getElementById('language_id');
    const formatBtn = document.getElementById('formatCode');
    const copyBtn = document.getElementById('copyCode');
    const charCount = document.getElementById('charCount');
    const lineCount = document.getElementById('lineCount');
    const sizeInfo = document.getElementById('sizeInfo');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('snippetForm');

    // Actualizar contador de caracteres y líneas
    function updateStats() {
        const code = codeTextarea.value;
        const chars = code.length;
        const lines = code.split('\n').length;
        
        charCount.textContent = `${chars} caracteres`;
        lineCount.textContent = lines;
        sizeInfo.textContent = `${chars} caracteres`;
        
        // Actualizar vista previa
        if (code.trim() !== '') {
            const selectedOption = languageSelect.options[languageSelect.selectedIndex];
            const languageSlug = selectedOption ? selectedOption.getAttribute('data-slug') : 'plaintext';
            
            codePreview.textContent = code;
            codePreview.className = `language-${languageSlug}`;
            previewContainer.classList.remove('d-none');
            
            // Resaltar sintaxis
            if (typeof hljs !== 'undefined') {
                hljs.highlightElement(codePreview);
            }
        } else {
            previewContainer.classList.add('d-none');
        }
    }

    // Inicializar estadísticas
    updateStats();

    // Event listeners
    codeTextarea.addEventListener('input', updateStats);
    languageSelect.addEventListener('change', updateStats);

    // Formatear código
    formatBtn.addEventListener('click', function() {
        let code = codeTextarea.value;
        
        // Limpiar espacios en blanco al inicio y final
        code = code.trim();
        
        // Reemplazar tabs por espacios (opcional)
        code = code.replace(/\t/g, '    ');
        
        // Normalizar saltos de línea
        code = code.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
        
        // Eliminar líneas vacías al final
        code = code.replace(/\n+$/, '');
        
        codeTextarea.value = code;
        updateStats();
        
        // Mostrar feedback
        const toast = new bootstrap.Toast(document.getElementById('copyToast'));
        document.querySelector('.toast-body').innerHTML = '<i class="fas fa-broom me-2"></i>Código formateado';
        document.getElementById('copyToast').classList.remove('bg-success', 'bg-danger');
        document.getElementById('copyToast').classList.add('bg-info');
        toast.show();
    });

    // Copiar código
    copyBtn.addEventListener('click', async function() {
        try {
            await navigator.clipboard.writeText(codeTextarea.value);
            
            const toast = new bootstrap.Toast(document.getElementById('copyToast'));
            document.querySelector('.toast-body').innerHTML = '<i class="fas fa-check-circle me-2"></i>Código copiado al portapapeles';
            document.getElementById('copyToast').classList.remove('bg-info', 'bg-danger');
            document.getElementById('copyToast').classList.add('bg-success');
            toast.show();
        } catch (err) {
            console.error('Error al copiar:', err);
            
            const toast = new bootstrap.Toast(document.getElementById('copyToast'));
            document.querySelector('.toast-body').innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Error al copiar';
            document.getElementById('copyToast').classList.remove('bg-success', 'bg-info');
            document.getElementById('copyToast').classList.add('bg-danger');
            toast.show();
        }
    });

    // Validación antes de enviar
    form.addEventListener('submit', function(e) {
        const code = codeTextarea.value.trim();
        if (code === '') {
            e.preventDefault();
            alert('El código no puede estar vacío.');
            codeTextarea.focus();
            return;
        }
        
        // Mostrar loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
        submitBtn.disabled = true;
    });

    // Actualizar vista previa del lenguaje
    languageSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption) {
            updateStats();
        }
    });

    // Resaltar sintaxis inicial
    if (typeof hljs !== 'undefined') {
        hljs.highlightAll();
    }
});
</script>

<style>
.code-editor-container {
    font-family: 'Fira Code', 'Courier New', monospace;
}

.code-textarea {
    font-family: 'Fira Code', 'Courier New', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    resize: vertical;
}

.code-textarea:focus {
    background: #fff;
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.code-preview {
    font-size: 0.8rem;
    max-height: 200px;
    overflow-y: auto;
}

.code-preview pre {
    margin: 0;
    background: transparent !important;
}

.color-preview {
    border: 1px solid #dee2e6;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.form-control, .form-select {
    border-radius: 6px;
}

.form-control:focus, .form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.toast {
    min-width: 300px;
    z-index: 9999;
}
</style>
@endpush