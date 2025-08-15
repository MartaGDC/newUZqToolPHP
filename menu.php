<?php
require_once 'auth_check.php';
session_start();

$host = 'http://127.0.0.1'; // A cambiar para despliegue en servidor

if (isset($_GET['button'])) {
    $_SESSION['button'] = $_GET['button'];

    //Aplicaciones Flask, DRAW antiguos:
    $proyectos_antiguos = [
        'tendon_identification' => $host . ':5003/',
        'nervelong_identification' => $host . ':5001/',
        'nervetrans_identification' => $host . ':5002/',
    ];
    //Aplicaciones Flask, DRAW futuros. Todos en un mismo puerto con diferente header:
     $proyectos_nuevos = [
        'foot' => $host . ':5004/foot',
    ];

     if (isset($proyectos_antiguos[$_GET['button']])) {
        header('Location: ' . $proyectos_antiguos[$_GET['button']]);
    } elseif (isset($nuevos_proyectos[$_GET['button']])) {
        header('Location: ' . $nuevos_proyectos[$_GET['button']]);
    } else {
        header('Location: tabs.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Options</title>
    <style>
        body {
        font-family: 'Arial', sans-serif;
        color: #1A1A1A;
        font-weight: bold;
        margin: 0;
        padding: 0;
        background-color: #B0B0B0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        }

        h1 {
            font-size: 110px;
            font-weight: bold;
            margin: 100px;
            color = #1A1A1A;
        }
        .first-two{
            color: #333333;
        }

        .container {
            text-align: center;
            background-color: #F5F5F5;
            padding: 60px;
            width: 80%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .button {
            display: inline-block;
            padding-top: 15px;
            padding-bottom: 15px;
            width: 200px;
            font-family: 'Arial', sans-serif;
            font-size: 20px;
            font-weight: bold;
            line-height: 1.5;
            color: #F5F5F5;
            text-align: center;
            background-color: #1A1A1A;
            border: 1px solid #F5F5F5;
            border-radius: 5px;
            transition: background-color 0.1s, color 0.1s, border-color 0.1s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 20;
            position:relative;
        }

        .button:hover,
        .button:active {
            background-color: #3a3a3a;
            color: #FFFFFF;
            border: 1px solid #FFFFFF;
        }

        .dropdown-container {
            position: relative;
            display: inline-block;
        }
        .dropdown {
            display: none;
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #FFFFFF;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10;
            padding: 10px;
            padding-bottom: 10px;
            padding-top:40px;
        }

        .dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            margin-top: 10px;
            background-color: #1A1A1A;
            color: #F5F5F5;
            width: 300px;
            transition: background-color 0.1s, color 0.1s, border-color 0.1s;
        }

        .dropdown a:hover,
        .dropdown a:active {
            background-color: #3a3a3a;
            color: #FFFFFF;
            border: 1px solid #FFFFFF;
        }

        .subdropdown-container {
            margin-top: 10px;
            position: relative;
            z-index: 2;
        }.
        .subdropdown-container > .button {
            position: relative;
            z-index: 3;
        }
        .subdropdown-container:nth-child(2) {
            z-index: 0; /* New más abajo que el subdropdown de Old */
        }
        .subdropdown {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px;
            padding-bottom: 10px;
            padding-top:40px;
        }

    </style>
</head>

<body>
    <h1>
      <span class="first-two">UZ</span>qTool
    </h1>
    <div class="container">
        <!-- Manual Button -->
        <div class="dropdown-container">
            <button class="button" id="manualBtn">Manual<br>UZqTool 1.0</button>
            <div class="dropdown" id="manualDropdown">
                <a class="button" href="?button=tendon">Tendon</a>
                <a class="button" href="?button=nerve-transversal">Nerve Transversal</a>
                <a class="button" href="?button=nerve-longitudinal">Nerve Longitudinal</a>
                <a class="button" href="?button=electrolysis">Electrolysis</a>
            </div>
        </div>
        <!-- Automático -->
        <div class="dropdown-container">
            <button class="button" id="automaticBtn">Automatic<br>UZqTool 2.0</button>
            <div class="dropdown" id="automaticDropdown">
                <!-- A definir -->
            </div>
        </div>
        <!-- DRAW Button -->
        <div class="dropdown-container">
            <button class="button" id="drawBtn">DRAW</button>
            <div class="dropdown" id="drawDropdown">
                <div class="subdropdown-container">
                    <button class="button draw-option" data-target="old" type="button">Old projects</button> <!-- No navegar a otra pagina, sino al target "old" que contendrá los proyectos que están en otra página -->
                    <div class="subdropdown" id="drawOldDropdown">
                        <a class="button" href="?button=tendon_identification">Tendon Identification</a>
                        <a class="button" href="?button=nervelong_identification">Nerve Long. Identification</a>
                        <a class="button" href="?button=nervetrans_identification">Nerve Trans. Identification</a>
                    </div>
                </div>
                <div class="subdropdown-container">
                    <button class="button draw-option" data-target="new" type="button">New projects</button>
                    <div class="subdropdown" id="drawNewDropdown">
                        <a class="button" href="<?= $host ?>:5004/foot">Foot</a>
                        <a class="button" href="#">...</a>
                    </div>
                </div>
            </div>            
        </div>
    </div>

    <script>
        // Función para mostrar/ocultar dropdown
        function desplgarDropdown(button, dropdown) {
            button.addEventListener('click', function(e){
                e.stopPropagation(); //Evitar que este click ejecute otros eventos de click
                const todosDropdowns = document.querySelectorAll('.dropdown');
                todosDropdowns.forEach(d => {
                    if(d !== dropdown) d.style.display = 'none'; //Si el dropdown no es el clicado, lo oculta
                });
                dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block'; //Si esta abierto lo cierra, y si no lo abre.
            });
        }
        
        // Inicializar dropdowns
        const manualBtn = document.getElementById('manualBtn');
        const manualDropdown = document.getElementById('manualDropdown');
        desplgarDropdown(manualBtn, manualDropdown);
        
        const automaticBtn = document.getElementById('automaticBtn');
        const automaticDropdown = document.getElementById('automaticDropdown');
        desplgarDropdown(automaticBtn, automaticDropdown);

        const drawBtn = document.getElementById('drawBtn');
        const drawDropdown = document.getElementById('drawDropdown');
        desplgarDropdown(drawBtn, drawDropdown);

        document.addEventListener('click', function(e) {
            const isClickInside = e.target.closest('.dropdown') || e.target.closest('.subdropdown');
            if (!isClickInside) {
                document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none');
                document.querySelectorAll('.subdropdown').forEach(s => s.style.display = 'none');
            }
        });

        //Funcion para subdropdowns
        document.querySelectorAll('.button.draw-option').forEach(opt => {
            opt.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation(); // evitar que cierre el dropdown principal
                const target = opt.dataset.target;
                const dropdownId = `draw${target.charAt(0).toUpperCase() + target.slice(1)}Dropdown`;
                const dropdown = document.getElementById(dropdownId);
                const todosSubdropdowns = document.querySelectorAll('.subdropdown');
                todosSubdropdowns.forEach(sd => {
                    if(sd !== dropdown) sd.style.display = 'none';
                });
                dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block'; 
            });
        });
    </script>
</body>
</html>