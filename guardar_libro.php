<?php
include("con_db.php");

$response = ["success" => false, "message" => ""];

if (isset($_POST['titulo']) && isset($_POST['autor']) && isset($_POST['descripcion']) && isset($_POST['existencia']) && isset($_POST['fecha_publicacion']) && isset($_POST['estanteria']) && isset($_POST['piso']) && isset($_POST['nivel'])) {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $descripcion = trim($_POST['descripcion']);
    $existencia = (int) trim($_POST['existencia']); // Convertir a número entero
    $fecha_publicacion = trim($_POST['fecha_publicacion']);
    $estanteria = trim($_POST['estanteria']);
    $piso = trim($_POST['piso']);
    $nivel = trim($_POST['nivel']);

    // Verificar si el libro ya está registrado
    $check_stmt = $conex->prepare("SELECT existencia FROM Libros WHERE titulo = ?");
    $check_stmt->bind_param("s", $titulo);
    $check_stmt->execute();
    $check_stmt->bind_result($existencia_actual);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($existencia_actual !== null) {
        // Si el libro ya existe, actualizar la existencia sumando la nueva cantidad
        $nueva_existencia = $existencia_actual + $existencia;
        $update_stmt = $conex->prepare("UPDATE Libros SET existencia = ? WHERE titulo = ?");
        $update_stmt->bind_param("is", $nueva_existencia, $titulo);

        if ($update_stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "¡Existencias actualizadas!";
        } else {
            $response["message"] = "Error al actualizar existencias: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        // Si no existe, insertar el libro como nuevo
        if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
            $portada = file_get_contents($_FILES['portada']['tmp_name']); // Obtener los datos binarios
        } else {
            $portada = null; // Si no se sube una imagen, asignar null
        }
        
        // Modificar la consulta para almacenar la imagen en formato binario
        $insert_stmt = $conex->prepare("INSERT INTO Libros (titulo, autor, descripcion, existencia, fecha_de_publicacion, portada, estanteria, piso, nivel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Usamos "b" (binary) en el bind_param para manejar los datos binarios
        $insert_stmt->bind_param("sssisbsii", $titulo, $autor, $descripcion, $existencia, $fecha_publicacion, $portada, $estanteria, $piso, $nivel);
        $insert_stmt->send_long_data(5, $portada); // Enviar datos binarios correctamente
        
        if ($insert_stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "¡Libro registrado correctamente!";
        } else {
            $response["message"] = "Error al registrar el libro: " . $insert_stmt->error;
        }
        
        $insert_stmt->close();
        
    }
} else {
    $response["message"] = "¡Por favor, complete todos los campos!";
}

// Enviar respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>