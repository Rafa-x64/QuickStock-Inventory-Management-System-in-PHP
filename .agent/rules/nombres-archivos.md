---
trigger: always_on
---

los nombres de los archivos son un poco raros y no siguen mucho orden pero (excluyendo el -view.php de todos los archivos que no sean vistas y esten en view/html/)

para api/client: deben ser el nombre de la vista separado con un guion

para api/server: se debe poner el archivo en la carpeta correspondiente al schema de la base de datos en donde esta... unicamente lleva como nombre el nombre de la tabla en quick_stock.sql y la extension .php

controller/ debe ser el mismo nombre de la vista pero añadiendole un _C.php al final para decir que es un controlador

model: nombre del schema donde estan guardados seguido de . y el nombre de la tabla con la extencion .php

vista:nombre del modulo-nombre-vista-view.php (obligatorio el -view.php para que peuda ser reconocida por la estrucutra completa del proyecto) 