<?php
// Iniciar a sessão para armazenar mensagens
session_start();

// Conectar ao banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname = "projeto_integrador"; // Nome do banco de dados

// Criar a conexão
$conn = new mysqli($servidor, $usuario, $senha, $dbname);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $nome = $_POST['produto']; // Nome do produto
    $preco = $_POST['preco'];
    $validade = $_POST['validade'];
    $quantidade = $_POST['quantidade'];
    $registro = $_POST['registro'];
    $dosagem = $_POST['dosagem'];
    $fornecedor = $_POST['fornecedor'];
    $nota_fiscal = $_POST['nota-fiscal'];
    $fabricante = $_POST['fabricante'];

    // Evitar SQL Injection com prepared statements
    $stmt_produto = $conn->prepare("INSERT INTO produto (nome, preco, validade, quantidade, registro, dosagem, fornecedor, nota_fiscal, fabricante) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_produto->bind_param("sdsssssss", $nome, $preco, $validade, $quantidade, $registro, $dosagem, $fornecedor, $nota_fiscal, $fabricante);

    // Executar a query de produto e verificar se foi bem-sucedido
    if ($stmt_produto->execute()) {
        // Calcular o valor total (quantidade * preço unitário)
        $valor_total = $quantidade * $preco;

        // Preparar a query para inserir na tabela compras
        $data_atual = date('Y-m-d'); // Data atual para registro
        $stmt_compras = $conn->prepare("INSERT INTO compras (data, fornecedor, produto, quantidade, valor) 
                                        VALUES (?, ?, ?, ?, ?)");
        $stmt_compras->bind_param("sssdi", $data_atual, $fornecedor, $nome, $quantidade, $valor_total);

        // Executar a query de compras e verificar se foi bem-sucedido
        if ($stmt_compras->execute()) {
            $_SESSION['message'] = "<div id='message' style='background-color: #28a745; color: white; padding: 10px; border-radius: 5px;'>Produto e compra cadastrados com sucesso!</div>";
        } else {
            $_SESSION['message'] = "<div id='message' style='background-color: #dc3545; color: white; padding: 10px; border-radius: 5px;'>Erro ao tentar cadastrar compra!</div>";
        }

        // Fechar a statement de compras
        $stmt_compras->close();
    } else {
        $_SESSION['message'] = "<div id='message' style='background-color: #dc3545; color: white; padding: 10px; border-radius: 5px;'>Erro ao tentar cadastrar produto!</div>";
    }

    // Fechar a statement de produto
    $stmt_produto->close();

    // Redirecionar para a mesma página após o cadastro
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - Farmacia Droga Lar</title>

    <style>
        body {
            background-color: #00A0FA;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            padding: 10px;
        }

        .header {
            background-color: white;
            color: #00A0FA;
            padding-top: 5px;
            padding-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 340px;
            text-align: center;
            margin-bottom: 30px;
            height: 60px;
        }

        .form-container {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        button {
            background-color: #00A0FA;
            color: white;
            border: none;
            padding: 15px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0088cc;
        }

        @media (min-width: 768px) {
            .button-container {
                flex-direction: row;
            }
        }

        @media (max-width: 600px) {
            body {
                font-size: 14px;
            }

            .header, .form-container {
                padding: 10px;
                box-shadow: none;
            }

            button {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Farmacia Droga Lar</h1>
    </div>

    <?php 
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
    ?>

    <div class="form-container">
        <form method="POST">
            <label for="produto">Produto</label>
            <input type="text" id="produto" name="produto" required>

            <label for="preco">Preço</label>
            <input type="number" step="0.01" id="preco" name="preco" required>

            <label for="validade">Validade</label>
            <input type="date" id="validade" name="validade" required>

            <label for="quantidade">Quantidade</label>
            <input type="number" id="quantidade" name="quantidade" required>

            <label for="registro">Número de Registro</label>
            <input type="text" id="registro" name="registro" required>

            <label for="dosagem">Dosagem</label>
            <input type="text" id="dosagem" name="dosagem" required>

            <label for="fornecedor">Fornecedor</label>
            <input type="text" id="fornecedor" name="fornecedor" required>

            <label for="nota-fiscal">Nota Fiscal</label>
            <input type="text" id="nota-fiscal" name="nota-fiscal" required>

            <label for="fabricante">Fabricante</label>
            <input type="text" id="fabricante" name="fabricante" required>

            <div class="button-container">
                <button type="button" class="back-button" onclick="location.href='cadastros.html'">Voltar</button>
                <button type="submit">Enviar</button>
            </div>
        </form>
    </div>

    <script>
        setTimeout(function() {
            var message = document.getElementById('message');
            if (message) {
                message.style.display = 'none';
            }
        }, 2000);
    </script>
</body>
</html>



