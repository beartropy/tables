---
description: Publicar una nueva versión del paquete (actualizar changelog, version, commit, tag)
---

1. Analizar los cambios recientes y determinar el nuevo número de versión (patch, minor, o major) basándose en Semantic Versioning.
2. Leer el archivo `composer.json` para obtener la versión actual.
3. Actualizar `CHANGELOG.md`:
   - Agregar una nueva entrada para la nueva versión con la fecha de hoy.
   - Listar los cambios realizados desde la última versión.
4. Actualizar `composer.json`:
   - Cambiar el campo `"version"` al nuevo número de versión.
   - Asegurarse de que coincida con la entrada en `CHANGELOG.md`.
5. Mostrar los cambios realizados en ambos archivos al usuario para su revisión (usar `render_diffs` o `read_file`).
6. Ejecutar los comandos de git para guardar los cambios:
   ```bash
   git add .
   git commit -m "chore: release version <NUEVA_VERSION>"
   ```
   *Nota: Reemplazar `<NUEVA_VERSION>` con el número real, ej: 1.0.2*
7. Subir los cambios al repositorio remoto:
   ```bash
   git push
   ```
8. Crear y subir el tag de la versión:
   ```bash
   git tag <NUEVA_VERSION>
   git push origin <NUEVA_VERSION>
   ```
   *Nota: Reemplazar `<NUEVA_VERSION>` con el número real, ej: 1.0.2*
