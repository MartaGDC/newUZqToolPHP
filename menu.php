<?php
session_start();
require_once 'auth_check.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$host = 'http://127.0.0.1'; // A cambiar para despliegue en servidor
$key = "NH/a05xVQFOsoEk4uBFrdRVVOJw1hdu9txKRmyCTYrE=";
$token = $_SESSION['jwt'] ?? null;
if (!$token) {
    header("Location: index.php");
    exit();
}

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $user = $decoded->username;
} catch (Exception $e) {
    session_destroy();
    header("Location: index.php");
    exit();
}


if (isset($_GET['button'])) {
    $_SESSION['button'] = $_GET['button'];

    //Aplicaciones Flask, DRAW antiguos:
    $proyectos_antiguos = [
        'tendon_identification' => $host . ':5003/',
        'nervelong_identification' => $host . ':5001/',
        'nervetrans_identification' => $host . ':5002/',
    ];
    //Aplicaciones Flask, DRAW futuros. Todos en un mismo puerto con diferente header. El href se definirá en el propio boton

    if (isset($proyectos_antiguos[$_GET['button']])) {
        header('Location: ' . $proyectos_antiguos[$_GET['button']]);
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
            color: #1A1A1A;
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
            text-decoration: none;
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
            width: 250px;
            transition: background-color 0.1s, color 0.1s, border-color 0.1s;
        }
        .dropdown a:hover,
        .dropdown a:active {
            background-color: #3a3a3a;
            color: #FFFFFF;
            border: 1px solid #FFFFFF;
        }

        .projects {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 15px;
            padding-bottom: 10px;
            padding-top:40px;
        }

        .projects-container {
            margin-top: 10px;
            position: relative;
            z-index: 7;
        }
        .projects-container > .button {
            position: relative;
            z-index: 8;
            width: 200px;
        }
        .projects-container:nth-child(2) {
            z-index: 5;
        }
        .projects-container:nth-child(3) {
            z-index: 4;
        }

        .subprojects-container {
            margin-top: 10px;
            position: relative;
            z-index: 7;
        }
        .subprojects-container > .button {
            position: relative;
            z-index: 8;
            width: 250px;
        }

        .subprojects-container:nth-child(2) {
            z-index: 5;
        }
        .subprojects-container:nth-child(3) {
            z-index: 4;
        }
        .subprojects-container:nth-child(4) {
            z-index: 3;
        }
        .subprojects-container:nth-child(5) {
            z-index: 2;
        }
        .subprojects-container:nth-child(6) {
            z-index: 1;
        }
        .subprojects-container:nth-child(7) {
            z-index: 0;
        }

        .subprojects {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 6;
            padding: 15px;
            padding-bottom: 10px;
            padding-top:40px;
        }
        .subprojects > .button {
            position: relative;
            z-index: 9;
        }

        .subsubprojects-container {
            margin-top: 10px;
            position: relative;
            z-index: 7;
        }
        .subsubprojects-container > .button {
            z-index: 8;
        }
        .subsubprojects-container:nth-child(2) {
            z-index: 5;
        }
        .subsubprojects-container:nth-child(3) {
            z-index: 4;
        }
        .subsubprojects-container:nth-child(4) {
            z-index: 3;
        }
        .subsubprojects-container:nth-child(5) {
            z-index: 2;
        }

        .subsubprojects {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 7;
            padding: 10px;
            padding-bottom: 10px;
            padding-top:40px;
        }
        
        .subsubprojects > .button {
            position: relative;
            z-index: 9;
            width:300px;
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
                <?php if ($user == "mmu" || $user == "mgd" || $user == "jmp" || $user == "iag"): ?>
                    <a class="button" href="<?= $host ?>:5005/electrolysis?token=<?= urlencode($token) ?>">Electrolysis</a>
                    <a class="button" href="<?= $host ?>:5005/electrolysis2?token=<?= urlencode($token) ?>">Electrolysis2</a>
                <?php endif; ?>
                <?php if ($user == "mmu" || $user == "mgd" || $user == "sjg"): ?>
                    <a class="button" href="?button=muscle">Músculo</a>
                <?php endif; ?>
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
            <button class="button" id="drawBtn">UZink</button>
            <div class="dropdown" id="drawDropdown">
                <?php if ($user == "mmu" || $user == "mgd"): ?>
                    <div class="projects-container">
                        <a class="button project-option" data-target="base">Bases</a>
                        <div class="projects" id="baseDropdown">
                            <a class="button" href="<?= $host ?>:5004/base_tejidos?token=<?= urlencode($token) ?>">Tejidos</a>
                            <a class="button" href="<?= $host ?>:5004/base_artefactos?token=<?= urlencode($token) ?>">Artefactos</a>
                            <a class="button" href="<?= $host ?>:5004/base_ROIS?token=<?= urlencode($token) ?>">ROIs y ROPs</a>
                            <a class="button" href="<?= $host ?>:5004/base_marco?token=<?= urlencode($token) ?>">Marco y escala</a>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="projects-container">
                    <a class="button project-option" data-target="anatomy">Anatomía</a>
                    <div class="projects" id="anatomyDropdown">
                        <?php if ($user == "mmu" || $user == "jpr" || $user == "mgd"): ?>
                            <div class="subprojects-container">
                                <a class="button subproject-option" data-target="foot">Pie</a>
                                <div class="subprojects" id="footDropdown">
                                    <a class="button" href="<?= $host ?>:5004/foot_longitudinal_fascia?token=<?= urlencode($token) ?>">Fascia plantar longitudinal</a>
                                    <a class="button" href="<?= $host ?>:5004/foot_transversal_fascia?token=<?= urlencode($token) ?>">Fascia plantar transversal</a>
                                    <a class="button" href="<?= $host ?>:5004/foot_longitudinal_achilles?token=<?= urlencode($token) ?>">Aquiles longitudinal</a>
                                    <a class="button" href="<?= $host ?>:5004/foot_transversal_achilles?token=<?= urlencode($token) ?>">Aquiles transversal</a>
                                    <a class="button" href="<?= $host ?>:5004/foot_longitudinal_volar?token=<?= urlencode($token) ?>">Placa volar longitudinal</a>
                                    <a class="button" href="<?= $host ?>:5004/foot_transversal_tarsal?token=<?= urlencode($token) ?>">T&uacute;nel tarsiano transversal</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($user == "mmu" || $user == "pfm" || $user == "jmp" || $user == "mgd"): ?>
                            <div class="subprojects-container">
                                <a class="button subproject-option" data-target="knee">Rodilla</a>
                                <div class="subprojects" id="kneeDropdown">
                                    <div class="subsubprojects-container">
                                        <a class="button subsubproject-option" data-target = "knee_ant">Rodilla Anterior</a>
                                        <div class="subsubprojects" id="knee_antDropdown">
                                            <a class="button" href="<?= $host ?>:5004/knee_anterior_longitudinal?token=<?= urlencode($token) ?>">Longitudinal</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_anterior_transversal?token=<?= urlencode($token) ?>">Transversal</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_anterior_transverse_trochlea?token=<?= urlencode($token) ?>">Tr&oacute;clea transversal</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_anterior_longitudinal_trochlea?token=<?= urlencode($token) ?>">Tr&oacute;clea longitudinal</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_anterior_parasagittal?token=<?= urlencode($token) ?>">Parasagital</a>
                                        </div>
                                    </div>
                                    <div class="subsubprojects-container">
                                        <a class="button subsubproject-option" data-target = "knee_medial">Rodilla Medial</a>
                                        <div class="subsubprojects" id="knee_medialDropdown">
                                            <a class="button" href="<?= $host ?>:5004/knee_medial_LLI?token=<?= urlencode($token) ?>">LLI</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_medial_meniscal_transversal?token=<?= urlencode($token) ?>">Meniscal transversal</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_medial_meniscal_longitudinal?token=<?= urlencode($token) ?>">Meniscal longitudinal</a>
                                        </div>
                                    </div>
                                    <div class="subsubprojects-container">
                                        <a class="button subsubproject-option" data-target = "knee_lat">Rodilla Lateral</a>
                                        <div class="subsubprojects" id="knee_latDropdown">
                                            <a class="button" href="<?= $host ?>:5004/knee_lateral_cintilla?token=<?= urlencode($token) ?>">Cintilla iliotibial</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_lateral_LLE?token=<?= urlencode($token) ?>">LLE</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_lateral_biceps?token=<?= urlencode($token) ?>">B&iacute;ceps</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_lateral_menisco_transversal?token=<?= urlencode($token) ?>">Menisco transversal</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_lateral_menisco_longitudinal?token=<?= urlencode($token) ?>">Menisco longitudinal</a>
                                        </div>
                                    </div>
                                    <div class="subsubprojects-container">
                                        <a class="button subsubproject-option" data-target = "knee_post">Rodilla Posterior</a>
                                        <div class="subsubprojects" id="knee_postDropdown">
                                            <a class="button" href="<?= $host ?>:5004/knee_posterior_transversal_medial?token=<?= urlencode($token) ?>">Transversal medial</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_posterior_transversal_central?token=<?= urlencode($token) ?>">Transversal central</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_posterior_transversal_lateral?token=<?= urlencode($token) ?>">Transversal lateral</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_posterior_logitudinal_medial?token=<?= urlencode($token) ?>">Longitudinal medial</a>
                                            <a class="button" href="<?= $host ?>:5004/knee_posterior_longitudinal_lateral?token=<?= urlencode($token) ?>">Longitudinal lateral</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($user == "mmu" || $user == "pfm" || $user == "mgd"): ?>
                            <div class="subprojects-container">
                                <a class="button subproject-option" data-target="hand">Mano</a>
                                <div class="subprojects" id="handDropdown">
                                    <a class="button" href="<?= $host ?>:5004/hand_longitudinal?token=<?= urlencode($token) ?>">Longitudinal</a>
                                    <a class="button" href="<?= $host ?>:5004/hand_transversal?token=<?= urlencode($token) ?>">Transversal</a>
                                    <a class="button" href="<?= $host ?>:5004/hand_radial?token=<?= urlencode($token) ?>">Radial</a>
                                    <a class="button" href="<?= $host ?>:5004/hand_cubital?token=<?= urlencode($token) ?>">Cubital</a>
                                    <a class="button" href="<?= $host ?>:5004/hand_dorsal?token=<?= urlencode($token) ?>">Dorsal</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($user == "mmu" || $user == "ebg" || $user == "mgd"): ?>
                            <div class="subprojects-container">
                                <a class="button subproject-option" data-target="nerves">Nervios</a>
                                <div class="subprojects" id="nervesDropdown">
                                    <a class="button" href="<?= $host ?>:5004/nerves_STC?token=<?= urlencode($token) ?>">STC</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($user == "mmu" || $user == "ppa" || $user == "mgd"): ?>
                            <div class="subprojects-container">
                                <a class="button subproject-option" data-target="abd">Abdomino perineal</a>
                                <div class="subprojects" id="abdDropdown">
                                    <a class="button" href="<?= $host ?>:5004/abd_transversal_alba?token=<?= urlencode($token) ?>">Transversal l&iacute;nea media</a>
                                    <a class="button" href="<?= $host ?>:5004/abd_transversal_recto?token=<?= urlencode($token) ?>">Transversal recto</a>
                                    <a class="button" href="<?= $host ?>:5004/abd_transversal_spiegel?token=<?= urlencode($token) ?>">Transversal spiegel</a>
                                    <a class="button" href="<?= $host ?>:5004/abd_transversal_toracolum?token=<?= urlencode($token) ?>">Transversal fascia toracolumbar</a>
                                    <a class="button" href="<?= $host ?>:5004/abd_suelo_pelvico?token=<?= urlencode($token) ?>">Suelo p&eacute;lvico</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($user == "mmu" || $user == "mgd" || $user == "jpr" || $user == "iac" || $user == "ebg" || $user == "gcf" || $user == "jmp" || $user == "pfm" || $user == "sjg"): ?>
                    <div class="projects-container">
                        <a class="button project-option" data-target="proyecto">Proyectos</a>
                        <div class="projects" id="proyectoDropdown">
                            <?php if ($user == "mmu" || $user == "mgd" ||$user == "jmp" ): ?>
                                <div class="subprojects-container">
                                    <a class="button" href="<?= $host ?>:5004/menisco?token=<?= urlencode($token) ?>">MeniscoUZ</a>
                                </div>
                            <?php endif; ?>
                            <?php if ($user == "mmu" || $user == "mgd" || $user == "jpr" || $user == "iac"): ?>
                                <div class="subprojects-container">        
                                    <a class="button subproject-option" data-target="aquiles">Tend&oacute;n de Aquiles</a>
                                    <div class="subprojects" id="aquilesDropdown">
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/aquiles_longitudinal?token=<?= urlencode($token) ?>">Longitudinal</a>
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/aquiles_transversal?token=<?= urlencode($token) ?>">Transversal</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($user == "mmu" || $user == "mgd" || $user == "ebg" || $user == "gcf"): ?>
                                <div class="subprojects-container">
                                    <a class="button subproject-option" data-target="stc" style="z-index:5;">STC</a>
                                    <div class="subprojects" style="z-index:4;" id="stcDropdown">
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/stc_transversal?token=<?= urlencode($token) ?>">Transversal</a>
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/stc_longitudinal?token=<?= urlencode($token) ?>">Longitudinal</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($user == "mmu" || $user == "mgd" || $user == "pfm" ): ?>
                                <div class="subprojects-container">
                                    <a class="button subproject-option" data-target="polea" style="z-index:3;">Polea</a>
                                    <div class="subprojects" style="z-index:2;" id="poleaDropdown">
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/polea_longitudinal?token=<?= urlencode($token) ?>">Longitudinal</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($user == "mmu" || $user == "mgd" || $user == "sjg" ): ?>
                                <div class="subprojects-container">
                                    <a class="button" href="<?= $host ?>:5004/proDiafragma?token=<?= urlencode($token) ?>">Diafragma</a>
                                </div>
                            <?php endif; ?>
                            <?php if ($user == "mmu" || $user == "mgd"): ?>
                                <div class="subprojects-container">
                                    <a class="button subproject-option" data-target="rotuliano" style="z-index:2;">Tendón rotuliano</a>
                                    <div class="subprojects" style="z-index:1;" id="rotulianoDropdown">
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/rotuliano_longitudinal?token=<?= urlencode($token) ?>">Tendón rotuliano longitudinal</a>
                                        <a class="button" style="width:300px;" href="<?= $host ?>:5004/rotuliano_transversal?token=<?= urlencode($token) ?>">Tendón rotuliano transversal</a>

                                    </div>
                                </div>
                                <div class="subprojects-container">
                                    <a class="button" href="<?= $host ?>:5004/fascia_plantar?token=<?= urlencode($token) ?>">Fascia plantar</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>            
        </div>
            <?php if ($user == "mmu" || $user == "mgd"): ?>
                <!-- <a class="button" href="<?= $host ?>:5004/rm?token=<?= urlencode($token) ?>">MRink</a> -->
                <a class="button" href="<?= $host ?>:5006/UZSim?token=<?= urlencode($token) ?>">UZSim</a>
            <?php endif; ?>
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
            const isClickInside = e.target.closest('.dropdown') || e.target.closest('.projects');
            if (!isClickInside) {
                document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none');
                document.querySelectorAll('.projects').forEach(p => p.style.display = 'none');
            }
        });



        //Funcion para subdropdowns
         document.querySelectorAll('.button.project-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const target = option.dataset.target;
                const projectId = `${target}Dropdown`;
                const projectDropdown = document.getElementById(projectId);
                const todosProjectDropdowns =document.querySelectorAll('.projects');
                todosProjectDropdowns.forEach(pd => {
                    if(pd != projectDropdown) pd.style.display = 'none';
                })
                projectDropdown.style.display = (projectDropdown.style.display==='block')? 'none' : 'block';
            })
        })

        document.querySelectorAll('.button.subproject-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const target = option.dataset.target;
                const projectId = `${target}Dropdown`;
                const projectDropdown = document.getElementById(projectId);
                const todosProjectDropdowns =document.querySelectorAll('.subprojects');
                todosProjectDropdowns.forEach(pd => {
                    if(pd != projectDropdown) pd.style.display = 'none';
                })
                projectDropdown.style.display = (projectDropdown.style.display==='block')? 'none' : 'block';
            })
        })
        
        //Funcion para proyetos
        document.querySelectorAll('.button.subsubproject-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const target = option.dataset.target;
                const projectId = `${target}Dropdown`;
                const projectDropdown = document.getElementById(projectId);
                const todosProjectDropdowns =document.querySelectorAll('.subsubprojects');
                todosProjectDropdowns.forEach(pd => {
                    if(pd != projectDropdown) pd.style.display = 'none';
                })
                projectDropdown.style.display = (projectDropdown.style.display==='block')? 'none' : 'block';
            })
        })

    </script>
</body>
</html>