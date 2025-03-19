<?php
include("con_db.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conex->prepare("SELECT * FROM libros WHERE ID_Libro = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $libro = $result->fetch_assoc();

    if (!$libro) {
        echo "<h2>Error: El libro no existe.</h2>";
        exit();
    }
} else {
    echo "<h2>Error: No se ha especificado un libro.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($libro['titulo']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Barra azul */
        .barra-superior {
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            font-size: 22px;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .titulo {
            text-align: center;
            flex: 1;
        }

        .btn-regresar {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }

        .btn-regresar:hover {
            background-color: #218838;
        }

        /* Ajuste del contenido para evitar que lo tape la barra */
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px gray;
            max-width: 600px;
            margin: 100px auto 20px auto; /* Espacio para la barra */
            text-align: center;
        }

        .book-info {
            margin-top: 20px;
            text-align: left;
        }

        .book-info p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .book-info img {
            max-width: 300px;
            height: auto;
            border-radius: 5px;
            margin-top: 20px;
        }
        .Amigos{
            padding-right: 25px;
        }
    </style>
</head>
<body>

    <!-- Barra superior -->
    <div class="barra-superior">
        <a href="inicio.php" class="btn-regresar">Volver al Inicio</a>
        <div class="titulo">Información del Libro</div>
        <div class="Amigos">Amigos de David</div>
    </div>

    <div class="container">
        <h2><?php echo htmlspecialchars($libro['titulo']); ?></h2>

        <div class="book-info">
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($libro['descripcion']); ?></p>
            <p><strong>Existencias:</strong> <?php echo htmlspecialchars($libro['existencia']); ?></p>
            <p><strong>Fecha de Publicación:</strong> <?php echo htmlspecialchars($libro['fecha_de_publicacion']); ?></p>

            <?php
            if (!empty($libro['portada'])) {
                $imagen_base64 = base64_encode($libro['portada']);
                $imagen_src = 'data:image/jpeg;base64,' . $imagen_base64;
            } else {
                $imagen_src = 'ruta/a/imagen/default.jpg';
            }
            ?>
            
            <img src="<?php echo $imagen_src; ?>" alt="Portada del libro">
            
        </div>
    </div>

</body>
</html>
