<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Autoparte.php';

$autoparteModel = new Autoparte();

// Exactamente como en el controlador
$filtros = [
    'buscar' => '',
    'categoria_id' => '',
    'marca' => '',
    'modelo' => '',
    'anio' => '',
    'precio_min' => '',
    'precio_max' => '',
    'estado' => '',
    'stock_bajo' => false,
    'orden' => 'fecha_creacion',
    'direccion' => 'DESC',
    'limite' => 20,
    'offset' => 0
];

echo "=== Filtros del controlador exactos ===\n";
try {
    $autopartes = $autoparteModel->obtenerTodos($filtros);
    echo "Autopartes: " . count($autopartes) . "\n";
    if (count($autopartes) > 0) {
        echo "Primera: " . $autopartes[0]['nombre'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Verificar contarTodos
echo "\n=== contarTodos ===\n";
try {
    $total = $autoparteModel->contarTodos($filtros);
    echo "Total: " . $total . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
