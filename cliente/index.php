<?php
    session_start();
    if(isset($_SESSION['logado']) && $_SESSION['logado'] == 'true'){
        if(isset($_POST['acao'])){
            switch($_POST['acao']){
                case 'cadastro':
                    require('./views/cadastro_usuario.php');
                    break;
                case 'leitura':
                    require('./views/lista_usuario.php');
                    break;
            }
        }else{
            require('./views/home.php');
        }
    }else{
        require('./views/login.php');
    }
?>

