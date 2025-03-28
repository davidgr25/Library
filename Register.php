<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Libros</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
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
            margin-top: 80px; /* Espacio para la barra */
        }

        form {
            background: rgb(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px gray;
            max-width: 500px;
            margin: auto;
        }
        
        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="file"] {
            width: 95%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button:hover {
            background-color: #218838;
        }

        button:active {
            background-color: #1e7e34;
        }

        input[type="file"] {
            border: 1px solid #ccc;
        }

        input[type="file"]:hover {
            border: 1px solid #aaa;
        }

        .file-label {
            color: #555;
            margin-top: 10px;
        }

        .file-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-input-wrapper input[type="file"] {
            width: auto;
            padding: 0;
        }

        body {
            background: url(biblioteca.jpg); 
            background-size: 50%;
        }
        .Amigos {
            padding-right: 25px;
        }
    </style>
</head>
<body>

    <!-- Barra superior -->
    <div class="barra-superior">
        <a href="inicioadmin.php" class="btn-regresar" onclick="sessionStorage.setItem('autenticado', 'true')">Volver al Inicio</a>
        <div class="titulo">Registro de Libros</div>
        <div class= "Amigos">Amigos de David</div>
    </div>
    <div class="container">
        <form id="libroForm">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" required>

            <label for="autor">Autor:</label>
            <input type="text" name="autor" id="autor" required>

            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" required>

            <label for="existencia">Existencia:</label>
            <input type="number" name="existencia" id="existencia" required>

            <label for="estanteria">Estanteria:</label>
            <input type="text" name="estanteria" id="estanteria" maxlength="2" required oninput="this.value = this.value.toUpperCase()">

            <label for="piso">Piso:</label>
            <input type="number" name="piso" id="piso" min="1" max="4" required>

            <label for="fecha_publicacion">Fecha de Publicación:</label>
            <input type="date" name="fecha_publicacion" id="fecha_publicacion" required>

            <div class="file-input-wrapper">
                <label for="portada" class="file-label">Portada (Imagen):</label>
                <input type="file" name="portada" id="portada" accept="image/*">
            </div>

            <button type="submit">Registrar Libro</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0];
            document.getElementById("fecha_publicacion").setAttribute("max", today);
        });

        document.getElementById("libroForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Evitar que se recargue la página

            let formData = new FormData(this);

            fetch("guardar_libro.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json()) // Convertir la respuesta a JSON
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000
                });

                if (data.success) {
                    document.getElementById("libroForm").reset(); // Limpiar el formulario
                }
            })
            .catch(error => console.error("Error:", error));
        });
    </script>

</body>
</html>
