
<?php
// Iniciar a sessão
session_start();

// Conectar ao banco de dados
$host = 'localhost';
$usuario = 'root';   // Usuário padrão do XAMPP
$senha = '';         // Senha em branco
$banco = 'projeto_integrador';

// Criar a conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Variáveis de controle para as mensagens
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['numero'];  // Alterado para 'telefone'
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];

    // Inserir dados na tabela 'clientes'
    $sql = "INSERT INTO clientes (nome, telefone, email, cpf) VALUES ('$nome', '$telefone', '$email', '$cpf')";

    if ($conn->query($sql) === TRUE) {
        // Mensagem de sucesso
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cliente cadastrado com sucesso!'];
    } else {
        // Mensagem de erro
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Erro ao cadastrar o cliente. Tente novamente!'];
    }

    // Fechar a conexão
    $conn->close();

    // Redirecionar para a mesma página com a mensagem
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Droga Lar</title>
   
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        width: 100%;
        max-width: 450px; 
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin: 20px; 
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header h1 {
        color: #00A0FA;
        font-size: 28px; 
    }

    .form-area {
        margin-top: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold; 
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box; 
    }

    .button-container {
        display: flex;
        justify-content: center; 
        gap: 20px; 
        margin-top: 20px;
    }

    .submit-button, .back-button {
        background-color: #00A0FA;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        width: 80px; 
    }

    .submit-button:hover, .back-button:hover {
        background-color: #007bb5;
    }

    .success-message {
        background-color: #28a745;
        color: white;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
        text-align: center;
    }

    .error-message {
        background-color: #dc3545;
        color: white;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
        text-align: center;
    }
</style>




</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Farmácia Droga Lar</h1>
        </div>
        <div class="form-area">
            <h2>Cadastro</h2>
            
            <!-- Mensagem de Sucesso -->
            <?php if (isset($_SESSION['message']) && $_SESSION['message']['type'] == 'success'): ?>
                <div class="success-message" id="successMessage">
                    <?= $_SESSION['message']['text']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <!-- Mensagem de Erro -->
            <?php if (isset($_SESSION['message']) && $_SESSION['message']['type'] == 'error'): ?>
                <div class="error-message" id="errorMessage">
                    <?= $_SESSION['message']['text']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form action="telacliente.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="numero" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" required>
                </div>
                <div class="button-container">
                    <button type="button" class="back-button" onclick="location.href='cadastros.html'">Voltar</button>
                    <button type="submit" class="submit-button">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Função para esconder as mensagens após 5 segundos
        function hideMessages() {
            setTimeout(function() {
                if (document.getElementById("successMessage")) {
                    document.getElementById("successMessage").style.display = "none";
                }
                if (document.getElementById("errorMessage")) {
                    document.getElementById("errorMessage").style.display = "none";
                }
            }, 2000); // Tempo de 5 segundos
        }

        // Verificar se a mensagem foi exibida e esconder após 5 segundos
        window.onload = function() {
            if (document.getElementById("successMessage") || document.getElementById("errorMessage")) {
                hideMessages();
            }
        }
    </script>
</body>
</html>







       
