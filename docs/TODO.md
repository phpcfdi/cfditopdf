# phpcfdi/cfditopdf To Do List

Intro: Al completar una tarea pasarla de *Planeada* a *Finalizada*. Escribir fecha o versión del cambio.   

# Planeadas

- Revisar que <https://github.com/spipu/html2pdf/issues/530> esté solucionado para desactivar "permitir fallos"
  en PHP 7.4 porque `spipu/html2pdf` tiene un bug.
- Cambiar a PHP 7.2 o mayor.
- Al cambiar de versión de PHP, actualizar los métodos que retornan void a que explícitamente lo hangan.
- Modificar `NodeInterface<NodeInterface>` para cuando `eclipxe/cfdiutils` ya lo incluya por sí mismo.
- Catálogos para expresar las claves.
- Impresión genérica de complementos.
- Crear nuevos métodos para generar el archivo PDF.

## Finalizadas

### En versión 0.3.2 o anteriores:
 
- Otros nodos de los conceptos.
- Cadena de origen del TFD.
- Liga del código QR.
- Formato del código QR.
- Construcción de objetos.
- Depend on new version of CfdiUtils.
- Move PdfToText to a different library and require it only on development.
- Improve testing.
- When macfja/phar-builder reaches phive move it from composer script requiere run remove to phive dependence.
