# Últimos Cambios

> Registro de cambios de este proyecto. Formato: versión, fecha, descripción y archivos afectados.

---

## v1.6.0 — 2026-09-15 — Publicar colección = publicar todos sus snippets

### Descripción general
- Se confirmó que thiscodeworks **no tiene página pública de colecciones**: `POST /api/collections` devuelve un `_id` pero su ruta `/collection/{id}` siempre responde "post does not exist" (colecciones fantasma). Por eso se abandona la creación de colecciones por API.
- El botón "Publicar colección" ahora publica **cada snippet de la colección** en thiscodeworks.com (que sí genera páginas públicas reales). Los snippets ya publicados se **reutilizan** (no se re-suben), así que es eficiente e idempotente.
- Si falla algún snippet, el proceso continúa y se informa un resumen (`X publicados, Y ya existían, Z fallidos`), sin abortar con error. Se audita en `categoria` con el desglose.
- Badge de progreso por colección en el listado (`X/Y publicados`) y en la ficha; el botón desaparece cuando está completa.

### Archivos
- `app/Models/Category.php` — relación `publishedSnippets()`.
- `app/Http/Controllers/CategoryController.php` — `publish()` publica los snippets pendientes; `index`/`show` con conteos.
- `routes/web.php` — `POST categories/{category}/publish`.
- `resources/views/categories/index.blade.php`, `show.blade.php` — badge de progreso + botón.

---

## v1.5.0 — 2026-09-15 — Indicador de estado thiscodeworks + verificación antes de marcar publicada

### Descripción general
- **Bombillito de estado** en el navbar (visible para usuarios autenticados): consulta `GET /thiscodeworks/status` cada 30 s y muestra verde "thisCodeWorks online" o rojo "thisCodeWorks caído". El endpoint usa `ThisCodeWorksService::isOnline()` (GET al feed público, cacheado 60 s).
- **Verificación antes de marcar "Publicada"**: la API de colecciones devuelve un `_id` aunque la página pública jamás exista ("post does not exist" con HTTP 200). Se comprobó con varias colecciones y se limpió el id fantasma de "Depuración". Conclusión → la publicación de colecciones se rediseñó en v1.6.0.

### Archivos
- `app/Services/ThisCodeWorksService.php` — `isOnline()`.
- `app/Http/Controllers/ThisCodeWorksStatusController.php` — nuevo (invokable).
- `routes/web.php` — `GET thiscodeworks/status`.
- `resources/views/layouts/app.blade.php` — bombillito en navbar + polling.

---

## v1.4.0 — 2026-09-15 — API key por usuario (perfil)

### Descripción general
- Cada usuario puede guardar su propia API key de thiscodeworks desde su perfil (card "Integración thiscodeworks"). Al publicar, el servicio usa la clave del usuario; si no tiene una, cae a la global del `.env`.
- La clave se guarda como campo `thiscodeworks_api_key` en `users`, se oculta (`$hidden`) y nunca se muestra en el input (solo indica "clave guardada" con placeholder). En blanco al guardar = se mantiene la actual.
- La card muestra la **"API Key en uso"** enmascarada (`••••••••` + últimos 4 caracteres) con su contexto: "Es tu clave personal" o "Es la clave global del servidor". El ojo para revelar solo aparece con clave personal (la global nunca viaja en el HTML).
- Audita con "API key de thiscodeworks actualizada." (sin volcar la clave en los datos).
- Verificado: guardar (302 + flash), dejar en blanco (mantiene), clave en uso enmascarada visible en ambos escenarios, clave global no filtrada en el HTML, override por usuario confirmado vía servicio (developer usa la suya; admin la global), auditoría registrada.

### Archivos
- `database/migrations/2025_10_28_000002_add_thiscodeworks_api_key_to_users_table.php`.
- `app/Models/User.php` — fillable + hidden de `thiscodeworks_api_key`.
- `app/Services/ThisCodeWorksService.php` — `apiKey()` prioriza la del usuario autenticado.
- `app/Http/Requests/ApiKeyUpdateRequest.php` — nuevo.
- `app/Http/Controllers/ProfileController.php` — `apiKeyUpdate()` + estado de integración en `edit()`.
- `routes/web.php` — `POST profile/thiscodeworks-api-key`.
- `resources/views/profile/edit.blade.php` — card de API key con "API Key en uso" enmascarada, toggle de visibilidad (solo clave personal).

