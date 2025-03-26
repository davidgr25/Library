<?php
include("con_db.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Biblioteca</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&family=Great+Vibes&display=swap" rel="stylesheet">
    <style>



        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background: url(biblioteca.jpg); 
            background-size: 100%;
        }

        .barra-superior {
            background-color: #007bff;
            color: white;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
        }

        .search-container {
            flex: 1;
        }

        .search-container input {
            padding: 12px;
            border: none;
            border-radius: 5px;
            width: 300px;
            font-size: 18px;
            margin-left: 20px;
        }

        .titulo-central {
            flex: 2;
            text-align: center;
            font-weight: bold;
        }

        .boton-derecha {
            flex: 1;
            text-align: right;
            padding-right: 20px;
        }

        .boton-derecha button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        .boton-derecha button:hover {
            background-color: #218838;
        }

        .boton-derecha {
            padding-right: 25px;
        }
        body {
            margin: 0;
            padding-top: 80px;
            font-family: Arial, sans-serif;
        }

        .results {
            background: rgb(255, 255, 255, 0.9);
            max-width: 500px;
            margin: 10px auto;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px gray;
        }

        .result-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .result-item:last-child {
            border-bottom: none;
        }
        .mensaje-bienvenida {
            font-family: "Caveat", cursive;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 80px;
            color: rgb(0, 123, 255, 0.5);
            text-align: center;
            z-index: -1;
            text-shadow: 
            -1px -1px 0 white,  
            1px -1px 0 white,
            -1px  1px 0 white,
            1px  1px 0 white;
        }
    </style>
</head>
<body>

    <div class="barra-superior">
        <div class="search-container">
            <input type="text" id="search" placeholder="Buscar libro..." onkeyup="buscarLibro()">
        </div>
        <div class="titulo-central">Amigos de David</div>
        <div class="boton-derecha">
            <button onclick="verificarContraseña()">Registrar Libro</button>
        </div>
    </div>
    <div class="mensaje-bienvenida">
        Bienvenido a la biblioteca
    </div>
    <div id="resultados" class="results"></div>

<script>
    let intentos = 0;
    const maxIntentos = 3;
    const tiempoMaximo = 2 * 24 * 60 * 60 * 1000; 
    const contrasure = "78cd83e78ae0f4e070a553bd25b365f6236892b8f21f4a17b24c8829c2bdf322"; 

    async function hashContraseña(contraseña) {
        const encoder = new TextEncoder();
        const data = encoder.encode(contraseña);
        const hashBuffer = await crypto.subtle.digest("SHA-256", data);
        return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, "0")).join("");
    }

    function verificarContraseña() {
        let bloqueoHasta = localStorage.getItem("bloqueo");
        let tiempoEspera = parseInt(localStorage.getItem("tiempoEspera")) || 30000; // 30 segundos iniciales

        if (bloqueoHasta && new Date().getTime() < bloqueoHasta) {
            let tiempoRestante = Math.ceil((bloqueoHasta - new Date().getTime()) / 1000);
            Swal.fire("Acceso bloqueado", `Intenta de nuevo en ${tiempoRestante} segundos`, "error");
            return;
        }

        Swal.fire({
            title: "Ingrese la contraseña",
            input: "password",
            inputPlaceholder: "Contraseña",
            showCancelButton: true,
            confirmButtonText: "Ingresar",
            cancelButtonText: "Cancelar",
            inputAttributes: {
                maxlength: 20,
                autocapitalize: "off",
                autocorrect: "off"
            }
        }).then(async (resultado) => {
            if (resultado.isConfirmed) {
                let hashIngresado = await hashContraseña(resultado.value);

                if (hashIngresado === contrasure) {
                    Swal.fire("Acceso concedido", "Redirigiendo...", "success");
                    localStorage.removeItem("bloqueo");
                    localStorage.removeItem("tiempoEspera");
                    intentos = 0;
                    setTimeout(() => {
                        window.location.href = "Register.php";
                    }, 1000);
                } else {
                    intentos++;
                    if (intentos >= maxIntentos) {
                        let nuevoBloqueoHasta = new Date().getTime() + tiempoEspera;
                        localStorage.setItem("bloqueo", nuevoBloqueoHasta);
                        localStorage.setItem("tiempoEspera", Math.min(tiempoEspera * 2, tiempoMaximo)); // Duplica el tiempo hasta 2 días
                        Swal.fire("Acceso bloqueado", `Demasiados intentos. Espera ${tiempoEspera / 1000} segundos.`, "error");
                        intentos = 0; // Reiniciar intentos tras bloqueo
                    } else {
                        Swal.fire("Contraseña incorrecta", `Intento ${intentos} de ${maxIntentos}`, "error");
                    }
                }
            }
        });
    }
function buscarLibro() {
    let query = document.getElementById("search").value;

    if (query.length < 1) {
        document.getElementById("resultados").innerHTML = "";
        return;
    }

    fetch("buscar_libro.php?q=" + query)
    .then(response => response.json())
    .then(data => {
        let resultadosHTML = "";
        if (data.length > 0) {
            data.forEach(libro => {
                resultadosHTML += `
                    <div class="result-item">
                        <a href="libro.php?id=${encodeURIComponent(libro.ID_Libro)}" style="text-decoration: none; color: black;">
                            <strong>${libro.titulo}</strong><br>
                            Autor: <em>${libro.autor}</em><br>
                            Existencias: ${libro.existencia}
                        </a>
                    </div>
                `;
            });
        } else {
            resultadosHTML = "<p>No se encontraron libros ni autores.</p>";
        }
        document.getElementById("resultados").innerHTML = resultadosHTML;
    })
    .catch(error => console.error("Error en la búsqueda:", error));
}
</script>

</body>
</html>
