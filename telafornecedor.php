<?php

session_start();

$host = 'localhost';
$dbname = 'projeto_integrador';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $numero = $_POST['numero'];
    $email = $_POST['email'];

    $sql = "INSERT INTO fornecedores (nome, telefone, email) VALUES (:nome, :telefone, :email)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $numero);
    $stmt->bindParam(':email', $email);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = '<div class="success">Fornecedor cadastrado com sucesso!</div>';
    } else {
        $_SESSION['mensagem'] = '<div class="error">Erro ao cadastrar fornecedor.</div>';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Droga Lar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
        }

        h1 {
            text-align: center;
            color: #00A0FA;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #333333;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input:focus {
            border-color: #00A0FA;
            outline: none;
        }

        button {
            background-color: #00A0FA;
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        button:hover {
            background-color: #007acc;
        }

        .button-container {
            margin-left: 80px;
            justify-content: space-between;
        }

        .success {
            background-color: #28a745;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cadastro de Fornecedor</h1>
        
        <?php
        if (isset($_SESSION['mensagem'])) {
            echo $_SESSION['mensagem'];
            unset($_SESSION['mensagem']);
        }
        ?>

        <form action="" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="numero">Telefone:</label>
            <input type="text" id="numero" name="numero" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <div class="button-container">
                <button type="button" onclick="location.href='cadastros.html'">Voltar</button>
                <button type="submit">Enviar</button>
            </div>
        </form>
    </div>

    <script>

        window.onload = function() {
            const successMessage = document.querySelector('.success');
            const errorMessage = document.querySelector('.error');
            
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 2000);
            }
            
            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 2000);
            }
        }
    </script>
</body>
</html>