---

## v1.3.0 — 2026-09-15 — Auto-publicación al guardar (snippets y colecciones)

### Descripción general
- Al **crear** un snippet o una colección se sube automáticamente a la API de thiscodeworks, controlado por la casilla "Publicar en thiscodeworks" (marcada por defecto) en los formularios de creación. Con la casilla desmarcada solo se guarda localmente.
- La casilla usa un `<input type="hidden" value="0">` de respaldo para que la desmarcación envíe `0` (y no el valor por defecto). Se valida como `sometimes|boolean`.
- Editar no re-publica: la API es solo de creación, así que las modificaciones locales no se sincronizan (evita duplicados).
- Verificado end-to-end: casilla ON → `thiscodeworks_id/url` guardados + auditoría `publicar` + aparición en el feed público; casilla OFF → sin llamada a la API. Edición de un snippet publicado no cambia su `thiscodeworks_id` ni añade auditorías.

### Archivos
- `app/Http/Requests/StoreSnippetRequest.php`, `StoreCategoryRequest.php` — regla `publish_to_api`.
- `app/Http/Controllers/SnippetController.php` — la publicación al crear queda sujeta a `enabled() && publish_to_api`.
- `app/Http/Controllers/CategoryController.php` — `store()` ahora publica la colección (id remoto + url + auditoría) cuando aplica.
- `resources/views/snippets/create.blade.php`, `categories/create.blade.php` — casilla "Publicar en thiscodeworks".

---

## v1.2.0 — 2026-09-15 — Publicación manual (snippets + colecciones) y respaldos verificados

### Descripción general
- Botón "Publicar en thiscodeworks" por snippet (index y show) y por colección (index y show). Las colecciones/categorías ahora guardan `thiscodeworks_id`/`thiscodeworks_url` (migración nueva) y muestran badge "Publicada" con enlace.
- Endpoints descubiertos para colecciones: `GET api/thiscodeworks.com/api/collections` (con key) y `www.../api/collections` (público) devuelven `[]` (no listan las del usuario); `POST api.thiscodeworks.com/api/collections` con `{"name":"..."}` crea la colección (devuelve `_id`); NO hay borrado, renombrado ni lectura individual por API. Requiere campo `name` (no `title`).
- Respaldos corregidos: los seeders generados ahora conservan los IDs originales y usan `DB::table()->updateOrInsert()` (las claves foráneas siguen siendo válidas), `tags` se exporta como JSON, `code` queda vacío (no falta) cuando el respaldo es "sin código", y el seeder de usuarios exporta `role` y timestamps.
- Verificación end-to-end de respaldos: zip real por HTTP (tipos `snippets` y `full`), `php -l` de todos los archivos generados, ejecución real de los 4 seeders con conteos idénticos a la BD (18/10/4/1), idempotencia confirmada (sin duplicados).

### Archivos
- `database/migrations/2025_10_28_000001_add_thiscodeworks_to_categories_table.php` — columnas en categories.
- `app/Models/Category.php` — fillable ampliado.
- `app/Services/ThisCodeWorksService.php` — `publishCollection()`.
- `routes/web.php` — `POST snippets/{snippet}/publish`, `POST categories/{category}/publish`.
- `app/Http/Controllers/SnippetController.php`, `CategoryController.php` — métodos `publish` + auditoría `publicar`.
- `resources/views/snippets/index|show.blade.php`, `categories/index|show.blade.php` — botones publicar, badges y alertas flash.
- `app/Services/BackupService.php` — seeders de respaldo corregidos (ids, `updateOrInsert`, tags JSON, code, role).

---

## v1.1.0 — 2026-09-15 — Verificación end-to-end de la integración thiscodeworks + auditoría completa

