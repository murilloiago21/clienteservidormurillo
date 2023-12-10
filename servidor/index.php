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

                $retorno = adicionarLogin($registro, $token);
                if($retorno == true){
                    $resultado = array(
                        "registro" => $registro,
                        "token" => $token,
                        "message" => "Login efetuado com sucesso!",
                        "success" => true
                    );
                    http_response_code(200);
                }else{
                    $resultado = array(
                        "message" => "Usuario ja logado!",
                        "success" => false
                    );
                    http_response_code(403);
                }
                
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
                        if (
                            (strlen($registro) > 0) && (strlen($nome) > 0) && (strlen($email) > 0) &&
                            (strlen($tipo_usuario)) > 0 && $senha != 'd41d8cd98f00b204e9800998ecf8427e'
                        ) {
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
                                        "message" => "Registro ja existe na base de dados!"
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
                                "message" => "Os campos nao podem ser vazios!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
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
        case '/segmentos':
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

                        $distancia = $data['distancia'];
                        $ponto_inicial = $data['ponto_inicial'];
                        $ponto_final = $data['ponto_final'];
                        $status = $data['status'];
                        $direcao = $data['direcao'];

                        if (
                            strlen($distancia) > 0 && strlen($ponto_inicial) > 0 && strlen($ponto_final) > 0 &&
                            strlen($status) > 0 && strlen($distancia) > 0
                        ) {
                            cadastrarsegmento($distancia, $ponto_inicial, $ponto_final, $status, $direcao);
                            $resultado = array(
                                "success" => true,
                                "message" => "Segmento cadastrado com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        } else {
                            $resultado = array(
                                "success" => false,
                                "message" => " Os campos nao podem ser vazios!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
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

        case '/pontos':
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

                        $nomeponto = $data['nome'];
                        if (strlen($nomeponto) > 0) {
                            cadastrarponto($nomeponto);
                            $resultado = array(
                                "success" => true,
                                "message" => "Ponto cadastrado com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        } else {
                            $resultado = array(
                                "success" => false,
                                "message" => " Os campos nao podem ser vazios!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
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

            case '/rotas':
                if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                    $tokencad = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                    error_log("Requisicao recebida: \nCabecalho: \n" . "     Authorization: " . $_SERVER['HTTP_AUTHORIZATION']);
                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);
                    $result = lerrotas($data['origem'], $data['destino']);
                    $resultado = array(
                        "success" => true,
                        "message" => "Rotas recebidas!",
                        "rota" => $result
                    );
                    http_response_code(200);
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
                        if($result == null){
                            $resultado = array(
                                "success" => false,
                                "message" => "Usuario nao existe!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        }else{
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
                            if($result == null){
                                $resultado = array(
                                    "success" => false,
                                    "message" => "Usuario nao existe!"
                                );
                                http_response_code(403);
                                $json = json_encode($resultado);
                                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                                echo $json;
                            }else{
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
    } else if (str_contains($_SERVER['REQUEST_URI'], '/segmentos')) {

        $parametro = str_replace('/segmentos', '', $_SERVER['REQUEST_URI']);
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
                    if (true) {
                        $result = listaSegmentos();
                        $resultado = array(
                            "segmentos" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;

                    } /*else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }*/
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
                    if (true) {
                        $result = listaSegmentosid(str_replace('/', '', $parametro));
                        if($result == null){
                            $resultado = array(
                                "success" => false,
                                "message" => "Segmento nao existe!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        }else{
                            $resultado = array(
                                "segmento" => $result,
                                "success" => true,
                                "message" => "Lista recebida com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        }
                    } /*else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }*/
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
    }else if (str_contains($_SERVER['REQUEST_URI'], '/pontos')) {
        $parametro = str_replace('/pontos', '', $_SERVER['REQUEST_URI']);
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
                    if (true) {
                        $result = listaPontos();
                        $resultado = array(
                            "pontos" => $result,
                            "success" => true,
                            "message" => "Lista recebida com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;

                    } /*else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }*/
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
                    if (true) {
                        $result = listaPontosid(str_replace('/', '', $parametro));
                        if($result == null){
                            $resultado = array(
                                "success" => false,
                                "message" => "Ponto nao existe!"
                            );
                            http_response_code(403);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        }else{
                            $resultado = array(
                                "ponto" => $result,
                                "success" => true,
                                "message" => "Lista recebida com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        }
                    } /*else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Usuario nao autorizado, usuario comum!"
                        );
                        http_response_code(403);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    }*/
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
            $nome = $data['nome'];
            $email = $data['email'];
            $senha = $data['senha'];
            if ((strlen($nome) > 0) && (strlen($email) > 0) && $senha != 'd41d8cd98f00b204e9800998ecf8427e') {
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
                            if ($result == true) {
                                $resultado = array(
                                    "success" => true,
                                    "message" => "Usuario atualizado com sucesso!"
                                );
                                http_response_code(200);
                                $json = json_encode($resultado);
                                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                                echo $json;
                            }else {
                                $resultado = array(
                                    "success" => false,
                                    "message" => "Usuario nao existe!"
                                );
                                http_response_code(401);
                                $json = json_encode($resultado);
                                error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                                echo $json;
                            }
                            
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
                    "message" => "Os campos nao podem ser vazios!"
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




        /*
                $resultado = array(
                    "success" => true,
                    "message" => "Contem!"
                );
                http_response_code(200);
                $json = json_encode($resultado);
                echo $json;*/
    } else if (str_contains($_SERVER['REQUEST_URI'], '/segmentos')) {
        $parametro = str_replace('/segmentos', '', $_SERVER['REQUEST_URI']);
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
            $distancia = $data['distancia'];
            $ponto_inicial = $data['ponto_inicial'];
            $ponto_final = $data['ponto_final'];
            $status = $data['status'];
            $direcao = $data['direcao'];

            if (strlen($distancia) > 0 && strlen($ponto_inicial) > 0 && strlen($ponto_final) > 0
                && strlen($status) > 0 && strlen($direcao) > 0) {
                if ($resultadocount == 1) {
                    if ($tipousuario == 1) {
                        $result = attsegmento($tokenuser, str_replace('/', '', $parametro), $distancia, $ponto_inicial, $ponto_final, $status, $direcao);
                        if ($result == true) {
                            $resultado = array(
                                "success" => true,
                                "message" => "Segmento atualizado com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        } else {
                            $resultado = array(
                                "success" => false,
                                "message" => "Segmento nao existe!"
                            );
                            http_response_code(401);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
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
            }else {
                $resultado = array(
                    "success" => false,
                    "message" => "Os campos nao podem ser vazios!"
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




        /*
                $resultado = array(
                    "success" => true,
                    "message" => "Contem!"
                );
                http_response_code(200);
                $json = json_encode($resultado);
                echo $json;*/
    }else if (str_contains($_SERVER['REQUEST_URI'], '/pontos')) {
        $parametro = str_replace('/pontos', '', $_SERVER['REQUEST_URI']);
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

            $nomeponto = $data['nome'];
            if (strlen($nomeponto) > 0) {
                if ($resultadocount == 1) {
                    if ($tipousuario == 1) {
                        $result = attponto($tokenuser, str_replace('/', '', $parametro), $nomeponto);
                        if ($result == true) {
                            $resultado = array(
                                "success" => true,
                                "message" => "Ponto atualizado com sucesso!"
                            );
                            http_response_code(200);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
                        } else {
                            $resultado = array(
                                "success" => false,
                                "message" => "Ponto nao existe!"
                            );
                            http_response_code(401);
                            $json = json_encode($resultado);
                            error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                            echo $json;
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
            }else {
                $resultado = array(
                    "success" => false,
                    "message" => "Os campos nao podem ser vazios!"
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
    }else if (str_contains($_SERVER['REQUEST_URI'], '/segmentos')) {
        $parametro = str_replace('/segmentos', '', $_SERVER['REQUEST_URI']);
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
                    $result = deletesegmento($tokenuser, str_replace('/', '', $parametro));
                    if ($result == true) {
                        $resultado = array(
                            "success" => true,
                            "message" => "Segmento excluido com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Segmento nao existe!"
                        );
                        http_response_code(401);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
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




        /*
                $resultado = array(
                    "success" => true,
                    "message" => "Contem!"
                );
                http_response_code(200);
                $json = json_encode($resultado);
                echo $json;*/
    }else if (str_contains($_SERVER['REQUEST_URI'], '/pontos')) {
        $parametro = str_replace('/pontos', '', $_SERVER['REQUEST_URI']);
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
                    $result = deleteponto($tokenuser, str_replace('/', '', $parametro));
                    if ($result == true) {
                        $resultado = array(
                            "success" => true,
                            "message" => "Ponto excluido com sucesso!"
                        );
                        http_response_code(200);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
                    } else {
                        $resultado = array(
                            "success" => false,
                            "message" => "Ponto nao existe!"
                        );
                        http_response_code(401);
                        $json = json_encode($resultado);
                        error_log("JSON enviado:\n" . json_encode($resultado, JSON_PRETTY_PRINT));
                        echo $json;
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




        /*
                $resultado = array(
                    "success" => true,
                    "message" => "Contem!"
                );
                http_response_code(200);
                $json = json_encode($resultado);
                echo $json;*/
    }
} else {
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
        $query = $bd->prepare("SELECT * FROM usuarios_logados WHERE registro = :pregistro AND token = :ptoken");
        $query->bindParam(':pregistro', $registro);
        $query->bindParam(':ptoken', $token);
        $query->execute();
        $user = $query->fetch() ?? null;
        if($user == null){
            $bd = Conexao::get();
            $query = $bd->prepare("INSERT INTO usuarios_logados(registro, token, tipo_usuario) VALUES(:pregistro, :ptoken, :ptipo_usuario)");
            $query->bindParam(':pregistro', $registro);
            $query->bindParam(':ptoken', $token);
            $query->bindParam(':ptipo_usuario', $result['tipo_usuario']);
            $query->execute();
            return true;
        }else{
            return false;
        }
    }else{
        return false;
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

function cadastrarsegmento($distancia, $ponto_inicial, $ponto_final, $status, $direcao)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT nome FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $ponto_inicial);
    $query->execute();
    $nomepontoinicial = $query->fetch() ?? null;

    $bd = Conexao::get();
    $query = $bd->prepare("SELECT nome FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $ponto_final);
    $query->execute();
    $nomepontofinal = $query->fetch() ?? null;

    if(($nomepontoinicial != null) && ($nomepontofinal != null)){
        $bd = Conexao::get();
        $query = $bd->prepare("INSERT INTO segmentos(distancia, ponto_inicial, ponto_final, status, 
        direcao) VALUES (:pdistancia, :pponto_inicial, :pponto_final, :pstatus, :pdirecao)");
        $query->bindParam(':pdistancia', $distancia);
        $query->bindParam(':pponto_inicial', $nomepontoinicial['nome']);
        $query->bindParam(':pponto_final', $nomepontofinal['nome']);
        $query->bindParam(':pstatus', $status);
        $query->bindParam(':pdirecao', $direcao);
        $query->execute();
    }else{
        $bd = Conexao::get();
        $query = $bd->prepare("INSERT INTO segmentos(distancia, ponto_inicial, ponto_final, status, 
        direcao) VALUES (:pdistancia, :pponto_inicial, :pponto_final, :pstatus, :pdirecao)");
        $query->bindParam(':pdistancia', $distancia);
        $query->bindParam(':pponto_inicial', $ponto_inicial);
        $query->bindParam(':pponto_final', $ponto_final);
        $query->bindParam(':pstatus', $status);
        $query->bindParam(':pdirecao', $direcao);
        $query->execute();
    }
    
}
function listaSegmentos()
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id AS segmento_id, distancia, ponto_inicial, ponto_final, status, direcao FROM segmentos");
    $query->execute();
    $segmentos = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $segmentos[] = $row;
    }
    return $segmentos;
}

function listaSegmentosid($id)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id AS segmento_id, distancia, ponto_inicial, ponto_final, status, direcao FROM segmentos WHERE id = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $usuarios = $query->fetch(PDO::FETCH_ASSOC) ?? null;
    return $usuarios;
}

function attsegmento($token, $id, $distancia, $ponto_inicial, $ponto_final, $status, $direcao)
{

    $bd = Conexao::get();
    $query = $bd->prepare("SELECT nome FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $ponto_inicial);
    $query->execute();
    $nomepontoinicial = $query->fetch() ?? null;

    $bd = Conexao::get();
    $query = $bd->prepare("SELECT nome FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $ponto_final);
    $query->execute();
    $nomepontofinal = $query->fetch() ?? null;

    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id FROM segmentos WHERE id = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $result = $query->fetch() ?? null;
    if ($result == null) {
        return false;
    } else {

        if(($nomepontoinicial != null) && ($nomepontofinal != null)){
            $bd = Conexao::get();
            $query = $bd->prepare("UPDATE segmentos SET distancia = :pdistancia, ponto_inicial = :pponto_inicial, ponto_final = :pponto_final, status = :pstatus, direcao = :pdirecao WHERE id = :pid ");
            $query->bindParam(':pdistancia', $distancia);
            $query->bindParam(':pponto_inicial', $nomepontoinicial['nome']);
            $query->bindParam(':pponto_final', $nomepontofinal['nome']);
            $query->bindParam(':pstatus', $status);
            $query->bindParam(':pdirecao', $direcao);
            $query->bindParam(':pid', $id);
            $query->execute();
            return true;
        }else{
            $bd = Conexao::get();
            $query = $bd->prepare("UPDATE segmentos SET distancia = :pdistancia, ponto_inicial = :pponto_inicial, ponto_final = :pponto_final, status = :pstatus, direcao = :pdirecao WHERE id = :pid ");
            $query->bindParam(':pdistancia', $distancia);
            $query->bindParam(':pponto_inicial', $ponto_final);
            $query->bindParam(':pponto_final', $ponto_final);
            $query->bindParam(':pstatus', $status);
            $query->bindParam(':pdirecao', $direcao);
            $query->bindParam(':pid', $id);
            $query->execute();
            return true;
        }
    }
}

function deletesegmento($token, $id)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT * FROM segmentos WHERE id = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $result = $query->fetch() ?? null;
    if ($result == null) {
        return false;
    } else {
        $bd = Conexao::get();
        $query = $bd->prepare("DELETE FROM segmentos WHERE id = :pid");
        $query->bindParam(':pid', $id);
        $query->execute();
        return true;
    }
}

function cadastrarponto($nomeponto)
{
    $bd = Conexao::get();
    $query = $bd->prepare("INSERT INTO pontos(nome) VALUES (:pnome)");
    $query->bindParam(':pnome', $nomeponto);
    $query->execute();
}

function listaPontos()
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id AS ponto_id, nome FROM pontos");
    $query->execute();
    $pontos = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $pontos[] = $row;
    }
    return $pontos;
}

function listaPontosid($id)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id AS ponto_id, nome FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $pontos = $query->fetch(PDO::FETCH_ASSOC) ?? null;
    return $pontos;
}

function attponto($token, $id, $nome)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT id FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $result = $query->fetch() ?? null;
    if ($result == null) {
        return false;
    } else {
        $bd = Conexao::get();
        $query = $bd->prepare("UPDATE pontos SET nome = :pnome WHERE id = :pid ");
        $query->bindParam(':pnome', $nome);
        $query->bindParam(':pid', $id);
        $query->execute();
        return true;
    }
}

function deleteponto($token, $id)
{
    $bd = Conexao::get();
    $query = $bd->prepare("SELECT * FROM pontos WHERE id = :pid");
    $query->bindParam(':pid', $id);
    $query->execute();
    $result = $query->fetch() ?? null;
    if ($result == null) {
        return false;
    } else {
        $bd = Conexao::get();
        $query = $bd->prepare("DELETE FROM pontos WHERE id = :pid");
        $query->bindParam(':pid', $id);
        $query->execute();
        return true;
    }
}

function lerrotas($origem, $destino){
    $bd = Conexao::get();
    /*$query = $bd->prepare("SELECT id AS segmento_id, status, direcao, distancia, ponto_inicial, ponto_final FROM segmentos");
    $query->execute();
    $segmentos = array();
    $conexoes = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $segmentos[] = $row;
    }*/
    $query = $bd->prepare("SELECT id AS segmento_id, status, direcao, distancia, ponto_inicial, ponto_final FROM segmentos");
    $query->execute();
    $segmentos = array();
    $result = array();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $segmentos[] = $row;
    }

    return $segmentos;
}


?>