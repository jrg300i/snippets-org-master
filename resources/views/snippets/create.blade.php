@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Snippet
                        </h4>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('snippets.store') }}" method="POST" id="createSnippetForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Título -->
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-semibold">
                                        <i class="fas fa-heading me-1 text-primary"></i>Título del Snippet
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="Ej: Función de conexión a base de datos"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Un título descriptivo para tu snippet.</div>
                                </div>

                                <!-- Código -->
                                <div class="mb-4">
                                    <label for="code" class="form-label fw-semibold">
                                        <i class="fas fa-code me-1 text-primary"></i>Código
                                    </label>
                                    <textarea class="form-control @error('code') is-invalid @enderror" 
                                              id="code" 
                                              name="code" 
                                              rows="12" 
                                              placeholder="Pega tu código aquí..."
                                              required>{{ old('code') }}</textarea>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Usa tabulaciones y saltos de línea para mantener el formato.</div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="fas fa-align-left me-1 text-primary"></i>Descripción
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="3"
                                              placeholder="Explica brevemente para qué sirve este snippet (opcional)...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Lenguaje -->
                                <div class="mb-4">
                                    <label for="language_id" class="form-label fw-semibold">
                                        <i class="fas fa-language me-1 text-primary"></i>Lenguaje de Programación
                                    </label>
<select class="form-select @error('language_id') is-invalid @enderror"
                                            id="language_id"
                                            name="language_id">
                                        <option value="">Sin lenguaje</option>
                                        @foreach($languages as $language)
                                            <option value="{{ $language->id }}" {{ old('language_id') == $language->id ? 'selected' : '' }}>
                                                {{ $language->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('language_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Opcional. Asigna un lenguaje para el resaltado de sintaxis.
                                        <a href="{{ route('languages.create') }}" class="text-decoration-none d-inline-flex align-items-center">
                                            <i class="fas fa-plus-circle me-1"></i>Crear nuevo lenguaje
                                        </a>
                                    </div>
                                </div>

                                <!-- Categoría -->
                                <div class="mb-4">
                                    <label for="category_id" class="form-label fw-semibold">
                                        <i class="fas fa-folder me-1 text-primary"></i>Categoría
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <a href="{{ route('categories.create') }}" class="text-decoration-none">
                                            <i class="fas fa-plus-circle me-1"></i>Crear nueva categoría
                                        </a>
                                    </div>
                                </div>

                                <!-- Etiquetas -->
                                <div class="mb-4">
                                    <label for="tags" class="form-label fw-semibold">
                                        <i class="fas fa-tags me-1 text-primary"></i>Etiquetas
                                    </label>
                                    <input type="text"
                                           class="form-control @error('tags') is-invalid @enderror"
                                           id="tags"
                                           name="tags"
                                           value="{{ old('tags') }}"
                                           placeholder="php, laravel, backup"
                                           data-separator=",">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Separa las etiquetas con comas (opcional).</div>
                                </div>

                                <!-- Publicar en thiscodeworks -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="publish_to_api" value="0">
                                        <input class="form-check-input @error('publish_to_api') is-invalid @enderror"
                                               type="checkbox"
                                               id="publish_to_api"
                                               name="publish_to_api"
                                               value="1"
                                               {{ old('publish_to_api', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="publish_to_api">
                                            <i class="fas fa-share-alt me-1 text-primary"></i>Publicar en thiscodeworks
                                        </label>
                                    </div>
                                    @error('publish_to_api')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Se sube a thiscodeworks.com al crear (la API no actualiza ediciones).</div>
                                </div>

                                <!-- Vista previa -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-eye me-1 text-primary"></i>Vista Previa
                                    </label>
                                    <div class="snippet-code p-3 small border rounded bg-light">
                                        <pre><code class="language-plaintext">{{ old('code', '// Tu código aparecerá aquí...') }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Guardar Snippet
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

@push('scripts')
<script>
    function parseTags() {
        return String(document.getElementById('tags')?.value || '')
            .split(',')
            .map(t => t.trim())
            .filter(t => t !== '');
    }

    // Serializar etiquetas antes de enviar
    const snippetForm = document.querySelector('form[action$="/snippets"]');
    if (snippetForm) {
        snippetForm.addEventListener('submit', function () {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'tags[]';
            parseTags().forEach(tag => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tags[]';
                input.value = tag;
                snippetForm.appendChild(input);
            });
        });
    }

    // Actualizar vista previa en tiempo real
    document.getElementById('code').addEventListener('input', function() {
        const languageSelect = document.getElementById('language_id');
        const selectedLanguage = languageSelect.value ? languageSelect.options[languageSelect.selectedIndex].text.toLowerCase() : 'plaintext';

        document.querySelector('.snippet-code code').textContent = this.value;
        document.querySelector('.snippet-code code').className = 'language-' + selectedLanguage;
        hljs.highlightElement(document.querySelector('.snippet-code code'));
    });

    document.getElementById('language_id').addEventListener('change', function() {
        const codeElement = document.querySelector('.snippet-code code');
        const selectedLanguage = this.value ? this.options[this.selectedIndex].text.toLowerCase() : 'plaintext';
        codeElement.className = 'language-' + selectedLanguage;
        hljs.highlightElement(codeElement);
    });

    // Validación con jQuery Validate (global)
    if (window.jQuery && jQuery.fn.validate) {
        $('#createSnippetForm').validate({
            rules: {
                title: 'required',
                code: 'required',
                category_id: 'required'
            },
            messages: {
                title: 'El título es obligatorio.',
                code: 'El código es obligatorio.',
                category_id: 'Debes seleccionar una categoría.'
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback'
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        hljs.highlightAll();
    });
</script>
@endpush
@endsection