### Descripción general
- Se descubrió y verificó en vivo el contrato real de la API de thiscodeworks: la escritura vive en **`https://api.thiscodeworks.com/api/snippets`** (host distinto a www).
- Confirmado `POST` con headerse `Authorization: Bearer <api_key>` y JSON `{title, code, tags[]}` → **201 "Snippet saved"**. La API es **solo de creación** (PATCH/PUT/DELETE/GET individual → 404; límite 30 req/min).
- `ThisCodeWorksService` implementado con degradación segura: deshabilitado o ante error remoto, la operación local nunca falla (solo se registra en log). Al crear un snippet se publica en thiscodeworks (si `THISCODEWORKS_ENABLED=true`) y se persiste `thiscodeworks_id`/`thiscodeworks_url` buscando el id en el listado público (newest-first).
- Módulo de auditoría completo y verificado: tabla `auditorias` (jsonb `datos_antes`/`datos_despues`), registro en login/logout/registro y en todos los CRUD (crear/actualizar/eliminar), middleware `role:admin`, ruta `/auditorias` con filtros (acción, entidad, usuario, IP, rango de fechas) y paginación Bootstrap 5.
- Se ejecutó `migrate:fresh --seed` en PostgreSQL (destructivo, aprobado): datos demo regenerados (3 usuarios, 18 categorías, 10 lenguajes, 6 snippets, auditorías demo; admin = `admin@example.com` / `password123`).
- Corregido guardado de `tags` como jsonb: el mutator ahora persiste el JSON codificado (PostgreSQL no acepta arrays PHP crudos).
- Smoke test HTTP completo: login (302), CRUD snippets (302/200), `/auditorias` bloquea no-admin (403), todas las vistas (200), endpoints API locales (200).
- Fase de simplificación: grid de snippets estilo thiscodeworks (búsqueda en vivo, filtros por colección/lenguaje, chips de tags, botón copiar, confirmación de borrado con SweetAlert2).

### Archivos afectados en esta versión
- `app/Services/ThisCodeWorksService.php` — nuevo servicio con `enabled()`, `publish()`, `remoteUrl()`, `findRemoteId()` (base `api.thiscodeworks.com`).
- `app/Services/BackupService.php` — lógica de backup extraída de `ProfileController`.
- `app/Services/AuditoriaService.php`, `app/Models/Auditoria.php` — módulo de auditoría.
- `app/Http/Middleware/CheckRole.php` + alias `role` en `bootstrap/app.php`.
- `app/Http/Controllers/AuditoriaController.php`, `resources/views/auditorias/index.blade.php` — panel admin con filtros.
- Controllers delgados con Form Requests: `Snippet`, `Category`, `Language`, `Profile` (+ métodos `passwordUpdate`, `backupStore`, `stats` eliminados los 500 ocultos).
- `Auth/LoginController`, `Auth/RegisterController` — auditoría de login/logout/registro.
- `app/Models/Snippet.php` — SoftDeletes, `tags` cast json + mutator, campos thiscodeworks.
- `.env` / `.env.example` — `THISCODEWORKS_API_KEY` (solo `.env`), `THISCODEWORKS_API_URL`, `THISCODEWORKS_BASE_URL`.
- `resources/views/snippets/` — index como grid con búsqueda/filtros/chips, create/edit con `description`, `tags`, lenguaje opcional, `url()->previous()` y jQuery Validate.
- `database/seeders/` — `SnippetsTableSeeder`, `AuditoriasTableSeeder`, seeder de usuarios con rol admin.

### API thiscodeworks (contrato verificado)
- Leer (público): `GET https://www.thiscodeworks.com/api/snippets` → JSON `[{_id,title,code,tags,...}]`, newest-first, ~50 por página.
- Escribir (key): `POST https://api.thiscodeworks.com/api/snippets` con `Authorization: Bearer <api_key>` y JSON `{title, code, tags[]}` → 201 "Snippet saved".
- No existe actualización ni borrado por API. La key va SOLO en `.env`.
- Nota: durante las pruebas quedaron publicados dos snippets «x»/«y» en el feed público (no se pueden borrar por API); borrarlos desde la cuenta en www.thiscodeworks.com si resultan molestos.

