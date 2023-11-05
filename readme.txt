Descrição:
    O banco de dados do Xampp foi escolhido para ser utilizado, onde o Apache e o Mysql precisam ser ligados
utilizando as portas default.
    Para o servidor e o cliente serão utilizados portas individuais diferentes da porta do Apache do Xampp, utilizando
 a variavel de ambiente php no terminal.

    O trabalho foi feito e testado utilizando o Php no Windows. Recomendo utilizar o php presente no Xampp, pois já tem as configurações 
e drivers(extension) necessarios para utilização do banco de dados Mysql com o PDO. 

    Caso optar por utilizar php sem ser o presente no Xampp é necessario entrar no(s) arquivo(s) php.ini da pasta raiz do php e descomentar o ponto e virgula(;) 
antes das seguintes extensões:
    extension=mysqli
    extension=pdo_mysql
    extension=pdo_sqlite

    Para ambas as versões do PHP é preciso configurar a variavel de ambiente php para poder iniciar o servidor e o cliente.
    
    No php, encontrar o caminho do diretório do php Ex.(C:xampp\php).
        Na pesquisa do Windows, pesquisar "Editar as variáveis de ambiente do sistema",
        clicar em "Variáveis de Ambiente".
        - Variáveis do sistema, clica em Path, em seguida em Editar..
            -Novo, em seguida cola "C:xampp\php" sem aspas.
            -Caso utilize o php separado é só colocar o diretorio da pasta, ex: (C:\php-8.2.12).
    

Passos para executar:

    Entrar no endereço do banco de dados do Xampp: localhost/phpmyadmin, criar um banco de dados chamado servidormurillo e 
importar o script servidormurillo.sql presente na raiz do projeto.

    INICIAR SERVIDOR -  

    Para iniciar o servidor, entrar na pasta raiz do projeto no cmd ou terminal, e seguir os seguintes comandos:
        Acessar pasta servidor:
        - cd servidor
            clienteservidormurillo\servidor>
            
        php -S localhost:25000

    Em seguida abrir um novo terminal na raiz do projeto e seguir os comandos:
        Acessar pasta cliente:
        - cd cliente
            clienteservidormurillo\cliente>

        php -S localhost:24000

        O usuário para acesso administrador é:
            Registro: 
                21
            Senha: 
                @1234567

    Para acessar o cliente é localhost:24000, e o servidor pode ser acessado por requisicoes no localhost:25000.
    Para abrir o servidor para outras máquinas na rede acessarem, trocar o localhost para o seu endereço ipv4.

    

    
