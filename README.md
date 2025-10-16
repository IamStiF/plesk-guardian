# Plesk Guardian — Starter (MVP)

Este repositorio inicia una extensión **Plesk** lista para subir a tu Git y empaquetar en tu pipeline.

## ¿Qué incluye?
- Detección básica de instalaciones **WordPress** buscando `wp-config.php` dentro de la ruta de vhosts.
- Página de **Dashboard** (`IndexController`) con tabla de instalaciones.
- Página de **Ajustes** (`SettingsController`) para cambiar la ruta de vhosts (por defecto `/var/www/vhosts`).
- Stubs para acciones de seguridad futuras (`ActionsController`).
- **GitHub Actions** para empaquetar ZIP en cada push/tag (puedes adaptarlo a tu CI).

## Estructura
```
meta.xml
plib/
  controllers/
    IndexController.php
    SettingsController.php
    ActionsController.php
  library/
    Guardian/
      Scanner.php
      Commander.php
  views/
    scripts/
      index/
        index.phtml
      settings/
        index.phtml
htdocs/
  css/
    app.css
.github/
  workflows/
    build.yml
.gitignore
LICENSE
```

## Desarrollo local
1. Edita código en `plib/` y vistas en `plib/views/`.
2. No necesitas Composer para el MVP.
3. **No desarrolles en producción**.

## Instalar manualmente en Plesk
Empaqueta en ZIP con `meta.xml` en la raíz del ZIP:
```bash
zip -r plesk-guardian.zip . -x '*.git*' -x 'dist/*' -x '.github/*'
```
Luego en Plesk: **Extensiones → Mis extensiones → Cargar extensión**.

## Variables importantes
- Ruta de vhosts (por defecto): `/var/www/vhosts` (puedes cambiarla en **Ajustes**).

## Roadmap sugerido
- Integrar WP-CLI/WP Toolkit para leer versiones, plugins y estados.
- Añadir acciones masivas: forzar HTTPS, permisos, auto-actualizaciones.
- Añadir tareas programadas (cron) para auditorías.

## Licencia
MIT