---

## v1.0.0 — 2026-09-15 — Aplicación de reglas.md + integración thiscodeworks + simplificación

### Descripción general
- Se creó `reglas.md` con las reglas de construcción de proyectos Laravel.
- Se aplicaron las reglas al proyecto (migraciones I-P-R-A-T, Form Requests, Servicios, paleta de colores, auditoría, validación JS).
- Se simplificó el proyecto al estilo thiscodeworks.com (grid de tarjetas de snippets + tags).
- Se preparó la integración con la API de thiscodeworks.com usando la API key proporcionada (escritura vía `POST /api/snippets`).

### Archivos afectados
- `reglas.md` — nueva reglas base (documentación).
- `ultimosCambios.md` — este archivo.
- `resources/views/layouts/app.blade.php` — paleta de colores CSS (variables), navbar gradiente, jQuery Validate/Mask + SweetAlert2, botones de navegación.
- `routes/web.php`, `routes/api.php` — rutas de auditoría, API registrada, mover closures a controladores.
- `bootstrap/app.php` — registrar `routes/api.php`, middleware `role:admin`.
- `app/Http/Controllers/Api/SnippetApiController.php` — corregido (estaba dañado con doble `<?php`).
- `app/Http/Controllers/` — controladores delgados (Snippet, Category, Language, Home, Profile, Auditoria, Api).
- `app/Http/Requests/` — nuevos Form Requests de validación.
- `app/Services/` — `ThisCodeWorksService`, `BackupService`, `AuditoriaService`.
- `app/Models/` — `Auditoria`, mejoras en `Snippet`, `User` (roles), `Category`, `Language`.
- `database/migrations/` — reorden I-P-R-A-T, fusión de `add_user_id` en `create_snippets`, columnas `tags`, `thiscodeworks_id/url`, `role` en users, tabla `auditorias`, índices.
- `database/seeders/` — nuevos `SnippetsSeeder`, `AuditoriasSeeder`, seeder de usuarios con rol admin.
- `resources/views/` — vistas semánticas, botones Cancelar/Volver con `url()->previous()`, validación jQuery, grid de snippets estilo thiscodeworks.
- `.env` / `.env.example` — claves de integración: `THISCODEWORKS_ENABLED`, `THISCODEWORKS_API_KEY`.
- `config/services.php` — bloque de configuración de thiscodeworks.

### API thiscodeworks (investigación)
- Endpoint de lectura público: `GET https://www.thiscodeworks.com/api/snippets` (JSON, sin auth).
- Endpoint de escritura: `POST https://api.thiscodeworks.com/api/snippets` (API key; `Authorization: Bearer <key>`).
- La key se guarda SOLO en `.env` (nunca en el repo).

---

## Historial

| Versión | Fecha       | Descripción |
|---------|-------------|-------------|
| v1.6.0  | 2026-09-15  | Publicar colección = publicar todos sus snippets (reutiliza ya publicados, resumen sin errores, badge X/Y) |
| v1.5.0  | 2026-09-15  | Bombillito de estado thiscodeworks en navbar + verificación (colecciones no tienen página pública) |
| v1.4.0  | 2026-09-15  | API key por usuario (perfil), "API Key en uso" enmascarada |
| v1.3.0  | 2026-09-15  | Auto-publicación al guardar snippets/colecciones con casilla "Publicar en thiscodeworks" (editar no re-publica) |
| v1.2.0  | 2026-09-15  | Publicación manual de snippets/colecciones en thiscodeworks + respaldos corregidos y verificados end-to-end |
| v1.1.0  | 2026-09-15  | Integración thiscodeworks verificada end-to-end (escritura en api.thiscodeworks.com), auditoría completa con rol admin, tag jsonb corregido, smoke tests HTTP |
| v1.0.0  | 2026-09-15  | Aplicación de reglas, auditoría, integración thiscodeworks y simplificación estilo thiscodeworks |