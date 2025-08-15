<?php
require_once 'auth_check.php';

    session_start();
    // Check if the image has been submitted
    if (isset($_SESSION['button'])){
        // Retrieve the selected tab 
        $caseButton = $_SESSION['button'];//Load session case (Button)
    
        $activateTab = TRUE;
        

        switch ($caseButton) {
            case 'tendon':
                $activateTab = TRUE;
                break;
            case 'nerve-transversal':

                $activateTab = FALSE;
                break;
            case 'nerve-longitudinal':
                $activateTab = FALSE;
                break;
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="./favicon.png" type="image/png">
    <title>Next Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            min-height: 915px;
            display: flex;
            flex-direction: column;
        }
   
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 2px;
        }

        .tab {
            padding: 10px 20px;
            background-color: #eee;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .summary {
            padding: 10px 20px;
            background-color: lightblue;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .summary.active {
            background-color: lightgreen;
        }

        .tab.active {
            background-color: lightgreen;
        }

        .tab.disabled {
            opacity: 0.6; /* Reduce opacity to visually indicate they are disabled */
            cursor: not-allowed; /* Change cursor to "not-allowed" to indicate non-clickable */
            pointer-events: none; /* Ignore pointer events to prevent clicks */
        }

        .summary.disabled {
            opacity: 0.6; /* Reduce opacity to visually indicate they are disabled */
            /*cursor: not-allowed; /* Change cursor to "not-allowed" to indicate non-clickable */
            /*pointer-events: none; /* Ignore pointer events to prevent clicks */
        }

        .content {
            padding: 10px;
            border-top: 1px solid #ccc;
            display: none;
            flex-grow: 1;
        }

        .content.active {
            display: flex;
        }

        .navigation {
            display: flex;
            justify-content: center;
            align-items: center;

        }

        .navigation-arrows{
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 5px;
        }

        .navigation-arrows button {
            padding: 8px 14px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background-color: #4CAF50;
            border: 1px solid black;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .navigation-arrows button:hover {
            background-color: #45a049;
        }

        .navigation-arrows button[disabled] {
            opacity: 0.6; /* Reduce opacity to visually indicate it's disabled */
            cursor: not-allowed; /* Change cursor to "not-allowed" to indicate non-clickable */
            pointer-events: none; /* Ignore pointer events to prevent clicks */
        }


        .end-button {
            padding: 8px 14px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background-color: Red;
            border: 1px solid black;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .end-button:hover{
            background-color: blue;
        }
        .end-button-container {
            margin-left: auto;
        }

        .button-89 {
        --b: 3px;   /* border thickness */
        --s: .45em; /* size of the corner */
        --color: #373B44;
        
        padding: calc(.5em + var(--s)) calc(.9em + var(--s));
        color: var(--color);
        --_p: var(--s);
        background:
            conic-gradient(from 90deg at var(--b) var(--b),#0000 90deg,var(--color) 0)
            var(--_p) var(--_p)/calc(100% - var(--b) - 2*var(--_p)) calc(100% - var(--b) - 2*var(--_p));
        transition: .3s linear, color 0s, background-color 0s;
        outline: var(--b) solid #0000;
        outline-offset: .6em;
        font-size: 16px;

        border: 0;

        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        }

        .button-89:hover,
        .button-89:focus-visible{
        --_p: 0px;
        outline-color: var(--color);
        outline-offset: .05em;
        }

        .button-89:active {
        background: var(--color);
        color: #fff;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all tabs and content elements
            var tabs = Array.from(document.getElementsByClassName('tab'));
            tabs.push(document.querySelector('.summary')); // Add the Summary tab to the tabs array
            var contents = Array.from(document.getElementsByClassName('content'));

            // Add click event listeners to each tab
            tabs.forEach(function(tab, index) {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs and contents
                    tabs.forEach(function(tab) {
                        tab.classList.remove('active');
                    });
                    contents.forEach(function(content) {
                        content.classList.remove('active');
                    });

                    // Add active class to the clicked tab and display the corresponding content
                    tab.classList.add('active');
                    contents[index].classList.add('active');
                });
            });

            // Set initial active tab and content
            tabs[0].classList.add('active');
            contents[0].classList.add('active');

            // Add navigation arrow functionality
            var currentIndex = 0;

            function isNextTabDisabled() {
                const nextIndex = (currentIndex === tabs.length - 1) ? 0 : currentIndex + 1;
                return tabs[nextIndex].classList.contains('disabled');
            }
            function isPrevTabDisabled() {
                const nextIndex = (currentIndex === 0) ? tabs.length - 1 : currentIndex - 1;
                return tabs[nextIndex].classList.contains('disabled');
            }

            function navigate(direction) {
                // Hide current content
                contents[currentIndex].classList.remove('active');

                // Update current index based on navigation direction
                if (direction === 'prev' && !isPrevTabDisabled()) {
                    currentIndex = (currentIndex === 0 ) ? tabs.length - 1 : currentIndex - 1;
                } else if (direction === 'next' && !isNextTabDisabled()) {
                    currentIndex = (currentIndex === tabs.length - 1) ? 0 : currentIndex + 1;
                }

                // Display new content
                contents[currentIndex].classList.add('active');

                // Update active tab
                tabs.forEach(function(tab) {
                    tab.classList.remove('active');
                });
                tabs[currentIndex].classList.add('active');
            }

            // Add click event listeners to navigation arrows
            var prevButton = document.getElementById('prev-button');
            var nextButton = document.getElementById('next-button');

            prevButton.addEventListener('click', function() {
                navigate('prev');
            });

            nextButton.addEventListener('click', function() {
                navigate('next');
            });
            
        });


        function End() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href = './index.php';
                }
            };
            xhr.open('POST', './unset_session.php', true);
            xhr.send();
        }

        function ReOther() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    parent.location.href = './tabs.php';
                }
            };
            xhr.open('POST', './re-evaluate-2.php', true);
            xhr.send();
        }

    </script>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <div class="tabs">
                <div class="tab">Select File</div>
                <div class="tab disabled" id="tab-tissue">Tissue Quality</div>
                <div class="tab disabled" id="tab-borders">Borders</div>
                <div class="tab disabled" id="tab-morphology">Morphology</div>
                <?php
                if ($activateTab){
                ?>
                    <div class="tab disabled" id="tab-bone">Bone</div>
                <?php
                }   
                ?>           
                <div class="summary disabled" id="tab-summary">Summary</div>
            </div>
            <div class="end-button-container"> 
                <button id="home-button" onclick="ReOther()" class="button-89">Home</button>
                <button id="end-button" onclick="End()" class="end-button">Logout</button>
            </div>
        </div>

        
        <div class="content active">
            <iframe id="select-iframe" src="./select-file.php" width="100%" frameborder="0"></iframe>
        </div>
        <div class="content">
            <iframe id="tissue-iframe" src="./tissue-quality.php" width="100%" frameborder="0"></iframe>
        </div>
        <div class="content">
            <iframe src="./borders.php" width="100%" frameborder="0"></iframe>
        </div>
        <div class="content">
            <iframe src="./morphology.php" width="100%" frameborder="0"></iframe>
        </div>
        <?php
            if ($activateTab){
        ?>
            <div class="content">
                <iframe src="./bone.php" width="100%" frameborder="0"></iframe>
            </div>
        <?php
        }
        ?>
        <div class="content">
            <iframe src="./summary.php" width="100%" frameborder="0"></iframe>
        </div>

        <div class="navigation-arrows">
                <button id="prev-button" disabled><<</button>
                <button id="next-button" disabled>>></button>
        </div>
    </div>
    <footer style="text-align: center; background-color: #f2f2f2;">
        &copy; 2023 | UNIZAR | Proyecto M2TI para el análisis digital de enfermedades en tendones
    </footer>
