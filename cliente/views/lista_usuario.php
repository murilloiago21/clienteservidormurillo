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
            <p style="margin: 0%; padding: 0%; text-align: center; font-size: 20px;" id="enderecourl">
                <?= $url; ?>
            </p>
        </div>
        <div id="cardlogin" style="height: 700px;">
            <h2 style="position: relative; top: -20px;">Lista</h2>
            <button style="position: absolute; left: 400px; top: 10px; height: 30px; width: 70px; font-size: 16px;" onclick="voltarHome()">Voltar</button>
            <table id="tabela-dinamica">
                <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo_usuario</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <p style="margin: 0; padding: 0; left: 15px; position: relative; top: 70px; font-size: 20px;">ID</p>
            <button style="display: block; position: relative; width: 140px; left: 130px; height: 35px; font-size: 16px; margin-top: 40px; background-color: red; border: none; border-radius: 7px; color: white;" id="btn-erase">Apagar cadastro</button>
            <input type="number" style="position: relative; top: -38px; width: 50px; left: 50px; height: 34px; text-align: center;" id="numeroleriddel" value="<?=$_SESSION['registro']?>">
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="./assets/script.js"></script>
    <?php
        echo "<script>
            el = document.getElementById('btn-erase');
            if(el != null){
                el.addEventListener('click', function(){ excluircadastro('".$url."','".$_SESSION['token']."') });
            }
        </script>";
    ?>
</body>

</html>