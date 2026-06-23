# Donapp — Laravel 11

Plataforma de donaciones de la Fundación CES Waldorf.
Migrado desde PHP estructurado → POO → **Laravel 11** con Blade, Eloquent ORM y autenticación propia por sesión.

---

## Requisitos

| Herramienta | Versión mínima |
|-------------|----------------|
| PHP         | 8.2+           |
| Composer    | 2.x            |
| MySQL       | 5.7+ / 8.x     |

---

## Instalación paso a paso

```bash
# 1. Entrar al proyecto
cd donapp

# 2. Copiar entorno
cp .env.example .env

# 3. Instalar dependencias
composer install

# 4. Generar clave
php artisan key:generate

# 5. Configurar BD en .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 6. Crear base de datos en MySQL
#    CREATE DATABASE donapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 7. Ejecutar migraciones
php artisan migrate

# 8. Sembrar datos iniciales
php artisan db:seed

# 9. Iniciar servidor
php artisan serve
```

Abre: http://localhost:8000

---

## Credenciales de prueba

| Rol           | Email               | Contraseña  |
|---------------|---------------------|-------------|
| Administrador | admin@donapp.co     | admin123    |
| Asistente     | asis@donapp.co      | asis123     |
| Donante       | donante@donapp.co   | donante123  |

---

## Configurar correo (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu@gmail.com
MAIL_PASSWORD="tu_app_password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu@gmail.com
MAIL_FROM_NAME="Donapp"
```

---

## Estructura

```
app/Http/Controllers/   → PublicController, AuthController, AdminController, AsisController, UserController
app/Http/Middleware/    → CheckRole (protección por rol)
app/Mail/               → NotificacionEstado, RecuperacionContrasena
app/Models/             → Usuario, Donacion, Solicitud, Categoria, Evento, Publicacion, ProgramadorEventos
database/migrations/    → 5 migraciones (todas las tablas)
database/seeders/       → DatabaseSeeder (usuarios y categorías de prueba)
resources/views/        → layouts/, auth/, public/, admin/, asis/, user/, partials/, emails/
routes/web.php          → Rutas agrupadas por rol con middleware
public/assets/          → CSS, JS e imágenes originales
```

---

## Características Laravel utilizadas

- Eloquent ORM con relaciones (belongsTo, hasMany, belongsToMany)
- Migraciones con foreign keys y BLOB para imágenes
- Middleware CheckRole con re-validación en BD
- Mailables con vistas Blade
- Validaciones con request->validate()
- DB::transaction() para operaciones críticas
- Rutas nombradas agrupadas por prefijo y middleware
- Blade con layouts, partials @include y directivas
- SweetAlert2 integrado en layout base