</body>
</html>
<script>
    // Function to enable the next tab after form submission
    function enableTissueTab() {
        const tabTissue = document.getElementById('tab-tissue');
        document.querySelector ('#next-button').disabled=false;
        document.querySelector ('#prev-button').disabled=false;

        // Enable the next tab and remove the "disabled" class
        tabTissue.classList.remove('disabled');
    }

    function enableBordersTab() {
        const tabBorders = document.getElementById('tab-borders');

        // Enable the next tab and remove the "disabled" class
        tabBorders.classList.remove('disabled');
    }

    function enableMorphologyTab() {
        const tabMorphology = document.getElementById('tab-morphology');

        // Enable the next tab and remove the "disabled" class
        tabMorphology.classList.remove('disabled');
    }

    <?php
    if ($activateTab){
    ?>
    function enableBoneTab() {
        const tabBone = document.getElementById('tab-bone');

        // Enable the next tab and remove the "disabled" class
        tabBone.classList.remove('disabled');
    }
    <?php
    }
    ?>

    function enableSummaryTab() {
        const tabSummary = document.getElementById('tab-summary');

        // Enable the next tab and remove the "disabled" class
        tabSummary.classList.remove('disabled');
    }

    // function checkForReload() {
    //         if (localStorage.getItem('reloadPage') === 'true') {
    //             localStorage.removeItem('reloadPage');
    //             location.reload();
    //         }
    //     }

    
    // // Check for reload flag every second
    // setInterval(checkForReload, 1000);
</script>

