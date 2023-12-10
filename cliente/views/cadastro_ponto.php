<?php
    $url = $_SESSION['endip'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <div id="header">
        <h1>Sistema de auxílio para deslocamento a pé de pessoas com baixa visão (SADPPBV)</h1>
    </div>
    <div id="container" style="height: 800px;">
    <div id="endereco" style="padding: 0%;">
        <p style="margin: 0%; padding: 0%; text-align: center; font-size: 20px;" id="enderecourl"><?=$url;?></p>
        </div>
        <div id="cardlogin" style="height: 420px;">
            <h2 style="position: relative; top: 0px;">Cadastro de pontos</h2>
            <button style="position: absolute; left: 400px; top: 10px; height: 30px; width: 70px; font-size: 16px;" onclick="voltarHome()">Voltar</button>
            <div id="formlogin">
                <label style="position: relative; top: -50px; left: 80px;">Nome do ponto:</label>
                <input id="nomepon" type="text" style="position: relative; top: -20px; left: 47px;">
                <button id="btn-enviarcadpon" style="top: 50px;">Enviar</button>
            </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="./assets/script.js"></script>

    <?php
        echo "<script>
            el = document.getElementById('btn-enviarcadpon');
            if(el != null){
                el.addEventListener('click', function(){ cadastrarponto('".$url."','".$_SESSION['token']."') });
            }
        </script>";
    ?>
</body>
</html>
