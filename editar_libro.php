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
    $nivel = $_POST['nivel'];

    if (!empty($_FILES['portada']['tmp_name'])) {
        $imagen = file_get_contents($_FILES['portada']['tmp_name']);
        
        $stmt = $conex->prepare("UPDATE libros SET titulo=?, autor=?, descripcion=?, existencia=?, estanteria=?, piso=?, nivel=?, portada=? WHERE ID_Libro=?");
        $stmt->bind_param("sssiissbi", $titulo, $autor, $descripcion, $existencia, $estanteria, $piso, $nivel, $null, $id);
        $stmt->send_long_data(7, $imagen); // Enviar la imagen en el parámetro 7
    } else {
        $stmt = $conex->prepare("UPDATE libros SET titulo=?, autor=?, descripcion=?, existencia=?, estanteria=?, piso=?, nivel=? WHERE ID_Libro=?");
        $stmt->bind_param("sssiissi", $titulo, $autor, $descripcion, $existencia, $estanteria, $piso, $nivel, $id);
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
