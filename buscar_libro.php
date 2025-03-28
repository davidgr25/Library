<?php
include("con_db.php"); // Asegúrate que este archivo existe y tiene la conexión correcta

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($query)) {
    // Limpiar el parámetro de búsqueda
    $searchTerm = $conex->real_escape_string($query);
    
    // Consulta modificada para buscar solo en la tabla libros con el campo autor
    $sql = "SELECT ID_Libro, titulo, autor, existencia 
            FROM libros
            WHERE titulo LIKE '".$searchTerm."%' OR autor LIKE '".$searchTerm."%'
            ORDER BY titulo ASC";

    $result = $conex->query($sql);
    
    if (!$result) {
        // Si hay error en la consulta
        echo json_encode(['error' => $conex->error]);
        exit;
    }
    
    $libros = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $libros[] = $row;
        }
    }
    
    echo json_encode($libros);
} else {
    echo json_encode(array());
}

$conex->close();
?>