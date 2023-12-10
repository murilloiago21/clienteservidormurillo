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
    <div id="container">
    <div id="endereco" style="padding: 0%;">
        <p style="margin: 0%; padding: 0%; text-align: center; font-size: 20px;" id="enderecourl"><?=$url;?></p>
        </div>
        <p style="margin: 0%; padding: 0%; text-align: center; font-size: 20px; position: relative; top: 30px;">Pontos disponíveis</p>
        
        <table id="tabela-dinamica" style="position: relative; top: 40px; left: 135px;">
                <thead>
                    <tr>
                        <th>ID do ponto</th>
                        <th>Nome do ponto</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        <div id="cardlogin" style="height: 420px;">
            <h2 style="position: relative; top: 0px;">Cadastro de segmento</h2>
            <button style="position: absolute; left: 400px; top: 10px; height: 30px; width: 70px; font-size: 16px;" onclick="voltarHome()">Voltar</button>
            <div id="formlogin">
                <label style="position: relative; top: -70px; left: -15px;">Distancia</label>
                <input id="distanciaseg" type="text" style="position: relative; top: -71px; left: -7px;">
                <label style="position: relative; top: -50px; left: -41px; display: block;">Ponto inicial</label>
                <input id="pontoiniseg" type="text" style="position: relative; top: -75px; left: 72px;">
                <label style="position: relative; top: -55px; left: -27px; display: block; ">Ponto final</label>
                <input id="pontofinseg" type="text" style="position: relative; top: -80px; left: 72px;">
                <label style="position: relative; top: -61px; left: 13px; display: block;">Status</label>
                <input id="statusseg" type="text" style="position: relative; top: -85px; left: 72px;">
                <label style="position: relative; top: -61px; left: 0px; display: block;">Direção</label>
                <input id="direcaoseg" type="text" style="position: relative; top: -85px; left: 72px;">
                <button id="btn-enviarcadseg">Enviar</button>
            </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="./assets/script.js"></script>

    <?php
        echo "<script>
            el = document.getElementById('btn-enviarcadseg');
            if(el != null){
                el.addEventListener('click', function(){ cadastrarsegmento('".$url."','".$_SESSION['token']."') });
            }
            var sizetable = document.getElementById('tabela-dinamica').offsetHeight;
            document.getElementById('cardlogin').style.top = 50 + sizetable + `px`;
            document.getElementById('container').style.height = 1000 + sizetable + `px`;
        </script>";
    ?>
</body>
</html>
