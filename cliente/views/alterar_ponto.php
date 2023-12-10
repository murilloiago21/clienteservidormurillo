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
            <h2 style="position: relative; top: -80px; margin-top: 120px;">Alterar ponto</h2>
            <button style="position: absolute; left: 400px; top: 10px; height: 30px; width: 70px; font-size: 16px;" onclick="voltarHome()">Voltar</button>
            <div id="formlogin">
                
                <p style="position: absolute; margin: 0px; padding: 0px; top: -110px;"> ID do ponto:</p>
                
                <input type="number" style="position: absolute; top: -110px; width: 60px; left: 110px; height: 22px; text-align: center;" id="numeroleratt">
                    <label style="position: relative; top: -70px; left: 0px;">Novo nome</label>
                    <input id="nomepon" type="text" style="position: relative; top: -93px; left: 110px;">
                    
                    <button id="btn-enviaratt-pon">Enviar</button>
            </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="./assets/script.js"></script>

    <?php
        echo "<script>

        el = document.getElementById('btn-enviaratt-pon');
        if(el != null){
            el.addEventListener('click', function(){ atualizarPonto('".$url."','".$_SESSION['token']."') });
        }
        </script>";
    ?>
</body>
</html>
