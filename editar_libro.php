<?php
include("con_db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"]);
    $titulo = $_POST["titulo"];
    $autor = $_POST["autor"];
    $descripcion = $_POST["descripcion"];
    $existencia = intval($_POST["existencia"]);
    $estanteria = $_POST["estanteria"];
    $piso = intval($_POST["piso"]);
    $nivel = $_POST["nivel"];

    $stmt = $conex->prepare("UPDATE libros SET titulo=?, autor=?, descripcion=?, existencia=?, estanteria=?, piso=?, nivel=? WHERE ID_Libro=?");
    $stmt->bind_param("sssissii", $titulo, $autor, $descripcion, $existencia, $estanteria, $piso, $nivel, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Libro actualizado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el libro"]);
    }
}
?>
