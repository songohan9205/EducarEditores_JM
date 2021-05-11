#  Prueba Educar Editores

El repositorio debe descargarse y colocarlo en la carperta HTDOCS de Xampp, la ruta de accceso es http://localhost/EducarEditores_JM/.
Dado el caso que el servidor apache donde se vaya a ejecutar tenga un puerto diferente al 80, este debe ingresarse después del http: .

**************************
## Configuración local
**************************

El desarrollo se ha realizado en PHP con framework Laravel. El equipo debe contar con Composer. Seguir la siguiente ejecución de comandos:

1. Crear una base de datos en el servidor MySQL local con el nombre "educareditores". Dejarla vacía
2. En el archivo .env se configuran los datos de conexión a la base de datos. Actualmente el usuario es **root** y sin contraseña
3. php artisan migrate => Ejecutar las migraciones para crear las tablas de la base de datos
4. php artisan db:seed => Ejecutar los seeders que crearán registros predeterminados para la tabla **cargo** y **empleado**
5. php artisan serve   => Inicia el servidor. La ruta de acceso a la API quedará siendo *http://127.0.0.1:8000/api*
6. Para pruebas de la API se recomienda el uso de Postman o Insomnia

**************************
## Información del API Rest
**************************

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

5. Transferencia entre usuarios (usuarioTransfiere/usuarioRecibe/transferencia): **http://127.0.0.1:8000/api/transferencia**

**Parámetros de ingreso**
- *usuarioTransfiere* : Número de documento del usuario que realiza la transferencia
- *usuarioRecibe*     : Número de documento del usuario que recibe la transferencia
- *transferencia*     : Monto de la transferencia

6. Registro de nuevos usuario (documento/primer_nombre/segundo_nombre/primer_apellido/segundo_apellido/saldo/cargo_id): **http://127.0.0.1:8000/api/crearUsuario**
###### Servicio adicional a lo solicitado

**Parámetros de ingreso**
- *documento*        : Número de documento del usuario
- *primer_nombre*    : Primer nombre del usuario
- *segundo_nombre*   : Segundo nombre del usuario (no obligatorio)
- *primer_apellido*  : Primer nombre del apellido
- *segundo_apellido* : Segundo nombre del apellido (no obligatorio)
- *saldo*            : Saldo inicial del usuario
- *cargo_id*         : ID del cargo asociado al usuario

7. Lista de cargos: **http://127.0.0.1:8000/api/listarCargos**
###### Servicio adicional a lo solicitado

8. Registro de gastos (documento/gasto/descripcion): **http://127.0.0.1:8000/api/gastos**
###### Servicio adicional a lo solicitado

**Parámetros de ingreso**
- *documento*    : Número de documento del usuario
- *gasto*        : Gasto realizado
- *descripcion*  : Descripción del gasto

### Se agregan validaciones a cada servicio, como:
- Evitar registros duplicados
- Actualización de saldos
- No permitir gastos mayores al saldo actual
- Comprobar existencia del usuario para cada movimiento}
- Otras

*Para conocerlas todas se pueden realizar pruebas sobre la API o directamente revisar el código*