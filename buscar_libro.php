<?php
include("con_db.php");

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($query)) {
    // Verifica la conexión a la base de datos
    if ($conex->connect_error) {
        die("Error de conexión: " . $conex->connect_error);
    }

    // Prepara la consulta SQL
    $stmt = $conex->prepare("SELECT ID_Libro, titulo, autor, existencia FROM Libros WHERE titulo LIKE ? OR autor LIKE ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conex->error);
    }

    // Asigna el término de búsqueda
    $searchTerm = "%$query%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);

    // Ejecuta la consulta
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }

    // Obtiene los resultados
    $result = $stmt->get_result();
    $libros = [];
    while ($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }

    // Devuelve los resultados en JSON
    echo json_encode($libros);
} else {
    echo json_encode([]);
}

// Cierra la conexión
$stmt->close();
$conex->close();
?>