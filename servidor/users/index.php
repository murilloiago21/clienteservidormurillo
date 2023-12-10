<?php
    require('../conexao.php');
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id, registro FROM usuarios_logados");
    $query->execute();
    $usuarios = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $usuarios[] = $row;
    }
    
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    #tabela-dinamica{
    border: 1px solid black;
    width: 480px;
    text-align: center;
    padding: 0px;
    border-collapse: collapse;
}

#tabela-dinamica tr td{
    border: 1px solid black;
    padding: none;
    margin: 0;
}

#tabela-dinamica th {
    border: 1px solid black;
    padding: none;
    margin: 0;
}
</style>

<body>
    <p>Usuarios logados</p>
    <table id="tabela-dinamica">
        <thead>
            <tr>
                <th>ID usuario</th>
                <th>registro</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($usuarios as $key => $value) {
            ?>
            <tr>
                <td><?=$usuarios[$key]['id'];?></td>
                <td><?=$usuarios[$key]['registro'];?></td>
            </tr>
            <?php
             }
            ?>
        </tbody>
    </table>
    <?php
        echo '<script>
        setTimeout(() => {
            window.location.replace(`../users/index.php`);
          }, `5000`);
        </script>';
    ?>
</body>

</html>