<?php
// Conexão com o banco de dados
$servername = "localhost"; // Alterar se necessário
$username = "root"; // Usuário do banco de dados
$password = ""; // Senha do banco de dados
$dbname = "projeto_integrador"; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['nome']); // Pegando o nome e armazenando na variável $usuario
    $email = trim($_POST['email']);
    $senha = password_hash(trim($_POST['senha']), PASSWORD_DEFAULT); // Hash da senha

    // Verifica se o e-mail já está cadastrado
    $check_email = "SELECT id FROM farmaceutico WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensagem = "<p class='mensagem erro'>Erro: Este e-mail já está cadastrado.</p>";
    } else {
        $sql = "INSERT INTO farmaceutico (usuario, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario, $email, $senha);
        
        if ($stmt->execute()) {
            header("Location: criarconta.php?success=1");
            exit();
        } else {
            $mensagem = "<p class='mensagem erro'>Erro ao cadastrar: " . $stmt->error . "</p>";
        }
    }
    
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Droga Lar</title>
    <style>
        body {
            background-color: #00A0FA; 
            color: white; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            font-family: Arial, sans-serif; 
            text-align: center; 
        }
        h1 {
            margin-bottom: 50px; 
        }
        form {
            background-color: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
            width: 300px; 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
        }
        input {
            padding: 10px; 
            font-size: 16px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            outline: none; 
        }
        input:focus {
            border-color: #00A0FA; 
        }
        button {
            background-color: #00A0FA; 
            color: white; 
            padding: 10px; 
            font-size: 16px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0088cc; 
        }
        a {
            color: white; 
            text-decoration: none; 
            margin-top: 20px; 
            font-size: 14px; 
        }
        a:hover {
            text-decoration: underline; 
        }
        .mensagem {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }
        .sucesso {
            color:rgb(8, 208, 44);
        }
        .erro {
            color: #FF6347;
        }
    </style>
    <script>
        setTimeout(function() {
            var msg = document.getElementById('mensagem');
            if (msg) {
                msg.style.display = 'none';
            }
        }, 1500);
    </script>
</head>
<body>
    <div>
        <h1>CRIAR CONTA</h1>
        <form method="POST" action="criarconta.php">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">CRIAR CONTA</button>
        </form>
        <div id="mensagem">
            <?php 
            if (isset($_GET['success'])) {
                echo "<p class='mensagem sucesso'>A conta foi criada com sucesso!</p>";
            } else {
                echo $mensagem;
            }
            ?>

        <a href="index.php">Voltar para o login</a>
        </div>
        <br>
        
    </div>
</body>
</html>
