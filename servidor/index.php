<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header('Content-Type: application/json; charset=utf-8;');
require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

require('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_SERVER['REQUEST_URI']) {
        case '/login':
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $registro = $data['registro'];
            $senha = $data['senha'];
            $validacao = validarLogin($registro, $senha);
            $resultado;
            if ($validacao == null) {
                $resultado = array(
                    "success" => false,
                    "message" => 'Credenciais inválidas!'
                );
                http_response_code(401);
            } else {
                $secret_key = 'Dv#T5@zR*wYq6p$GnHjZ^B^vXs&M8K@a';
                $dados = array(
                    'registro' => strval($registro),
                    'senha' => $senha,
                );
                $token = JWT::encode($dados, $secret_key, 'HS256');

                adicionarLogin($registro, $token);

                $resultado = array(
                    "token" => $token,
                    "message" => "Login efetuado com sucesso!",
                    "success" => true
                );
                http_response_code(200);
            }
            $json = json_encode($resultado);
            echo $json;
            break;
        case '/logout':
                if(isset($_SERVER['HTTP_AUTHORIZATION'])){
                    $tokenlogout = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                    $bd = Conexao::get();
                    $query = $bd->prepare("SELECT count(*) FROM usuarios_logados WHERE token = :ptoken");
                    $query->bindParam(':ptoken', $tokenlogout);
                    $query->execute(); 
                    $usuario = $query->fetch() ?? null;
                    $resultadocount = $usuario['count(*)'];
                    if($resultadocount == 1){
                        logout($tokenlogout);
                        $resultado = array(
                            "success" => true,
                            "message" => "Logout bem-sucedido!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        echo $json;
                    }else if($resultadocount == 0){
                        $resultado = array(
                            "success" => false,
                            "message" => "Não autenticado, token inválido!"
                        );
                        http_response_code(401);
                        $json = json_encode($resultado);
                        echo $json;
                    }
                    
                }else {
                    $resultado = array(
                        "success" => false,
                        "message" => "Não autenticado!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    echo $json;
                    }
                
            break;
        case '/usuarios':
            if(isset($_SERVER['HTTP_AUTHORIZATION'])){
                $tokencad = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                $bd = Conexao::get();
                $query = $bd->prepare("SELECT count(*), tipo_usuario FROM usuarios_logados WHERE token = :ptoken");
                $query->bindParam(':ptoken', $tokencad);
                $query->execute(); 
                $usuario = $query->fetch() ?? null;
                $resultadocount = $usuario['count(*)'];
                $tipousuario = $usuario['tipo_usuario'];
                if($resultadocount == 1){
                    if($tipousuario == 1){
                        $json = file_get_contents('php://input');
                        $data = json_decode($json, true);
                        $registro = $data['registro'];
                        $nome = $data['nome'];
                        $email = $data['email'];
                        $senha = $data['senha'];
                        $tipo_usuario = $data['tipo_usuario'];
                        $verificacao = novoUsuario($registro,$nome,$email,$senha,$tipo_usuario);
                        if($verificacao == true){
                            $resultado = array(
                                "success" => true,
                                "message" => "Usuário criado com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            echo $json;
                        }else{
                            $resultado = array(
                                "success" => false,
                                "message" => "Registro ou email ja existe na base de dados!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            echo $json;
                        }
                    }else{  
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuário não autorizado, usuário comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        echo $json;
                    }
                }else {
                    $resultado = array(
                        "success" => false,
                        "message" => "Não autenticado!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    echo $json;
                }
            }else {
                $resultado = array(
                    "success" => false,
                    "message" => "Não autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                echo $json;
            }
        break;
    }
}else if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(str_contains($_SERVER['REQUEST_URI'], '/usuarios')){
        $parametro = str_replace('/usuarios', '', $_SERVER['REQUEST_URI']);
        if(strlen($parametro) == 0){
            if(isset($_SERVER['HTTP_AUTHORIZATION'])){
                $tokenuser = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                $bd = Conexao::get();
                $query = $bd->prepare("SELECT count(*), tipo_usuario FROM usuarios_logados WHERE token = :ptoken");
                $query->bindParam(':ptoken', $tokenuser);
                $query->execute(); 
                $usuario = $query->fetch() ?? null;
                $resultadocount = $usuario['count(*)'];
                $tipousuario = $usuario['tipo_usuario'];
                if($resultadocount == 1){
                    if($tipousuario == 1){
                        $result = listaUsuarios();
                        $resultado = array(
                            "usuarios" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        echo $json;
                    }else{  
                        $result = dadosUsuario($tokenuser);
                        $resultado = array(
                            "usuarios" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        echo $json;
                    }
                }else {
                    $resultado = array(
                        "success" => false,
                        "message" => "Não autenticado!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    echo $json;
                }
            }else {
                $resultado = array(
                    "success" => false,
                    "message" => "Não autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                echo $json;
            }
        }else{
            $resultado = array(
                "success" => false,
                "message" => "Funcionalidade ainda não implementada!"
            );
            http_response_code(401);
            $json = json_encode($resultado);
            echo $json;
        }
        


/*
        $resultado = array(
            "success" => true,
            "message" => "Contem!"
        );
        http_response_code(200);
        $json = json_encode($resultado);
        echo $json;*/
    }
}else {
    //print_r($_SERVER);
}

//LOGIN
function validarLogin($registro, $senha)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT * FROM usuarios WHERE registro = :registro AND senha = :senha");
    $query->bindParam(':registro', $registro);
    $query->bindParam(':senha', $senha);
    $query->execute();
    $result = $query->fetch() ?? null;
    return $result;
}

function adicionarLogin($registro, $token)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT tipo_usuario FROM usuarios WHERE  registro = :pregistro");
    $query->bindParam(':pregistro', $registro);
    $query->execute();
    $result = $query->fetch() ?? null;
    if($result != null){
        $bd = Conexao::get();
        $query = $bd->prepare("INSERT INTO usuarios_logados(registro, token, tipo_usuario) VALUES(:pregistro, :ptoken, :ptipo_usuario)");
        $query->bindParam(':pregistro', $registro);
        $query->bindParam(':ptoken', $token);
        $query->bindParam(':ptipo_usuario', $result['tipo_usuario']);
        $query->execute();
    }
}

//LOGOUT

function logout($tokenlogout)
{
    $bd = Conexao::get();
    $query = $bd->prepare("DELETE FROM usuarios_logados WHERE token = :ptoken");
    $query->bindParam(':ptoken', $tokenlogout);
    $query->execute();
}

//CADASTRO USUARIO
function novoUsuario($registro, $nome, $email, $senha, $tipo_usuario)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT count(*) FROM usuarios WHERE registro = :pregistro");
    $query->bindParam(':pregistro', $registro);
    $query->execute(); 
    $usuario = $query->fetch() ?? null;
    $resultadocount = $usuario['count(*)'];
    if($resultadocount > 0){
        return false;
    }else{
        $bd = Conexao::get();
        $query = $bd->prepare("INSERT INTO usuarios(registro, nome, email, senha, tipo_usuario) VALUES (:pregistro, :pnome, :pemail, :psenha, :ptipo_usuario)");
        $query->bindParam(':pregistro', $registro);
        $query->bindParam(':pnome', $nome);
        $query->bindParam(':pemail', $email);
        $query->bindParam(':psenha', $senha);
        $query->bindParam(':ptipo_usuario', $tipo_usuario);
        $query->execute();
        return true;
    }
}

function listaUsuarios()
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT registro, nome, email, tipo_usuario FROM usuarios");
    $query->execute();
    $usuarios = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $usuarios[] = $row;
    }
    return $usuarios;
}

function dadosUsuario($token)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT registro FROM usuarios_logados WHERE token = :ptoken");
    $query->bindParam(':ptoken', $token);
    $query->execute();
    $result = $query->fetch() ?? null;

    $bd = Conexao::get();
    $query = $bd->prepare("SELECT registro, nome, email, tipo_usuario FROM usuarios WHERE registro = :pregistro");
    $query->bindParam(':pregistro', $result['registro']);
    $query->execute();
    $usuarios = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $usuarios[] = $row;
    }
    return $usuarios;
}


?>