<?php
include("con_db.php");

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $conex->prepare("DELETE FROM libros WHERE ID_Libro = ?");
    $stmt->bind_param("i", $id);

    echo json_encode(["success" => $stmt->execute()]);
}
?>
