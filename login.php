<?php
session_start(); // Inicia a sessão para usar variáveis de sessão

$erro_login = "";  // Variável para armazenar a mensagem de erro

if (isset($_POST['submit'])) {
    include_once('conexao.php');

    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consulta o banco de dados para verificar o usuário, email e senha
    $query = "SELECT * FROM farmaceutico WHERE usuario = '$usuario' AND email = '$email' AND senha = '$senha'";
    $result = mysqli_query($conexao, $query);

    // Verifica se a consulta encontrou um usuário correspondente
    if (mysqli_num_rows($result) > 0) {
        // Usuário autenticado com sucesso, redireciona para a próxima página
        header("Location: tela2escolha.php");
        exit();
    } else {
        // Caso o login falhe, define a mensagem de erro na variável de sessão
        $_SESSION['erro_login'] = "Informações incorretas!"; // Mensagem para login incorreto
    }

    // Redireciona após o POST para evitar reenviar o formulário ao atualizar a página
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Verifica se existe uma mensagem de erro na sessão
if (isset($_SESSION['erro_login'])) {
    $erro_login = $_SESSION['erro_login'];
    unset($_SESSION['erro_login']); // Remove a mensagem de erro após exibição
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
            color: #00A0FA;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        h5, h1 {
            margin: 0 0 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            color: black;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 93%;
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
        }
    </style>
    <script>
        // Função para esconder a mensagem de erro após 1,5 segundos
        function esconderErro() {
            setTimeout(function() {
                var errorMessage = document.getElementById("erro-login");
                if (errorMessage) {
                    errorMessage.style.display = "none";
                }
            }, 1500); // 1500 ms = 1,5 segundos
        }

        // Chama a função esconderErro apenas se a mensagem de erro estiver presente
        window.onload = function() {
            if (document.getElementById("erro-login")) {
                esconderErro();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h5>FARMÁCIA - DROGA LAR</h5>
        <h1>FAÇA LOGIN</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="usuario">USUÁRIO</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="email">EMAIL</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">SENHA</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" name="submit">ENTRAR</button>
        </form>

        <?php if ($erro_login != ""): ?>
            <p id="erro-login" class="error-message"><?php echo $erro_login; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>







