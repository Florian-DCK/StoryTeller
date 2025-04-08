<?php
ob_start();
include('../../tests/testDB.php');
$output = ob_get_clean();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tests de base de données</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .output { background-color: #f5f5f5; padding: 15px; border-radius: 5px; }
        .pass { color: green; }
        .fail { color: red; }
    </style>
</head>
<body>
    <h1>Résultats des tests de base de données</h1>
    <div class="output">
        <?php 
        $coloredOutput = preg_replace('/PASS: (.*?)\n/', '<span class="pass">PASS: $1</span><br>', $output);
        $coloredOutput = preg_replace('/FAIL: (.*?)\n/', '<span class="fail">FAIL: $1</span><br>', $coloredOutput);
        $coloredOutput = preg_replace('/===(.+?)===/', '<strong>===$1===</strong>', $coloredOutput);
        echo nl2br($coloredOutput); 
        ?>
    </div>
</body>
</html>