# API REST básica con PHP - Grupo 5

CRUD de productos con PHP, MySQL y Docker, probado con Postman.

Integrantes: Fernando Abad Diaz Castro - 202410040257 , Michael Abad Diaz Castro - 202310040165.

## Cómo ejecutarla
1. Abrir Docker Desktop.
2. En la carpeta del proyecto: `docker compose up -d --build`
3. API: http://localhost:8080/productos.php
4. phpMyAdmin: http://localhost:8081 (usuario root, contraseña root)

## Endpoints
- GET /productos.php - listar productos
- GET /productos.php?id=1 - consultar un producto
- POST /productos.php - insertar
- PUT /productos.php?id=1 - actualizar
- DELETE /productos.php?id=1 - eliminar

## Pruebas
Importar `API_Productos_PHP.postman_collection.json` en Postman.
Las capturas y la explicación están en el informe.
