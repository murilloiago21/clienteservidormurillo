<?php
    session_start();
    require("conexao.php");
    class Usuario {
        private $nome;
        private $cpf;
        private $hc;

        public function __construct($nome, $cpf, $senha, $op){
            $this->nome = $nome;
            $this->cpf = $cpf;
            if($op == 'cadastrar'){
                $this->hc = password_hash($senha, PASSWORD_DEFAULT);
            }else if ($op == 'logar'){
                $this->hc = $senha;
            }
        }
        
        public function cadastrar(){
            if(ctype_space($this->nome) && ctype_space($this->cpf) && ctype_space($this->hc)){
                //header('location: ../index.php?acao=cadastrar&erro=4');
                echo "<script>window.location.replace('https://projetocbi.com/index.php?acao=cadastrar&erro=4');</script>";
                //echo "<script>window.location.replace('../index.php?acao=cadastrar&erro=4');</script>";
            }else if((strlen($this->nome) >= 7) && (strlen($this->cpf) >= 7) && (strlen($this->hc) >= 7)){
                $bd = Conexao::get();
                $query = $bd->prepare("select * from usuario where cpf = :cpf");
                $query->bindParam(':cpf', $this->cpf);
                $query->execute();
                $usuario = $query->fetch() ?? null;
                if($usuario == null){
                    $query = $bd->prepare("INSERT INTO usuario(nome, cpf, hashc) VALUES(:nome, :cpf, :hashc)");
                    $query->bindParam(':nome', $this->nome);
                    $query->bindParam(':cpf', $this->cpf);
                    $query->bindParam(':hashc', $this->hc);
                    $query->execute();
                    $_SESSION['logado'] = 'true';
                    $_SESSION['usuario'] = $this->nome;
                    //header("location: ../index.php");
                    echo "<script>window.location.replace('https://projetocbi.com/index.php');</script>";
                    //echo "<script>window.location.replace('../index.php');</script>";
                }else if($usuario != null){
                    //header('location: ../index.php?acao=cadastrar&erro=1');
                    echo "<script>window.location.replace('https://projetocbi.com/index.php?acao=cadastrar&erro=1');</script>";
                    //echo "<script>window.location.replace('../index.php?acao=cadastrar&erro=1');</script>";
                }
            }else{
                //header('location: ../index.php?acao=cadastrar&erro=4');
                echo "<script>window.location.replace('https://projetocbi.com/index.php?acao=cadastrar&erro=4');</script>";
                //echo "<script>window.location.replace('../index.php?acao=cadastrar&erro=4');</script>";
            }
        }

        public function logar(){
            $bd = Conexao::get();
            $query = $bd->prepare("select * from usuario where cpf = :cpf");
            $query->bindParam(':cpf', $this->cpf);
            $query->execute();
            $usuario = $query->fetch() ?? null;
            if($usuario == null){
                //header('location: ../index.php?acao=logar&erro=2');
                echo "<script>window.location.replace('https://projetocbi.com/index.php?acao=logar&erro=2');</script>";
                //echo "<script>window.location.replace('../index.php?acao=logar&erro=2');</script>";
            }else if($usuario != null){
                if($usuario[2] == $this->cpf && password_verify($this->hc, $usuario[3])){ 
                    $_SESSION['logado'] = 'true';
                    $_SESSION['usuario'] = $usuario[1];
                    $_SESSION['cpf'] = $usuario[2];
                    echo "<script>window.location.replace('https://projetocbi.com/index.php');</script>";
                    //echo "<script>window.location.replace('../index.php');</script>";
                }else{
                    //header('location: ../index.php?acao=logar&erro=3');
                    echo "<script>window.location.replace('https://projetocbi.com/index.php?acao=logar&erro=3');</script>";
                    //echo "<script>window.location.replace('../index.php?acao=logar&erro=3');</script>";
                } 
            }

        }
        
        
    }
    

?>