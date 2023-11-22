<?php
    session_start();
    if(isset($_GET['iddel']) && isset($_SESSION['registro'])){
        if($_GET['iddel'] == $_SESSION['registro']){
            session_destroy();
            echo "<script>window.location.replace('../index.php');</script>";
        }
    }
    if(isset($_SESSION['logado']) && $_SESSION['logado'] == 'true'){
        if(isset($_POST['acao'])){
            switch($_POST['acao']){
                case 'cadastro':
                    require('./views/cadastro_usuario.php');
                    break;
                case 'leitura':
                    require('./views/lista_usuario.php');
                    echo "<script>lerUsuarios('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
                    break;
                case 'leitura-id':
                    require('./views/lista_usuario.php');
                    echo "<script>lerUsuariosid('".$_SESSION['endip']."','".$_SESSION['token']."','".$_POST['idbusca']."'); </script>";
                    break;
                case 'atualizacao':
                    require('./views/alterar_cadastro_usuario.php');
                    break;
            }
        }else{
            require('./views/home.php');
        }
    }else{
        require('./views/login.php');
    }
    
?>

