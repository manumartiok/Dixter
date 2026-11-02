# Reglas del proyecto

## Listado de archivos trabajados

Cada vez que termines de trabajar un prompt, cerrá tu respuesta final con una sección **"Archivos trabajados"** que liste todos los archivos que creaste, modificaste o eliminaste durante ese prompt.

- Aplica a cualquier tipo de archivo: vistas Blade, controladores, modelos, rutas, migraciones, seeders, requests, middlewares, configuración, JS/CSS, tests, documentación, etc.
- Formato: lista con un link relativo por archivo y, entre paréntesis, la acción (creado / modificado / eliminado) y una frase corta de qué cambió. Agrupá por tipo si son muchos (Vistas, Controladores, Modelos, Rutas, Migraciones, Otros).

  ```
  ### Archivos trabajados
  **Vistas**
  - [resources/views/usuarios/index.blade.php](resources/views/usuarios/index.blade.php) (modificado): agregué filtro por rol.

  **Controladores**
  - [app/Http/Controllers/UsuarioController.php](app/Http/Controllers/UsuarioController.php) (modificado): el método `index` filtra por rol.

  **Rutas**
  - [routes/web.php](routes/web.php) (modificado): nueva ruta `usuarios.exportar`.
  ```

- Incluí también los archivos que cambiaron de forma indirecta (p. ej. generados por un comando de `artisan` o `npm`), salvo `vendor/`, `node_modules/` y `storage/`.
- Si en el prompt no se cambió ningún archivo, escribí "Archivos trabajados: ninguno".
- No listes archivos que solo leíste o consultaste sin modificar.
