---
trigger: always_on
---

todo se ejecuta dentro de index (a excepcion de la carpeta api/server, api/client ya que son endpoints con los que se comunica js), por ende todas las rutas deberas ser desde la perspectiva de index obligatoriamente sin utilizar __DIR__ ya que no es necesario. los modelos, los controladores, y las vistas deberan tener rutas relativas a src/index.php. 

la carpeta api si debe tener rutas absolutas y los archivos js que se comunican con llos endpoints en api/server si deben incluir __DIR__ o una manera de incluir el archivo de origen 

esto con la finalidad de reducir errore sde inclucion y mantener un estandar en las rutas para que todo se vea esteticom organizado y coherente