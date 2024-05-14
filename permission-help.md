# SPATIE permission v6

[Documentación oficial](https://spatie.be/docs/laravel-permission/v6/introduction)

El objetivo de este archivo es explicar como se implementó el sistema de permisos y algunas ayudas de como usar la librería.

## Descripción

El sistema de permisos funcionará como lista blanca, es decir, que solo se puede acceder al recurso si se tiene el permiso.

## Esquema

El esquema de permisos esta formado por:

-   Usuarios
-   Roles de usuarios
-   Roles funcionales
-   Permisos

**Usuarios:** Cada usuario podrá tener 1 o mas roles de usuarios asignado.

**Roles de usuario:** Los roles de usuario son los roles que SOLO pueden ser asociados a los usuarios y, que contienen 1 o más roles funcionales.

**Roles funcionales:** Los roles funcionales son los roles que SOLO pueden ser asociados a los roles de usuario y, que contienen 1 o más permisos. Funcionan como agrupadores de permisos.

**Permisos** Es el nivel mas pequeño (más granular) para identificar si puede realizar o no una acción en el sistema.

## Sintaxis

_Usuarios_ : Es libre, no hay restricciones en los mismos.

_Roles de usuario_:

-   columna **name:** Tiene que estar todo en mayúscula; y usar el \_ como separador de palabras.
-   columna **description:** No tiene restricciones, es visual al cliente.

_Roles funcionales_:
Se recomienda que tenga nombres significativos a los permisos que agrupa, para que al momento de la configuración sea más fácil la misma.

-   columna **name:** Tiene que estar todo en minúscula y comenzar con "functional\_"
-   columna **description:** No tiene restricciones, es visual al cliente.

_Permisos_: si es restrictiva, ya que sobre la misma se realiza el chequeo de acceso.

La sintaxis tiene que tener el siguiente formato “nombreEntidad”\_”acción”, si los nombres de las entidades son compuestos usar camelCase en su nombre.

Ejemplos: herramientas_insert, obraAvanceObra_insert.

Listado de acciones iniciales: list, insert, update, delete, display, export

El navbar tendrá un listado de permisos distinto, el mismo tendrá el siguiente formato: el prefijo "navbar\_" + la entidad a mostrar. Ejemplo: navbar_tools, navbar_budget, etc.

# Instalación

1. Instalar todas las dependencias, **composer install**

1. Aplicar los cambios en la DB, **php artisan migrate**

1. Ejecutar los siguientes seed (UserSeeder y RolesAndPermissionsSeeder) para crear nuevos usuarios genéricos y cargar los roles de usuarios, roles funcionales y permisos iniciales al sistema

    1. Crear nuevos usuarios genéricos
        - **php artisan db:seed --class=UserSeeder** 
    2. Cargar los roles de usuarios, roles funcionales y permisos iniciales
        - **php artisan db:seed --class=RolesAndPermissionsSeeder**
    3. Crea las relaciones iniciales entre los usuarios genéricos y roles de usuario, los roles funcionales y permisos, y le asigna al rol de usuario "dueño" todos los permisos.
        - **php artisan db:seed --class=RelationalRolesPermissionsSeeder**

## Comandos Útiles

Obtener los roles del usuario

-   $user->getRoleNames();

Preguntar si un usuario tiene un permiso

-   $user->hasPermissionTo('contacts_insert');

Asignar Roles a usuarios

-   $user->assignRole('OWNER');
-   $user->assignRole(['OWNER', 'admin']);

Crear roles de usuario

-   $userRole = Role::create([
    "name" => "OWNER",
    "guard_name" => "api",
    "description" => "Dueño",
    ]);

Crear roles funcionales

-   $functionalRole = Role::create([
    "name" => "functional_navbar_full",
    "guard_name" => "api",
    "description" => "Menú Completo",
    ]);

Asignar permisos a los roles funcionales

-   $functionalRole->givePermissionTo(['ver-usuarios', 'editar-usuarios', 'eliminar-usuarios']);

Obtener todos los roles de usuario

-   

Obtener todos los roles funcionales

-   

# FUNCIONES IMPLEMENTADAS

## Web

1. Creación de usuarios
2. Asignación de roles de usuario a usuarios

## API

1. Listado de Roles de usuario
2. Listado de Roles funcionales
3. Listado de Permisos

4. Alta de roles funcionales en Roles de usuario
5. Baja de roles funcionales en Roles de usuario
6. Alta de permisos en Roles funcionales
7. Baja de permisos en Roles funcionales

## Seeder

1. Alta de Roles de usuario
2. Alta de Roles de funcionales
3. Alta de Permisos
4. Alta de Relaciones iniciales entre Permisos y roles funcionales
5. Alta de Relaciones iniciales entre roles de usuario y roles funcionales (solo rol Dueño)

## DB

Todas las consultas de roles y permisos
