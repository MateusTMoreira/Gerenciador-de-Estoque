<?php
// Configurações do banco de dados
$host = 'localhost';         // Endereço do servidor
$usuario = 'root';           // Nome de usuário do banco de dados
$senha = '';                 // Senha do banco de dados
$banco = 'projeto_integrador';    // Nome do banco de dados

// Criação da conexão
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verificação da conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Definir charset para UTF-8
$conexao->set_charset("utf8");




