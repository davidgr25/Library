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
        /* Estilos CSS anteriores se mantienen igual */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background: url(biblioteca.jpg); 
            background-size: 100%;
        }
        .btn-volver {
        position: fixed;
        left: 20px;
        bottom: 20px;
        padding: 10px 20px;
        background-color: #28a745; /* Verde */
        color: white;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
    }

    .btn-volver:hover {
        background-color: #218838; /* Verde más oscuro */
    }
        .barra-superior {
            background-color: #007bff;
            color: white;
            padding: 15px;
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .boton-derecha {
            flex: 1;
            text-align: right;
            padding-right: 20px;
        }

        .boton-derecha button {
            padding: 15px 15px;
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
        .btn-regresar {
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            padding-right: 25px;
        }
        
        /* Nuevos estilos para el contador */
        .contador-espera {
            margin-top: 15px;
            font-size: 14px;
            color: #dc3545;
            font-weight: bold;
        }
        
        .input-bloqueado {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    
    <div class="barra-superior">
        <div class="search-container">
            <input type="text" id="search" placeholder="Buscar libro..." onkeyup="buscarLibro()">
        </div>
        <div class="titulo-central">Amigos de David</div>
        <div class="btn-regresar">
        <div class="boton-derecha">
            <button onclick="window.location.href='Register.php'">Registrar Libro</button>
        </div>
        </div>
    </div>
    <div class="mensaje-bienvenida">
        Bienvenido a la biblioteca
    </div>
    <div id="resultados" class="results"></div>
    
    <a href="inicio.php" class="btn-volver">
    Volver al perfil de usuarios
</a>

<script>
// Configuración de seguridad
const HASH_CORRECTO = "78cd83e78ae0f4e070a553bd25b365f6236892b8f21f4a17b24c8829c2bdf322";
const INTENTOS_PERMITIDOS = 3; // 3 intentos iniciales
const tiemposEspera = [10, 15, 600, 1800, 3600, 7200, 14400, 28800, 57600, 172800]; // 2m, 5m, 10m, 30m, 1h, 2h, 4h, 8h, 16h, 48h

// Obtener o inicializar estado de bloqueo desde localStorage
function obtenerEstadoBloqueo() {
    const estado = localStorage.getItem('estadoBloqueo');
    return estado ? JSON.parse(estado) : {
        intentosFallidos: 0,
        tiempoBloqueoHasta: 0
    };
}

// Guardar estado de bloqueo en localStorage
function guardarEstadoBloqueo(intentos, tiempo) {
    localStorage.setItem('estadoBloqueo', JSON.stringify({
        intentosFallidos: intentos,
        tiempoBloqueoHasta: tiempo
    }));
}

// Resetear estado de bloqueo (cuando la contraseña es correcta)
function resetearBloqueo() {
    guardarEstadoBloqueo(0, 0);
}

async function generarSHA256(texto) {
    const encoder = new TextEncoder();
    const data = encoder.encode(texto);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
}

function mostrarMensajeError(intentosRestantes) {
    return Swal.fire({
        title: 'Contraseña incorrecta',
        html: `Te quedan ${intentosRestantes} intento(s) antes de que se active el tiempo de espera.`,
        icon: 'error',
        confirmButtonText: 'Intentar nuevamente',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
}

function formatearTiempo(segundos) {
    const horas = Math.floor(segundos / 3600);
    const minutos = Math.floor((segundos % 3600) / 60);
    const segs = segundos % 60;
    
    let mensaje = '';
    if (horas > 0) mensaje += `${horas}h `;
    if (minutos > 0) mensaje += `${minutos}m `;
    if (segs > 0 && horas === 0) mensaje += `${segs}s`;
    return mensaje.trim();
}

async function verificarAcceso() {
    // Obtener estado actual
    const estado = obtenerEstadoBloqueo();
    let { intentosFallidos, tiempoBloqueoHasta } = estado;
    
    // Verificar si estamos en periodo de bloqueo
    const ahora = Math.floor(Date.now() / 1000);
    if (ahora < tiempoBloqueoHasta) {
        const segundosRestantes = tiempoBloqueoHasta - ahora;
        await mostrarDialogoBloqueo(segundosRestantes);
        return; // No se permite el acceso hasta que termine el bloqueo
    }
    
    let autenticado = false;
    
    while (!autenticado) {
        const { value: contrasena } = await mostrarDialogoContrasena(intentosFallidos);
        
        if (contrasena === true) {
            autenticado = true;
            // Marcar como autenticado en sessionStorage
            sessionStorage.setItem('autenticado', 'true');
        } else if (contrasena === false) {
            // El usuario canceló o hubo un error
            intentosFallidos++;
        } else {
            // Verificar contraseña
            const hashIngresado = await generarSHA256(contrasena);
            
            if (hashIngresado !== HASH_CORRECTO) {
                intentosFallidos++;
                
                // Manejar según el número de intentos
                if (intentosFallidos <= INTENTOS_PERMITIDOS) {
                    // Mostrar mensaje de intentos restantes
                    const intentosRestantes = INTENTOS_PERMITIDOS - intentosFallidos;
                    guardarEstadoBloqueo(intentosFallidos, 0);
                    await mostrarMensajeError(intentosRestantes);
                    continue; // Continuar en el bucle, el acceso no se otorga
                } else {
                    // Calcular tiempo de espera progresivo
                    const intentosExcedidos = intentosFallidos - INTENTOS_PERMITIDOS;
                    const indiceTiempo = Math.min(intentosExcedidos - 1, tiemposEspera.length - 1);
                    const tiempoEsperaActual = tiemposEspera[indiceTiempo];
                    tiempoBloqueoHasta = ahora + tiempoEsperaActual;
                    
                    // Guardar el nuevo estado
                    guardarEstadoBloqueo(intentosFallidos, tiempoBloqueoHasta);
                    
                    await mostrarDialogoBloqueo(tiempoEsperaActual);
                    continue; // Continuar en el bucle, el acceso no se otorga
                }
            }
            
            // Restablecer contador de intentos si la contraseña es correcta
            resetearBloqueo();
            autenticado = true;
            sessionStorage.setItem('autenticado', 'true');
        }
    }
}

function mostrarDialogoContrasena(intentosFallidos) {
    let html = '<input type="password" id="contrasena" class="swal2-input" placeholder="Contraseña">';
    let intentosRestantes = INTENTOS_PERMITIDOS - intentosFallidos;
    
    if (intentosRestantes > 0 && intentosRestantes <= INTENTOS_PERMITIDOS) {
        html += `<div class="intentos-restantes">Intentos restantes: ${intentosRestantes}</div>`;
    }
    
    return Swal.fire({
        title: 'Acceso restringido',
        html: html,
        confirmButtonText: 'Ingresar',
        showCancelButton: true,
        cancelButtonText: 'Volver', // Usamos el símbolo de equis
        cancelButtonAriaLabel: 'Cancelar y salir',
        focusConfirm: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        preConfirm: async () => {
            const input = Swal.getPopup().querySelector('#contrasena');
            const contrasenaIngresada = input.value;
            
            if (!contrasenaIngresada) {
                Swal.showValidationMessage('Por favor ingresa la contraseña');
                return false;
            }
            
            return contrasenaIngresada;
        }
    }).then((result) => {
        // Si se hace clic en la equis (cancel)
        if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = 'inicio.php';
            return false; // Retornamos false para indicar que el usuario canceló
        }
        return result; // De lo contrario, retornamos el resultado normal
    });
}
async function mostrarDialogoBloqueo(segundosRestantes) {
    let tiempoInicial = segundosRestantes;
    let tiempoActual = segundosRestantes;
    
    // Crear el diálogo
    const { value: resultado } = await Swal.fire({
        title: 'Demasiados intentos fallidos',
        html: `
            <p>Debes esperar antes de intentar nuevamente.</p>
            <div class="contador-espera">Tiempo restante: ${formatearTiempo(tiempoActual)}</div>
            <input type="password" id="contrasena" class="swal2-input input-bloqueado" placeholder="Contraseña" disabled>
        `,
        icon: 'error',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            // Iniciar el contador
            const contadorElement = Swal.getPopup().querySelector('.contador-espera');
            const inputElement = Swal.getPopup().querySelector('#contrasena');
            
            const intervalo = setInterval(() => {
                tiempoActual--;
                contadorElement.textContent = `Tiempo restante: ${formatearTiempo(tiempoActual)}`;
                
                if (tiempoActual <= 0) {
                    clearInterval(intervalo);
                    Swal.close();
                }
            }, 1000);
            
            // Almacenar el intervalo para limpiarlo si el diálogo se cierra
            Swal.getPopup().setAttribute('data-intervalo', intervalo);
        },
        willClose: () => {
            // Limpiar el intervalo al cerrar
            const intervalo = Swal.getPopup().getAttribute('data-intervalo');
            if (intervalo) clearInterval(intervalo);
        }
    });
    
    // Después de que termine el tiempo, mostrar el diálogo normal
    if (tiempoActual <= 0) {
        return verificarAcceso(); // Volver a intentar
    }
    
    return false;
}

// Verificar autenticación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si viene de inicio.php (el referente contiene "inicio.php")
    const vieneDeInicio = document.referrer.indexOf('inicio.php') !== -1;
    
    // Si viene de inicio.php o no está autenticado, pedir contraseña
    if (vieneDeInicio || !sessionStorage.getItem('autenticado')) {
        verificarAcceso();
    }
});

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
                        <a href="libroadmin.php?id=${encodeURIComponent(libro.ID_Libro)}" style="text-decoration: none; color: black;">
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