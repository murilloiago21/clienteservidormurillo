<?php  
    if(isset($_POST['registro'])){
        $_SESSION['logado'] = 'true';
        $_SESSION['registro'] = $_POST['registro'];
        $_SESSION['endip'] = $_POST['endip'];
        $_SESSION['token'] = $_POST['token'];
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
    <div id="container">
        <div id="endereco">
            <label for="enderecoip">Endereço IP</label>
            <input type="text" id="enderecoip">
            <label for="enderecoporta" style="margin-left: 15px;">Porta</label>
            <input type="text" id="enderecoporta" style="width: 90px;">
        </div>
        <div id="cardlogin">
            <h2>Login</h2>
            <div id="formlogin">
                <label for="registro">Registro</label>
                <input type="text" id="registro"><br><br><br>
                <label for="senha" style="position: relative; left: 16px;">Senha</label>
                <input type="password" id="senha">
                <button id="btn-login">Enviar</button>
            </div>
            <form action="../index.php" method="POST">
                <input type="text" style="display: none;" id="regsessao" name="registro">
                <input type="text" style="display: none;" id="endip" name="endip">
                <input type="text" style="display: none;" id="token" name="token">
                <button type="submit" style="display: none;" id="sessao">
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="./assets/script.js"></script>
    
</body>
</html>
