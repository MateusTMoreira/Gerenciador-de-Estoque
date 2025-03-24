<?php
session_start(); // Inicia a sessão

$erro_login = ""; // Variável para armazenar mensagem de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('conexao.php'); // Inclui o arquivo de conexão

    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $senha_digitada = trim($_POST['senha']);

    // Consulta segura para buscar os dados do usuário
    $query = "SELECT id, usuario, senha FROM farmaceutico WHERE usuario = ? AND email = ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("ss", $usuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se encontrou um usuário correspondente
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $senha_hash = $row['senha'];

        // Verifica a senha digitada com o hash do banco
        if (password_verify($senha_digitada, $senha_hash)) {
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['id'] = $row['id'];
            header("Location: tela2escolha.php");
            exit();
        } else {
            $_SESSION['erro_login'] = "Senha incorreta!";
        }
    } else {
        $_SESSION['erro_login'] = "Usuário ou e-mail não encontrados!";
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Verifica se existe uma mensagem de erro na sessão
if (isset($_SESSION['erro_login'])) {
    $erro_login = $_SESSION['erro_login'];
    unset($_SESSION['erro_login']); // Remove a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Droga Lar</title>
    <style>
   body {
    background-color: #00A0FA;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
}

.container {
    background-color: white;
    color: black;
    border-radius: 8px;
    padding-top: 20px;
    padding-bottom: 20px;
    width: 320px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    display: flex;
    flex-direction: column;
}

.form-group input {
    width: 100%;  /* Deixe a largura ocupar toda a div, mas sem ultrapassar */
    max-width: 280px; /* Define um limite para evitar que os inputs fiquem muito grandes */
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box; /* Evita que padding aumente o tamanho total */
    margin-left: 20px;
}

label {
    margin-left: 20px;
}

button {
    width: 100%; /* Faz o botão ocupar o espaço correto */
    max-width: 280px; /* Define um limite para não ficar muito grande */
}

.voltar-login {
    margin-top: 20px;
    color: #00A0FA;
    transition: color 0.3s ease-in-out, transform 0.2s ease-in-out;
}

a:hover {
    color: #008BCC; /* Nova cor ao passar o mouse */
}

        h5, h1 {
            margin: 0 0 20px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #00A0FA;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        button:hover {
            background-color: #008BCC;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }
        .fade-out {
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h5>FARMÁCIA - DROGA LAR</h5>
        <h1>FAÇA LOGIN</h1>
        <form method="POST">
            <div class="form-group">
                <label for="usuario">NOME COMPLETO</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Nome Completo">
            </div>
            <div class="form-group">
                <label for="email">EMAIL</label>
                <input type="email" id="email" name="email" required placeholder="E-mail">
            </div>
            <div class="form-group">
                <label for="senha">SENHA</label>
                <input type="password" id="senha" name="senha" required placeholder="Senha">
            </div>
            <button type="submit">ENTRAR</button>
        </form>

        <?php if ($erro_login != ""): ?>
            <p class="error-message"><?php echo $erro_login; ?></p>
        <?php endif; ?>

        <a class="voltar-login" href="index.php">Voltar para criar sua conta</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const errorMessages = document.querySelectorAll('.error-message');
    
        errorMessages.forEach(errorMessage => {
        setTimeout(() => {
            errorMessage.classList.add('fade-out');
            
            setTimeout(() => {
                errorMessage.remove();
            }, 500);
        }, 1000);
            });
                });
    </script>
</body>
</html>
