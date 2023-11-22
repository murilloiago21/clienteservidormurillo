<?php
    $url = $_SESSION['endip'];
    if(isset($_POST['destroysessao'])){
        session_destroy();
        echo "<script>window.location.replace('../index.php');</script>";
    }
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
        <div id="cardlogin" style="height: 600px;">
            <h2 style="position: relative; top: -20px;">Menu</h2>
            <div id="formlogin">
                <label style="position: relative; top: -100px; left: 115px;">Usuário</label>
                <nav style="position: relative; left: 50px;
                 width: 200px; top: -95px;">
                    <button style="display: block; width: 200px; height: 40px; font-size: 18px;" id="btn-cad">Cadastrar</button>
                    <button style="display: block; width: 200px; height: 40px; font-size: 18px; margin-top: 2px;" id="btn-att">Alterar cadastro</button>
                    <button style="display: block; width: 200px; height: 40px; font-size: 18px; margin-top: 2px;" id="btn-read">Ler dados gerais</button>
                    <button style="display: block; position: relative; width: 140px; left: 60px; height: 40px; font-size: 16px; margin-top: 2px;" id="btn-read-id">Ler dados Registro</button>
                </nav>
                <input type="number" style="position: absolute; top: 53px; width: 50px; left: 50px; height: 34px; text-align: center;" id="numerolerid" value="<?=$_SESSION['registro']?>">
                
                <label style="position: relative; top: -80px; left: 130px;">Rotas</label>
                <nav style="position: relative; left: 50px;
                 width: 200px; top: -75px;">
                    <button style="display: block; width: 200px; height: 40px; font-size: 18px;">Cadastrar rotas</button>
                    <button style="display: block; width: 200px; height: 50px; font-size: 18px; margin-top: 2px;">Atualizar dados de rotas</button>
                    <button style="display: block; width: 200px; height: 40px; font-size: 18px; margin-top: 2px;">Ler dados</button>
                    <button style="display: block; width: 200px; height: 40px; font-size: 18px; margin-top: 2px;">Apagar dados de rotas</button>
                </nav>
                <div style="position: relative; left: 65px;width: 160px; top: -30px;">
                    <button id="fazerlogout" style="position: relative; width: 160px; height: 45px; font-size: 17px; background-color: red; border: none; border-radius: 10px; color: white;">Sair do sistema</button>
                </div>
            </div>
            <form action="../index.php" method="POST">
                <input type="text" style="display: none;" name="destroysessao">
                <button type="submit" style="display: none;" id="sairsessao">
            </form>
            <form action="../index.php" method="POST">
                <input type="text" style="display: none;" name="acao" value="" id="acao">
                <input type="text" style="display: none;" name="idbusca" value="" id="idbusca">
                <button type="submit" style="display: none;" id="enviaracao">
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="./assets/script.js"></script>

    <?php
        echo "<script>
        el = document.getElementById('fazerlogout');
        if(el != null){
            el.addEventListener('click', function(){ enviarLogout('".$url."','".$_SESSION['token']."') });
        }
        el = document.getElementById('btn-cad');
        if(el != null){
            el.addEventListener('click', function(){ acaoHome('cadastro') });
        }
        el = document.getElementById('btn-att');
        if(el != null){
            el.addEventListener('click', function(){ acaoHome('atualizacao') });
        }
        el = document.getElementById('btn-read');
        if(el != null){
            el.addEventListener('click', function(){  acaoHome('leitura') });
        }

        el = document.getElementById('btn-read-id');
        if(el != null){
            el.addEventListener('click', function(){  acaoHome('leitura-id') });
        }

        el = document.getElementById('btn-erase');
        if(el != null){
            el.addEventListener('click', function(){ excluircadastro('".$url."','".$_SESSION['token']."') });
        }
        </script>";
    ?>
</body>
</html>
