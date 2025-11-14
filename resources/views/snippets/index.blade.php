@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-code me-2"></i>Mis Snippets Organizados
                        </h4>
                        <div>
                            <a href="{{ route('home') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            <a href="{{ route('snippets.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i>Nuevo Snippet
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center py-3">
                                    <h5 class="mb-1">{{ $snippets->count() }}</h5>
                                    <small class="opacity-75">Total Snippets</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center py-3">
                                    <h5 class="mb-1">{{ $snippetsWithLanguage->count() }}</h5>
                                    <small class="opacity-75">Con Lenguaje</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center py-3">
                                    <h5 class="mb-1">{{ $snippetsWithoutLanguage->count() }}</h5>
                                    <small class="opacity-75">Sin Lenguaje</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center py-3">
                                    <h5 class="mb-1">{{ $snippets->groupBy('category_id')->count() }}</h5>
                                    <small class="opacity-75">Categorías</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        // Función para determinar el color del texto basado en el color de fondo
                        function getTextColor($hexColor) {
                            // Si no es un color hexadecimal válido, retornar blanco
                            if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $hexColor)) {
                                return 'white';
                            }
                            
                            // Convertir hex a RGB
                            $hex = ltrim($hexColor, '#');
                            
                            if (strlen($hex) == 3) {
                                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                            }
                            
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                            
                            // Calcular luminosidad (fórmula estándar)
                            $luminosity = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                            
                            // Si la luminosidad es mayor a 0.5, usar texto oscuro, sino claro
                            return $luminosity > 0.5 ? 'black' : 'white';
                        }

                        // Filtrar snippets
                        $snippetsWithLanguage = $snippets->filter(function($snippet) {
                            return $snippet->language !== null;
                        });
                        
                        $snippetsWithoutLanguage = $snippets->filter(function($snippet) {
                            return $snippet->language === null;
                        });
                    @endphp

                    @if($snippets->count() > 0)

                        <!-- Snippets con lenguaje asociado -->
                        @if($snippetsWithLanguage->count() > 0)
                            <div class="mb-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="text-success mb-1">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Snippets con Lenguaje Asociado
                                        </h5>
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Estos snippets tienen un lenguaje de programación asignado para mejor organización.
                                        </p>
                                    </div>
                                    <span class="badge bg-success fs-6">{{ $snippetsWithLanguage->count() }} snippets</span>
                                </div>
                                <div class="row">
                                    @foreach ($snippetsWithLanguage as $snippet)
                                        @php
                                            // Obtener el color del lenguaje o usar uno por defecto
                                            $languageColor = $snippet->language->color ?? '#6c757d';
                                            $textColor = getTextColor($languageColor);
                                            
                                            // Estadísticas del snippet
                                            $lineCount = substr_count($snippet->code, "\n") + 1;
                                            $charCount = strlen($snippet->code);
                                        @endphp
                                        <div class="col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                            <div class="card w-100 shadow-sm snippet-card border-0">
                                                <!-- Card header con color del lenguaje -->
                                                <div class="card-header d-flex justify-content-between align-items-center" 
                                                     style="background: linear-gradient(135deg, {{ $languageColor }} 0%, {{ $languageColor }}dd 100%); color: {{ $textColor }};">
                                                    <h6 class="mb-0 fw-bold">{{ Str::limit($snippet->title, 25) }}</h6>
                                                    <span class="badge" 
                                                          style="background-color: rgba(255,255,255,0.3); color: {{ $textColor }}; backdrop-filter: blur(10px);">
                                                        {{ $snippet->language->name }}
                                                    </span>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <!-- Información de categoría y estadísticas -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-folder me-1"></i>{{ Str::limit($snippet->category->name, 20) }}
                                                        </span>
                                                        <div class="text-muted small">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $snippet->updated_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Vista previa del código -->
                                                    <div class="code-container position-relative bg-dark rounded border-0 p-3 flex-grow-1">
                                                        <button class="btn btn-sm copy-btn position-absolute" 
                                                                style="top: 8px; right: 8px; background-color: {{ $languageColor }}; color: {{ $textColor }};"
                                                                data-code="{{ htmlspecialchars($snippet->code) }}"
                                                                title="Copiar código completo">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <pre class="m-0 overflow-auto" style="max-height: 120px; font-size: 0.75rem; line-height: 1.3;">
<code class="language-{{ $snippet->language->slug ?? 'plaintext' }}">{{ $snippet->getFirstLines(8) }}</code>
                                                        </pre>
                                                    </div>

                                                    <!-- Estadísticas del código -->
                                                    <div class="mt-3 d-flex justify-content-between text-muted small">
                                                        <span>
                                                            <i class="fas fa-file-code me-1"></i>
                                                            {{ $lineCount }} líneas
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-ruler me-1"></i>
                                                            {{ $charCount }} chars
                                                        </span>
                                                    </div>

                                                    <!-- Descripción (si existe) -->
                                                    @if($snippet->description)
                                                        <div class="mt-2">
                                                            <p class="small text-muted mb-0">
                                                                <i class="fas fa-align-left me-1"></i>
                                                                {{ Str::limit($snippet->description, 60) }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card-footer bg-transparent mt-auto border-0 pt-0">
                                                    <div class="btn-group w-100">
                                                        <a href="{{ route('snippets.show', $snippet->id) }}" 
                                                           class="btn btn-sm btn-outline-info" 
                                                           title="Ver snippet">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('snippets.edit', $snippet->id) }}" 
                                                           class="btn btn-sm btn-outline-warning"
                                                           title="Editar snippet">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('snippets.destroy', $snippet->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('¿Estás seguro de eliminar este snippet?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    title="Eliminar snippet">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Snippets sin lenguaje asociado -->
                        @if($snippetsWithoutLanguage->count() > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="text-warning mb-1">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Snippets sin Lenguaje Asociado
                                        </h5>
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Asigna un lenguaje de programación para mejor organización y resaltado de sintaxis.
                                        </p>
                                    </div>
                                    <span class="badge bg-warning text-dark fs-6">{{ $snippetsWithoutLanguage->count() }} snippets</span>
                                </div>
                                
                                <div class="alert alert-warning border-0">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading">¡Mejora la organización!</h6>
                                            <p class="mb-2">Asigna un lenguaje de programación a tus snippets para:</p>
                                            <ul class="mb-0 small">
                                                <li>Mejor resaltado de sintaxis</li>
                                                <li>Búsqueda y filtrado más eficiente</li>
                                                <li>Organización visual por colores</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach ($snippetsWithoutLanguage as $snippet)
                                        @php
                                            // Estadísticas del snippet
                                            $lineCount = substr_count($snippet->code, "\n") + 1;
                                            $charCount = strlen($snippet->code);
                                        @endphp
                                        <div class="col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                            <div class="card w-100 shadow-sm snippet-card border-warning">
                                                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold">{{ Str::limit($snippet->title, 25) }}</h6>
                                                    <span class="badge bg-dark text-white">
                                                        <i class="fas fa-language me-1"></i>Sin lenguaje
                                                    </span>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <!-- Información de categoría y estadísticas -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-folder me-1"></i>{{ Str::limit($snippet->category->name, 20) }}
                                                        </span>
                                                        <div class="text-muted small">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $snippet->updated_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Vista previa del código -->
                                                    <div class="code-container position-relative bg-light rounded border p-3 flex-grow-1">
                                                        <button class="btn btn-sm btn-warning copy-btn position-absolute" 
                                                                style="top: 8px; right: 8px;"
                                                                data-code="{{ htmlspecialchars($snippet->code) }}"
                                                                title="Copiar código completo">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <pre class="m-0 overflow-auto" style="max-height: 120px; font-size: 0.75rem; line-height: 1.3;">
<code class="language-plaintext">{{ $snippet->getFirstLines(8) }}</code>
                                                        </pre>
                                                    </div>

                                                    <!-- Estadísticas del código -->
                                                    <div class="mt-3 d-flex justify-content-between text-muted small">
                                                        <span>
                                                            <i class="fas fa-file-code me-1"></i>
                                                            {{ $lineCount }} líneas
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-ruler me-1"></i>
                                                            {{ $charCount }} chars
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent mt-auto border-warning">
                                                    <div class="btn-group w-100">
                                                        <a href="{{ route('snippets.show', $snippet->id) }}" 
                                                           class="btn btn-sm btn-outline-info"
                                                           title="Ver snippet">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('snippets.edit', $snippet->id) }}" 
                                                           class="btn btn-sm btn-outline-warning"
                                                           title="Editar y asignar lenguaje">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('snippets.destroy', $snippet->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('¿Estás seguro de eliminar este snippet?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    title="Eliminar snippet">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-code fa-4x text-muted mb-4"></i>
                                <h3 class="text-muted">No hay snippets creados</h3>
                                <p class="text-muted mb-4">Comienza organizando tu código creando tu primer snippet</p>
                                <a href="{{ route('snippets.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Crear Primer Snippet
                                </a>
                            </div>
                        </div>
                    @endif
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para determinar el color del texto basado en el color de fondo
    function getTextColor(hexColor) {
        if (!/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hexColor)) {
            return 'white';
        }
        
        let hex = hexColor.replace('#', '');
        
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        
        const luminosity = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return luminosity > 0.5 ? 'black' : 'white';
    }

    // Función mejorada para copiar código
    async function copyCode(code) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(code);
                return true;
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = code;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                textarea.style.left = '-999999px';
                textarea.style.top = '-999999px';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                
                try {
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);
                    return successful;
                } catch (err) {
                    document.body.removeChild(textarea);
                    return false;
                }
            }
        } catch (err) {
            console.error('Error al copiar:', err);
            return false;
        }
    }

    // Función para mostrar el toast
    function showToast(message = 'Código copiado al portapapeles', type = 'success') {
        const toastElement = document.getElementById('copyToast');
        if (toastElement) {
            const toastBody = toastElement.querySelector('.toast-body');
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };
            
            toastBody.innerHTML = `<i class="fas ${icons[type]} me-2"></i>${message}`;
            toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
            
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }

    // Event listeners para los botones de copiar
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const code = this.getAttribute('data-code');
            
            if (await copyCode(code)) {
                // Feedback visual mejorado
                const originalHTML = this.innerHTML;
                const originalBgColor = this.style.backgroundColor;
                const originalColor = this.style.color;
                
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.style.backgroundColor = '#198754';
                this.style.color = 'white';
                this.disabled = true;
                
                showToast();
                
                // Restaurar después de 2 segundos
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.style.backgroundColor = originalBgColor;
                    this.style.color = originalColor;
                    this.disabled = false;
                }, 2000);
            } else {
                showToast('Error al copiar el código', 'error');
            }
        });
    });

    // Aplicar colores de texto correctos a todos los botones de copia
    document.querySelectorAll('.copy-btn').forEach(button => {
        const bgColor = button.style.backgroundColor;
        if (bgColor && bgColor !== '') {
            const textColor = getTextColor(bgColor);
            button.style.color = textColor;
        }
    });

    // Resaltar sintaxis con Highlight.js
    if (typeof hljs !== 'undefined') {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block);
        });
    }

    // Efectos hover mejorados
    document.querySelectorAll('.snippet-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<style>
.snippet-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.snippet-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.code-container {
    font-family: 'Fira Code', 'Courier New', Monaco, monospace;
    position: relative;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    border-radius: 8px;
}

.code-container pre {
    cursor: default;
    white-space: pre-wrap;
    word-break: break-word;
    flex-grow: 1;
    margin: 0;
    background: transparent !important;
}

.code-container code {
    font-family: 'Fira Code', 'Courier New', Monaco, monospace !important;
    font-size: 0.75rem !important;
    line-height: 1.3 !important;
    background: transparent !important;
    display: block;
    white-space: pre-wrap;
    word-break: break-word;
}

.copy-btn {
    opacity: 0.8;
    transition: all 0.3s ease;
    padding: 6px 10px;
    z-index: 10;
    border: none;
    border-radius: 6px;
    font-size: 0.75rem;
    cursor: pointer;
    backdrop-filter: blur(10px);
}

.copy-btn:hover {
    opacity: 1;
    transform: scale(1.1);
}

/* Estilo para el toast */
.toast {
    min-width: 300px;
    font-size: 0.9rem;
    z-index: 9999;
}

/* Mejorar scroll en pre */
.code-container pre {
    scrollbar-width: thin;
    scrollbar-color: #4a5568 #2d3748;
}

.code-container pre::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.code-container pre::-webkit-scrollbar-track {
    background: #2d3748;
    border-radius: 3px;
}

.code-container pre::-webkit-scrollbar-thumb {
    background: #4a5568;
    border-radius: 3px;
}

.code-container pre::-webkit-scrollbar-thumb:hover {
    background: #718096;
}

/* Cards de estadísticas */
.card.bg-primary,
.card.bg-success,
.card.bg-warning,
.card.bg-info {
    border: none;
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.card.bg-primary:hover,
.card.bg-success:hover,
.card.bg-warning:hover,
.card.bg-info:hover {
    transform: translateY(-2px);
}

/* Empty state */
.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.7;
}

/* Badges modernos */
.badge {
    font-weight: 500;
    border-radius: 6px;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .btn-group {
        flex-wrap: wrap;
    }
    
    .btn-group .btn {
        flex: 1;
        min-width: 60px;
    }
}
</style>
@endpush