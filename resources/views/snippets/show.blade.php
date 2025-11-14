@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-code me-2"></i>{{ $snippet->title }}
                        </h4>
                        <div>
                            <a href="{{ route('home') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información del Snippet -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Categoría</label>
                                <p class="mb-0">
                                    <i class="fas fa-folder text-warning me-2"></i>
                                    {{ $snippet->category->name }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Lenguaje</label>
                                <p class="mb-0">
                                    @if($snippet->language)
                                        <i class="fas fa-language me-2" style="color: {{ $snippet->language->color }}"></i>
                                        <span class="badge" style="background-color: {{ $snippet->language->color }}; color: white;">
                                            {{ $snippet->language->name }}
                                        </span>
                                    @else
                                        <i class="fas fa-language text-info me-2"></i>
                                        Sin lenguaje
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Propietario</label>
                                <p class="mb-0">
                                    <i class="fas fa-user text-success me-2"></i>
                                    {{ $snippet->user->name ?? 'Usuario no disponible' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Creado</label>
                                <p class="mb-0">
                                    <i class="fas fa-calendar text-success me-2"></i>
                                    {{ $snippet->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Actualizado</label>
                                <p class="mb-0">
                                    <i class="fas fa-edit text-warning me-2"></i>
                                    {{ $snippet->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas del código -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body py-2">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <small class="text-muted">Líneas de código</small>
                                            <h6 class="mb-0 text-primary">{{ substr_count($snippet->code, "\n") + 1 }}</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Caracteres</small>
                                            <h6 class="mb-0 text-info">{{ strlen($snippet->code) }}</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Tamaño</small>
                                            <h6 class="mb-0 text-success">{{ number_format(strlen($snippet->code) / 1024, 2) }} KB</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Estado</small>
                                            <h6 class="mb-0">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    @if($snippet->description)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">
                            <i class="fas fa-align-left me-1"></i>Descripción
                        </label>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <p class="mb-0">{{ $snippet->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Código -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold text-muted mb-0">
                                <i class="fas fa-code me-1"></i>Código
                                @if($snippet->language)
                                    <small class="text-muted ms-2">({{ $snippet->language->name }})</small>
                                @endif
                            </label>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2" onclick="copyAllCode()">
                                    <i class="fas fa-copy me-1"></i> Copiar Todo
                                </button>
                                @if($snippet->language)
                                    <span class="badge" style="background-color: {{ $snippet->language->color }};">
                                        {{ $snippet->language->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card border-0 bg-dark text-light">
                            <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center py-2">
                                <small class="text-muted">
                                    <i class="fas fa-file-code me-1"></i>
                                    {{ $snippet->language->name ?? 'Texto plano' }}
                                    @if($snippet->language && !$snippet->language->is_active)
                                        <span class="badge bg-warning text-dark ms-1" title="Lenguaje inactivo">
                                            <i class="fas fa-eye-slash me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </small>
                                <div>
                                    <small class="text-muted me-3">
                                        <i class="fas fa-ruler me-1"></i>{{ strlen($snippet->code) }} chars
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-file-alt me-1"></i>{{ substr_count($snippet->code, "\n") + 1 }} líneas
                                    </small>
                                </div>
                            </div>
                            <div class="card-body p-0 position-relative">
                                <button class="btn btn-sm btn-outline-light copy-btn position-absolute" 
                                        style="top: 10px; right: 10px; z-index: 10;"
                                        onclick="copyAllCode()"
                                        title="Copiar código completo">
                                    <i class="fas fa-copy"></i>
                                </button>
                                
                                <pre class="m-0 p-3 overflow-auto code-pre" style="max-height: 500px; font-size: 0.9rem; line-height: 1.4;">
<code id="snippet-code" class="language-{{ $snippet->language->slug ?? 'plaintext' }}">{{ htmlspecialchars($snippet->code) }}</code>
                                </pre>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('snippets.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Volver al Listado
                                    </a>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('snippets.edit', $snippet) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-1"></i> Editar
                                    </a>
                                    @if($snippet->user_id === auth()->id())
                                        <form action="{{ route('snippets.destroy', $snippet) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este snippet? Esta acción no se puede deshacer.')">
                                                <i class="fas fa-trash me-1"></i> Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger" disabled title="Solo el propietario puede eliminar este snippet">
                                            <i class="fas fa-trash me-1"></i> Eliminar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast para mostrar mensaje de copiado -->
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

@section('scripts')
<script>
function copyAllCode() {
    const codeElement = document.getElementById('snippet-code');
    if (!codeElement) {
        alert('Error: No se pudo encontrar el código');
        return;
    }
    
    // Obtener el texto sin formato HTML
    const code = codeElement.textContent || codeElement.innerText;
    
    // Usar Clipboard API moderna
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).then(function() {
            showCopySuccess();
        }).catch(function(err) {
            console.error('Error con Clipboard API:', err);
            fallbackCopyText(code);
        });
    } else {
        fallbackCopyText(code);
    }
}

function fallbackCopyText(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    textarea.style.left = '-999999px';
    textarea.style.top = '-999999px';
    
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    
    try {
        const successful = document.execCommand('copy');
        document.body.removeChild(textarea);
        
        if (successful) {
            showCopySuccess();
        } else {
            alert('Error al copiar. Por favor, selecciona el código manualmente.');
        }
    } catch (err) {
        document.body.removeChild(textarea);
        alert('Error al copiar. Por favor, selecciona el código manualmente.');
    }
}

function showCopySuccess() {
    const toastEl = document.getElementById('copyToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
    
    // Feedback visual en los botones
    const buttons = document.querySelectorAll('.copy-btn, .btn-outline-primary');
    buttons.forEach(btn => {
        if (btn.textContent.includes('Copiar') || btn.querySelector('.fa-copy')) {
            const originalHTML = btn.innerHTML;
            const originalClasses = btn.className;
            
            btn.innerHTML = '<i class="fas fa-check"></i> Copiado';
            btn.className = originalClasses.replace('btn-outline-light', 'btn-success').replace('btn-outline-primary', 'btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.className = originalClasses;
            }, 2000);
        }
    });
}

// Permitir Ctrl+A para seleccionar todo el código
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
        const codeElement = document.getElementById('snippet-code');
        if (codeElement && document.activeElement !== codeElement && !e.target.matches('input, textarea, select')) {
            e.preventDefault();
            const range = document.createRange();
            range.selectNodeContents(codeElement);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }
});

// Inicializar resaltado de sintaxis si está disponible
document.addEventListener('DOMContentLoaded', function() {
    // Resaltar sintaxis si highlight.js está disponible
    if (typeof hljs !== 'undefined') {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block);
        });
    }
    
    // También permitir copiar haciendo clic en el área del código (solo si no es selección de texto)
    let isSelecting = false;
    const codePre = document.querySelector('.code-pre');
    
    if (codePre) {
        codePre.addEventListener('mousedown', function() {
            isSelecting = false;
        });
        
        codePre.addEventListener('mousemove', function() {
            isSelecting = true;
        });
        
        codePre.addEventListener('click', function(e) {
            if (!isSelecting && !e.target.closest('.copy-btn')) {
                const selection = window.getSelection();
                if (selection.toString().length === 0) {
                    copyAllCode();
                }
            }
            isSelecting = false;
        });
    }
});

// Mostrar información adicional al hacer hover en elementos
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
.card pre {
    cursor: text;
    font-family: 'Fira Code', 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

.card pre code {
    background: transparent !important;
    font-family: inherit;
}

.copy-btn {
    transition: all 0.3s ease;
    opacity: 0.8;
}

.copy-btn:hover {
    transform: scale(1.1);
    opacity: 1;
}

.bg-dark .copy-btn {
    border-color: #6c757d;
}

.bg-dark .copy-btn:hover {
    background-color: rgba(255, 255, 255, 0.15);
    border-color: #fff;
}

.toast {
    min-width: 300px;
    z-index: 9999;
}

/* Mejorar la legibilidad del código */
.code-pre {
    scrollbar-width: thin;
    scrollbar-color: #495057 #343a40;
}

.code-pre::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.code-pre::-webkit-scrollbar-track {
    background: #343a40;
    border-radius: 4px;
}

.code-pre::-webkit-scrollbar-thumb {
    background: #495057;
    border-radius: 4px;
}

.code-pre::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
}

/* Estilos para estadísticas */
.bg-light .row > div {
    border-right: 1px solid #dee2e6;
}

.bg-light .row > div:last-child {
    border-right: none;
}

@media (max-width: 768px) {
    .bg-light .row > div {
        border-right: none;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    
    .bg-light .row > div:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
}
</style>
@endsection