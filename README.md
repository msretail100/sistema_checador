# Reloj Checador

Aplicación de control de asistencia con Laravel 12, Blade y Bootstrap. El sistema usa roles nativos (`root`, `admin`, `worker`) y no depende de la hora del navegador para registrar asistencia.

## Requisitos

- PHP 8.2 o superior, Composer y las extensiones PHP habituales de Laravel (incluidas PDO y ZIP).
- Base de datos soportada por Laravel (el proyecto inicia con SQLite).
- HTTPS en producción: los navegadores solo exponen geolocalización en un contexto seguro.

## Instalación

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
```

Configura la conexión de base de datos, `APP_URL` y `APP_TIMEZONE` en `.env`. Antes de sembrar el usuario Root, define credenciales propias:

```dotenv
ROOT_NAME="Superadministrador"
ROOT_USERNAME="root"
ROOT_PASSWORD="reemplaza-por-una-frase-segura-de-al-menos-12-caracteres"
```

Después ejecuta:

```powershell
php artisan migrate --seed
php artisan serve
```

En producción, usa secretos de alta entropía y no reutilices las credenciales de ejemplo. La base de datos guarda contraseñas con hash; `ROOT_PASSWORD` solo se utiliza al crear o actualizar al Root durante el seeding.

## Roles

- **Root:** administración de todos los usuarios, cambio de contraseñas, reportes diarios/mensuales/anuales y monitoreo.
- **Administrador:** alta y habilitación/deshabilitación de trabajadores, reportes diarios/mensuales y monitoreo.
- **Trabajador:** checada personal de entrada y salida.

El usuario se redirige al panel de su rol al iniciar sesión. Las rutas comprueban rol y estado activo en el servidor.

## Integridad de las checadas

- `hora_servidor` se genera con `now()` en Laravel y no se acepta desde el formulario.
- `ip_address` se toma de la petición HTTP en el servidor.
- El navegador solo envía latitud/longitud; el servidor valida sus rangos y alterna una única entrada y salida por día.
- La transacción bloquea el registro del usuario mientras determina la siguiente checada.
- Para usar la aplicación detrás de un proxy inverso, configura únicamente las direcciones CIDR de tus proxies de confianza en la configuración de middleware de Laravel. No confíes en todos los proxies: esto podría permitir falsificar la IP.
- La geolocalización del navegador depende del permiso del usuario y no es una prueba infalible de ubicación física; para controles antifraude estrictos se requiere validación adicional.

## Validación

```powershell
php artisan test
```
