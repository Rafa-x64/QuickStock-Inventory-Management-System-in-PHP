---
trigger: always_on
---

los ID´s de los inputs de la vista deben ser logicos no puede quedar ningun campo sin id o ningun elemento sin id (si va se necesetia manipularlo en js).

Los nombres de los ID´s deben ser logicos y relacionado a lo que se quiere obtener.

ej: si tengo dos campos de nombre

<label for="">Nombre de usuario</label>
<input type="text" name="" id="" placeholder="nombre de usuario">

<label for="">Primer Nombre</label>
<input type="text" name="" id="" placeholder="primer nombre">

ambos no pueden llamarse nombre... deben estar separados por un gion bajo

el primero seria id="nombre_usuario" y el segundo seria id="primer_nombre"

Una convencion que suelo usar es ponerle guion bajo para separar palabras y colocarle el mismo nombre que el name y viceversa el name debe tener el mismo nombre del id

Otra cosa al tener en cuenta es que todo debe ser en minuscula
