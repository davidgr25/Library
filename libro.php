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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            background: url(biblioteca.jpg); 
            background-size: 100%;
        }
        .imagen{
            width: 200px;
            height: 300px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px gray;
            max-width: 600px;
            margin: 100px auto 20px auto;
            text-align: center;
        }
        .book-info {
            text-align: left;
        }
        .botones {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .btn {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            width: 48%;
        }
        .btn-editar {
            background-color: #ffc107;
        }
        .btn-editar:hover {
            background-color: #e0a800;
        }
        .btn-eliminar {
            background-color: #dc3545;
        }
        .btn-eliminar:hover {
            background-color: #c82333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            text-align: center;
        }
        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
        .campo {
            width: 55%;
            padding: 5px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .guardar {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 48%;
            background-color: #ffc107;
        }
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
        .titulo {
            text-align: center;
            flex: 1;
        }
        .Amigos {
            padding-right: 25px;
        }
    </style>
</head>
<body>

    <div class="barra-superior">
        <a href="inicio.php" class="btn-regresar">Volver al Inicio</a>
        <div class="titulo">Informacion de Libro</div>
        <div class= "Amigos">Amigos de David</div>
    </div>

    <div class="container">
        <h2><?php echo htmlspecialchars($libro['titulo']); ?></h2>

        <div class="book-info">
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
            <p><strong>Existencias:</strong> <?php echo htmlspecialchars($libro['existencia']); ?></p>
            <p><strong>Estanteria:</strong> <?php echo htmlspecialchars($libro['estanteria']); ?></p>
            <p><strong>Piso:</strong> <?php echo htmlspecialchars($libro['piso']); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($libro['descripcion']); ?></p>

            <img class = "imagen" src="<?php echo !empty($libro['portada']) ? 'data:image/jpeg;base64,' . base64_encode($libro['portada']) : 'ruta/a/imagen/default.jpg'; ?>" alt="Portada del libro">
        </div>
    </div>

</body>
</html>
