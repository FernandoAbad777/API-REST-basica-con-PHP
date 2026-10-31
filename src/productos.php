<?php
require_once 'conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($id !== null && !ctype_digit((string)$id)) {
    responder(400, ['error' => 'El id debe ser un número entero positivo']);
}

function formatear(array $p): array {
    return [
        'id'          => (int)$p['id'],
        'nombre'      => $p['nombre'],
        'descripcion' => $p['descripcion'],
        'precio'      => (float)$p['precio'],
        'stock'       => (int)$p['stock'],
        'creado_en'   => $p['creado_en'],
    ];
}

function buscar(PDO $pdo, int $id): ?array {
    $st = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
    $st->execute([$id]);
    $fila = $st->fetch();
    return $fila ? formatear($fila) : null;
}

function leerBody(): array {
    $datos = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datos)) {
        responder(400, ['error' => 'El cuerpo debe ser un JSON válido']);
    }
    return $datos;
}

function validar(array $d): array {
    $errores = [];

    if (!isset($d['nombre']) || !is_string($d['nombre']) || trim($d['nombre']) === '') {
        $errores[] = 'El campo "nombre" es obligatorio';
    } elseif (mb_strlen($d['nombre']) > 100) {
        $errores[] = 'El "nombre" no puede superar 100 caracteres';
    }

    if (!isset($d['precio']) || !is_numeric($d['precio']) || $d['precio'] < 0) {
        $errores[] = 'El campo "precio" es obligatorio y debe ser un número mayor o igual a 0';
    }

    if (!isset($d['stock']) || filter_var($d['stock'], FILTER_VALIDATE_INT) === false || $d['stock'] < 0) {
        $errores[] = 'El campo "stock" es obligatorio y debe ser un entero mayor o igual a 0';
    }

    if (isset($d['descripcion']) && !is_string($d['descripcion'])) {
        $errores[] = 'La "descripcion" debe ser texto';
    }

    return $errores;
}

try {
    switch ($metodo) {

        case 'GET':
            if ($id === null) {
                $filas = $pdo->query('SELECT * FROM productos ORDER BY id')->fetchAll();
                responder(200, array_map('formatear', $filas));
            }
            $producto = buscar($pdo, (int)$id);
            if (!$producto) {
                responder(404, ['error' => "No existe el producto con id $id"]);
            }
            responder(200, $producto);
            break;

        case 'POST':
            if ($id !== null) {
                responder(400, ['error' => 'No envíes id al crear un producto']);
            }
            $datos = leerBody();
            $errores = validar($datos);
            if ($errores) {
                responder(400, ['errores' => $errores]);
            }
            $st = $pdo->prepare(
                'INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)'
            );
            $st->execute([
                trim($datos['nombre']),
                $datos['descripcion'] ?? null,
                $datos['precio'],
                $datos['stock'],
            ]);
            $nuevo = buscar($pdo, (int)$pdo->lastInsertId());
            header('Location: productos.php?id=' . $nuevo['id']);
            responder(201, $nuevo);
            break;

        case 'PUT':
            if ($id === null) {
                responder(400, ['error' => 'Debes indicar el id: productos.php?id=1']);
            }
            if (!buscar($pdo, (int)$id)) {
                responder(404, ['error' => "No existe el producto con id $id"]);
            }
            $datos = leerBody();
            $errores = validar($datos);
            if ($errores) {
                responder(400, ['errores' => $errores]);
            }
            $st = $pdo->prepare(
                'UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?'
            );
            $st->execute([
                trim($datos['nombre']),
                $datos['descripcion'] ?? null,
                $datos['precio'],
                $datos['stock'],
                (int)$id,
            ]);
            responder(200, buscar($pdo, (int)$id));
            break;

        case 'DELETE':
            if ($id === null) {
                responder(400, ['error' => 'Debes indicar el id: productos.php?id=1']);
            }
            if (!buscar($pdo, (int)$id)) {
                responder(404, ['error' => "No existe el producto con id $id"]);
            }
            $st = $pdo->prepare('DELETE FROM productos WHERE id = ?');
            $st->execute([(int)$id]);
            responder(200, ['mensaje' => "Producto $id eliminado correctamente"]);
            break;

        default:
            responder(405, ['error' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    responder(500, ['error' => 'Error interno del servidor']);
}
