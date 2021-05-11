###################
#  Prueba Educar Editores
###################

El repositorio debe descargarse y colocarlo en la carperta HTDOCS de Xampp, la ruta de accceso es http://localhost/EducarEditores_JM/.
Dado el caso que el servidor apache donde se vaya a ejecutar tenga un puerto diferente al 80, este debe ingresarse después del http: .

*******************
## Configuración local
*******************

El desarrollo se ha realizado en PHP con framework Laravel. El equipo debe contar con Composer. Seguir la siguiente ejecución de comandos:

1. Crear una base de datos en el servidor MySQL local con el nombre "educareditores". Dejarla vacía
2. En el archivo .env se configuran los datos de conexión a la base de datos. Actualmente el usuario es **root** y sin contraseña
3. php artisan migrate => Ejecutar las migraciones para crear las tablas de la base de datos
4. php artisan db:seed => Ejecutar los seeders que crearán registros predeterminados para la tabla **cargo** y **empleado**
5. php artisan serve   => Inicia el servidor. La ruta de acceso a la API quedará siendo *http://127.0.0.1:8000/api*

*******************
## Información del API Rest
*******************

La API rest con los siguientes servicios:

1. Listar todos los usuarios registrados: **http://127.0.0.1:8000/api/listarUsuarios**
2. Listar usuario por filtro (número de documento): **http://127.0.0.1:8000/api/filtroUsuario**

**Parámetros de ingreso**
- *documento* : Número de documento de un usuario registrado

3. Listar usuario por filtro doble (campo/dato): **http://127.0.0.1:8000/api/filtroDoble**
###### Servicio adicional a lo solicitado

**Parámetros de ingreso**
- *campo* : Nombre del campo por el cual se desea aplicar filtro
- *dato*  : Valor del campo por el cual se va a filtrar (funciona con un like '%dato%')

4. Recarga de saldo (documento/recarga): **http://127.0.0.1:8000/api/recarga**

**Parámetros de ingreso**
- *documento* : Número de documento del usuario al que se realiza la descarga
- *recarga*   : Valor de la recarga a realizar


***************
Especificaciones
***************

Se realiza en lenguaje PHP con framework Codeigniter, manejo de Ajax para peticiones y Bootsrtap 4 para interfaz
