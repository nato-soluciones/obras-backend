# SPATIE permission v6

[Documentación oficial](https://spatie.be/docs/laravel-permission/v6/introduction)

El objetivo de este archivo es explicar como se implementó el sistema de permisos y algunas ayudas de como usar la librería.

## Descripción

El sistema de permisos funcionará como lista blanca, es decir, que solo se puede acceder al recurso si se tiene el permiso.

## Esquema

El esquema de permisos esta formado por:

-   Usuarios
-   Roles
-   Permisos

**Usuarios:** Cada usuario podrá tener 1 o mas roles  asignado.

**Roles:** Los roles SOLO pueden ser asociados a los usuarios y contienen 1 o más permisos.

**Permisos** Es el nivel mas pequeño (más granular) para identificar si puede realizar o no una acción en el sistema.

## Sintaxis

_Usuarios_ : Es libre, no hay restricciones en los mismos.

_Roles_:

-   columna **name:** Tiene que estar todo en mayúscula; y usar el \_ como separador de palabras.
-   columna **description:** No tiene restricciones, es visual al cliente.

_Permisos_: si es restrictiva, ya que sobre la misma se realiza el chequeo de acceso.

La sintaxis tiene que tener el siguiente formato “nombreEntidad”\_”acción”, si los nombres de las entidades son compuestos usar camelCase en su nombre.

Ejemplos: herramientas_insert, obraAvanceObra_insert, obra_RedirectSystem

Listado de acciones iniciales: list, insert, update, delete, display, export

El navbar tendrá un listado de permisos distinto, el mismo tendrá el siguiente formato: el prefijo "navbar\_" + la entidad a mostrar. Ejemplo: navbar_tools, navbar_budget, etc.

# Instalación

1. Instalar todas las dependencias, **composer install**

1. Aplicar los cambios en la DB, **php artisan migrate**

1. Ejecutar los siguientes seed (UserSeeder y RolesAndPermissionsSeeder) para crear nuevos usuarios genéricos, los roles y permisos iniciales al sistema

    1. Crear nuevos usuarios genéricos
        - **php artisan db:seed --class=UserSeeder** 
    2. Cargar los roles de usuarios, roles funcionales y permisos iniciales
        - **php artisan db:seed --class=RolesAndPermissionsSeeder**

## Comandos Útiles

Obtener los roles del usuario

-   $user->getRoleNames();

Preguntar si un usuario tiene un permiso

-   $user->hasPermissionTo('contacts_insert');

Asignar Roles a usuarios

-   $user->assignRole('OWNER');
-   $user->assignRole(['OWNER', 'admin']);

Crear roles

-   $userRole = Role::create([
        "name" => "OWNER",
        "guard_name" => "api",
        "description" => "Dueño",
    ]);

Asignar permisos a los roles

-   $userRole->givePermissionTo(['contacts_insert', 'contacts_update', 'contacts_delete']);

-   
# APIs
- **Entity_check**: El servicio entity_check?entity=xxxx, devuelve los permisos que tiene el usuario para la entidad pasada, ej: si se pasa fleets, verificará todos permisos que comiencen con "fleets_", como podría ser "fleets_display".

