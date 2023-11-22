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
            error_log("Requisicao recebida: \nCorpo da mensagem: \n" . json_encode($data, JSON_PRETTY_PRINT));
            $registro = $data['registro'];
            $senha = $data['senha'];
            $validacao = validarLogin($registro, $senha);
            $resultado;
            if ($validacao == null) {
                $resultado = array(
                    "success" => false,
                    "message" => 'Credenciais invalidas!'
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
                    "registro" => $registro,
                    "token" => $token,
                    "message" => "Login efetuado com sucesso!",
                    "success" => true
                );
                http_response_code(200);
            }
            $json = json_encode($resultado);
            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
            echo $json;
            break;
        case '/logout':
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenlogout = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                error_log("Requisicao recebida: \nCabecalho: \n" . "     Authorization: " . $_SERVER['HTTP_AUTHORIZATION']);
                $bd = Conexao::get();
                $query = $bd->prepare("SELECT count(*) FROM usuarios_logados WHERE token = :ptoken");
                $query->bindParam(':ptoken', $tokenlogout);
                $query->execute();
                $usuario = $query->fetch() ?? null;
                $resultadocount = $usuario['count(*)'];
                if ($resultadocount == 1) {
                    logout($tokenlogout);
                    $resultado = array(
                        "success" => true,
                        "message" => "Logout bem-sucedido!"
                    );
                    http_response_code(200);
                    $json = json_encode($resultado);
                    error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                    echo $json;
                } else if ($resultadocount == 0) {
                    $resultado = array(
                        "success" => false,
                        "message" => "Nao autenticado, token invalido!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                    echo $json;
                }

            } else {
                $resultado = array(
                    "success" => false,
                    "message" => "Nao autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                echo $json;
            }

            break;
        case '/usuarios':
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokencad = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                error_log("Requisicao recebida: \nCabecalho: \n" . "     Authorization: " . $_SERVER['HTTP_AUTHORIZATION']);
                $bd = Conexao::get();
                $query = $bd->prepare("SELECT count(*), tipo_usuario FROM usuarios_logados WHERE token = :ptoken");
                $query->bindParam(':ptoken', $tokencad);
                $query->execute();
                $usuario = $query->fetch() ?? null;
                $resultadocount = $usuario['count(*)'];
                $tipousuario = $usuario['tipo_usuario'];
                if ($resultadocount == 1) {
                    if ($tipousuario == 1) {
                        $json = file_get_contents('php://input');
                        $data = json_decode($json, true);
                        error_log("Corpo da mensagem: \n" . json_encode($data, JSON_PRETTY_PRINT));
                        $registro = $data['registro'];
                        $nome = $data['nome'];
                        $email = $data['email'];
                        $senha = $data['senha'];
                        $tipo_usuario = $data['tipo_usuario'];
                        if (possuiLetras($registro)) {
                            $resultado = array(
                                "success" => false,
                                "message" => "O registro nao pode conter letras!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        } else {
                            $verificacao = novoUsuario($registro, $nome, $email, $senha, $tipo_usuario);
                            if ($verificacao == true) {
                                $resultado = array(
                                    "success" => true,
                                    "message" => "Usuario criado com sucesso!"
                                );
                                http_response_code(200);
                                $json = json_encode($resultado);
                                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                                echo $json;
                            } else {
                                $resultado = array(
                                    "success" => false,
                                    "message" => "Registro ou email ja existe na base de dados!"
                                );
                                http_response_code(403);
                                $json = json_encode($resultado);
                                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                                echo $json;
                            }
                        }
                    } else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }
                } else {
                    $resultado = array(
                        "success" => false,
                        "message" => "Nao autenticado!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                    echo $json;
                }
            } else {
                $resultado = array(
                    "success" => false,
                    "message" => "Nao autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                echo $json;
            }
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (str_contains($_SERVER['REQUEST_URI'], '/usuarios')) {
        $parametro = str_replace('/usuarios', '', $_SERVER['REQUEST_URI']);
        if (strlen($parametro) == 0) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenuser = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                error_log("Requisicao recebida: \nCabecalho: \n" . "     Authorization: " . $_SERVER['HTTP_AUTHORIZATION']);
                $bd = Conexao::get();
                $query = $bd->prepare("SELECT count(*), tipo_usuario FROM usuarios_logados WHERE token = :ptoken");
                $query->bindParam(':ptoken', $tokenuser);
                $query->execute();
                $usuario = $query->fetch() ?? null;
                $resultadocount = $usuario['count(*)'];
                $tipousuario = $usuario['tipo_usuario'];
                if ($resultadocount == 1) {
                    if ($tipousuario == 1) {
                        $result = listaUsuarios();
                        $resultado = array(
                            "usuarios" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $result = dadosUsuario($tokenuser);
                        $resultado = array(
                            "usuarios" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }
                } else {
                    $resultado = array(
                        "success" => false,
                        "message" => "Nao autenticado!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                    echo $json;
                }
            } else {
                $resultado = array(
                    "success" => false,
                    "message" => "Nao autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                echo $json;
            }
        } else {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenuser = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                error_log("Requisicao recebida: \nCabecalho: \n" . "     Authorization: " . $_SERVER['HTTP_AUTHORIZATION']);
                $bd = Conexao::get();
                $query = $bd->prepare("SELECT count(*), tipo_usuario, registro FROM usuarios_logados WHERE token = :ptoken");
                $query->bindParam(':ptoken', $tokenuser);
                $query->execute();
                $usuario = $query->fetch() ?? null;
                $resultadocount = $usuario['count(*)'];
                $tipousuario = $usuario['tipo_usuario'];
                if ($resultadocount == 1) {
                    if ($tipousuario == 1) {
                        $result = listaUsuariosid(str_replace('/', '', $parametro));
                        $resultado = array(
                            "usuario" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;

                    } else {
                        $registrodouser = $usuario['registro'];
                        if (str_replace('/', '', $parametro) != $registrodouser) {
                            $resultado = array(
                                "success" => false,
                                "message" => "Usuario nao autorizado, usuario comum!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        } else {
                            $result = dadosUsuarioid($tokenuser, str_replace('/', '', $parametro));
                            $resultado = array(
                                "usuario" => $result,
                                "success" => true,
                                "message" => "Lista recebida com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        }
                    }
                } else {
                    $resultado = array(
                        "success" => false,
                        "message" => "Nao autenticado!"
                    );
                    http_response_code(401);
                    $json = json_encode($resultado);
                    error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                    echo $json;
                }
            } else {
                $resultado = array(
                    "success" => false,
                    "message" => "Nao autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                echo $json;
            }
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
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (str_contains($_SERVER['REQUEST_URI'], '/usuarios')) {
        $parametro = str_replace('/usuarios', '', $_SERVER['REQUEST_URI']);
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $tokenuser = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
            error_log("Requisicao recebida: \nCorpo da mensagem: \n" . json_encode($data, JSON_PRETTY_PRINT));
            $bd = Conexao::get();
            $query = $bd->prepare("SELECT count(*), tipo_usuario, registro FROM usuarios_logados WHERE token = :ptoken");
            $query->bindParam(':ptoken', $tokenuser);
            $query->execute();
            $usuario = $query->fetch() ?? null;
            $resultadocount = $usuario['count(*)'];
            $tipousuario = $usuario['tipo_usuario'];
            if ($resultadocount == 1) {
                if ($tipousuario == 1) {
                    $result = attusuario($tokenuser, str_replace('/', '', $parametro), $data['nome'], $data['email'], $data['senha']);
                    if ($result == true) {
                        $resultado = array(
                            "success" => true,
                            "message" => "Cadastro atualizado com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao existe!"
                        );
                        http_response_code(401);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }
                } else {
                    $registrodouser = $usuario['registro'];
                    if (str_replace('/', '', $parametro) != $registrodouser) {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $result = attusuario($tokenuser, str_replace('/', '', $parametro), $data['nome'], $data['email'], $data['senha']);
                        $resultado = array(
                            "success" => true,
                            "message" => "Usuario atualizado com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }
                }
            } else {
                $resultado = array(
                    "success" => false,
                    "message" => "Nao autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                echo $json;
            }
        } else {
            $resultado = array(
                "success" => false,
                "message" => "Nao autenticado!"
            );
            http_response_code(401);
            $json = json_encode($resultado);
            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
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
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (str_contains($_SERVER['REQUEST_URI'], '/usuarios')) {
        $parametro = str_replace('/usuarios', '', $_SERVER['REQUEST_URI']);
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $tokenuser = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
            error_log("Requisicao recebida: \nCabecalho: \n" . "     Authorization: " . $_SERVER['HTTP_AUTHORIZATION']);
            $bd = Conexao::get();
            $query = $bd->prepare("SELECT count(*), tipo_usuario, registro FROM usuarios_logados WHERE token = :ptoken");
            $query->bindParam(':ptoken', $tokenuser);
            $query->execute();
            $usuario = $query->fetch() ?? null;
            $resultadocount = $usuario['count(*)'];
            $tipousuario = $usuario['tipo_usuario'];
            if ($resultadocount == 1) {
                if ($tipousuario == 1) {
                    $result = deleteusuario($tokenuser, str_replace('/', '', $parametro));
                    if ($result == true) {
                        $resultado = array(
                            "success" => true,
                            "message" => "Usuario excluido com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao existe!"
                        );
                        http_response_code(401);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }
                } else {
                    $registrodouser = $usuario['registro'];
                    if (str_replace('/', '', $parametro) != $registrodouser) {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $result = deleteusuario($tokenuser, str_replace('/', '', $parametro));
                        $resultado = array(
                            "success" => true,
                            "message" => "Usuario excluido com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }
                }
            } else {
                $resultado = array(
                    "success" => false,
                    "message" => "Nao autenticado!"
                );
                http_response_code(401);
                $json = json_encode($resultado);
                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                echo $json;
            }
        } else {
            $resultado = array(
                "success" => false,
                "message" => "Nao autenticado!"
            );
            http_response_code(401);
            $json = json_encode($resultado);
            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
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
    if ($result != null) {
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
    if ($resultadocount > 0) {
        return false;
    } else {
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

function listaUsuariosid($id)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT registro, nome, email, tipo_usuario FROM usuarios WHERE registro = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $usuarios = $query->fetch(PDO::FETCH_ASSOC) ?? null;
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

function dadosUsuarioid($token, $id)
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
    $usuarios = $query->fetch(PDO::FETCH_ASSOC) ?? null;
    return $usuarios;
}

function possuiLetras($str)
{
    return preg_match('/[a-zA-Z]/', $str) === 1;
}


function attusuario($token, $id, $nome, $email, $senha)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT * FROM usuarios WHERE registro = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $result = $query->fetch() ?? null;
    if ($result == null) {
        return false;
    } else {
        $bd = Conexao::get();
        $query = $bd->prepare("UPDATE usuarios SET nome = :pnome, email = :pemail, senha = :psenha WHERE registro = :pid ");
        $query->bindParam(':pnome', $nome);
        $query->bindParam(':pemail', $email);
        $query->bindParam(':psenha', $senha);
        $query->bindParam(':pid', $id);
        $query->execute();
        return true;
    }
}

function deleteusuario($token, $id)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT * FROM usuarios WHERE registro = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $result = $query->fetch() ?? null;
    if ($result == null) {
        return false;
    } else {
        $bd = Conexao::get();
        $query = $bd->prepare("DELETE FROM usuarios WHERE registro = :pid");
        $query->bindParam(':pid', $id);
        $query->execute();
        return true;
    }
}


?>