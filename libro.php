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
            margin: 10% auto;
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
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($libro['descripcion']); ?></p>
            <p><strong>Existencias:</strong> <?php echo htmlspecialchars($libro['existencia']); ?></p>
            <p><strong>Estanteria:</strong> <?php echo htmlspecialchars($libro['estanteria']); ?></p>
            <p><strong>Piso:</strong> <?php echo htmlspecialchars($libro['piso']); ?></p>
            <p><strong>Nivel:</strong> <?php echo htmlspecialchars($libro['nivel']); ?></p>

            <img class = "imagen" src="<?php echo !empty($libro['portada']) ? 'data:image/jpeg;base64,' . base64_encode($libro['portada']) : 'ruta/a/imagen/default.jpg'; ?>" alt="Portada del libro">
        </div>

        <div class="botones">
            <button class="btn btn-editar" onclick="abrirModal()">Editar Información</button>
            <button class="btn btn-eliminar" onclick="eliminarLibro(<?php echo $id; ?>)">Eliminar Libro</button>
        </div>
    </div>

    <!-- Modal de edición -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Editar Libro</h2>
            <form id="formEditar">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <label>Título:</label>
                <input class="campo" type="text" name="titulo" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required><br>
                <label>Autor:</label>
                <input class="campo" type="text" name="autor" value="<?php echo htmlspecialchars($libro['autor']); ?>" required><br>
                <label>Existencias:</label>
                <input class="campo" type="number" name="existencia" value="<?php echo htmlspecialchars($libro['existencia']); ?>" required><br>
                <label>Estantería:</label>
                <input class="campo" type="text" name="estanteria" maxlength="2" value="<?php echo htmlspecialchars($libro['estanteria']); ?>" required><br>
                <label>Piso:</label>
                <input class="campo" type="number" name="piso" min="1" max="4" value="<?php echo htmlspecialchars($libro['piso']); ?>" required><br>
                <label>Nivel:</label>
                <input class="campo" type="number" name="nivel" min="1" max="9" value="<?php echo htmlspecialchars($libro['nivel']); ?>" required><br>
                <label>Descripción:</label>
                <textarea class="campo" name="descripcion" required><?php echo htmlspecialchars($libro['descripcion']); ?></textarea><br>
                <button class="guardar" type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById("modalEditar").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modalEditar").style.display = "none";
        }

        document.getElementById("formEditar").addEventListener("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            
            fetch("editar_libro.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? "success" : "error",
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        });
    </script>

</body>
</html>
