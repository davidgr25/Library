<?php
include("con_db.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $descripcion = $_POST['descripcion'];
    $existencia = intval($_POST['existencia']);
    $estanteria = $_POST['estanteria'];
    $piso = intval($_POST['piso']);

    if (!empty($_FILES['portada']['tmp_name'])) {
        $imagen = file_get_contents($_FILES['portada']['tmp_name']);
        
        $stmt = $conex->prepare("UPDATE Libros SET titulo=?, autor=?, descripcion=?, existencia=?, estanteria=?, piso=?, portada=? WHERE ID_Libro=?");
        $stmt->bind_param("sssissbi", $titulo, $autor, $descripcion, $existencia, $estanteria, $piso, $null, $id);
        $stmt->send_long_data(7, $imagen); // Enviar la imagen en el parámetro 7
    } else {
        $stmt = $conex->prepare("UPDATE Libros SET titulo=?, autor=?, descripcion=?, existencia=?, estanteria=?, piso=? WHERE ID_Libro=?");
        $stmt->bind_param("sssissi", $titulo, $autor, $descripcion, $existencia, $estanteria, $piso, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Libro actualizado con éxito"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
?>
