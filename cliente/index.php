<?php
    session_start();
    if(isset($_GET['iddel']) && isset($_SESSION['registro'])){
        if($_GET['iddel'] == $_SESSION['registro']){
            session_destroy();
            echo "<script>window.location.replace('../index.php');</script>";
        }else{
            require('./views/lista_usuario.php');
            echo "<script>lerUsuarios('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
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
                case 'cad-seg':
                    require('./views/cadastro_segmento.php');
                    echo "<script>lerPontos('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
                    break;
                case 'leitura-seg':
                    require('./views/lista_segmento.php');
                    echo "<script>lerSegmentos('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
                    break;
                case 'leitura-id-seg':
                    require('./views/lista_segmento.php');
                    echo "<script>lerSegmentosid('".$_SESSION['endip']."','".$_SESSION['token']."','".$_POST['idbusca']."'); </script>";
                    break;
                case 'atualizacao-seg':
                    require('./views/alterar_segmento.php');
                    echo "<script>lerPontos('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
                    break;
                case 'cad-pon':
                    require('./views/cadastro_ponto.php');
                    break;
                case 'leitura-pon':
                    require('./views/lista_pontos.php');
                    echo "<script>lerPontos('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
                    break;
                case 'leitura-id-pon':
                    require('./views/lista_pontos.php');
                    echo "<script>lerPontosid('".$_SESSION['endip']."','".$_SESSION['token']."','".$_POST['idbusca']."'); </script>";
                    break;
                case 'atualizacao-pon':
                    require('./views/alterar_ponto.php');
                    break;
                 case 'rota':
                    require('./views/rotas.php');
                    echo "<script>lerPontos('".$_SESSION['endip']."','".$_SESSION['token']."'); </script>";
                    
                    break;
            }
        }else{
            require('./views/home.php');
        }
    }else{
        require('./views/login.php');
    }
    
?>

