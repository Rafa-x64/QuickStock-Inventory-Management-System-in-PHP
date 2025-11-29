---
trigger: always_on
---

mi mvc se mezcla con arquitectura cliente servidor por esod ebes saber cuando usar logica php y cuando realziar peticiones y usar js

la logica php se realiza para enviar logica la controldaor, como por ejemplo darle submit a un boton para enviar un formulario de creacion, de edicion o eliminar logicamente algo

js se utiliza para las peticiones y validaciones no para backend ni formularios por eso siempre debes estar pendiente de cuando usar cada cosa

es importante que al crear lo archivos de validacion y peticiones, los guardes de la siguiente manera

peticiones (excluyendo -view.php y agregando .js):
api/client/nombre-vista.js

validaciones (excluyendo -view.php y agregando .js):
view/js/nombre-vista.js