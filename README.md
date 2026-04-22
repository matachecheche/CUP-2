# PROYECTO API

2. Instalar las dependencias

```
composer install
```

3. Configurar las variables de entorno tomando la plantilla `.env.example` y clonar a un archivo `.env`
   configurarlo de preferencia MySql
4. Cambiar las variables de entorno
5. Generar la llave

```
php artisan key:generate
```

5. Configurar el .env a la base de datos ya sea MySql o la uses

6. Ejecutar las migraciones

```
php artisan migrate
```

```
php artisan migrate:fresh --seed
```

7. Instalar los modulos de node

```
npm install
```

8. Construir la app

```
npm run build
```

```

9. Correr la app

```

php artisan serve

```

```
