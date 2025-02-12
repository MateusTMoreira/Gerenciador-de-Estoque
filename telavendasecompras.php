<?php
// Configuração do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname = "projeto_integrador";

// Criar a conexão
$conn = new mysqli($servidor, $usuario, $senha, $dbname);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Data limite: 6 meses atrás
$data_limite = date('Y-m-d', strtotime('-6 months'));

// Consultar compras dos últimos 6 meses
$sql_compras = "SELECT data, fornecedor, produto, quantidade, valor FROM compras WHERE data >= ?";
$stmt_compras = $conn->prepare($sql_compras);
$stmt_compras->bind_param("s", $data_limite);
$stmt_compras->execute();
$result_compras = $stmt_compras->get_result();

// Consultar vendas dos últimos 6 meses
$sql_vendas = "SELECT data, cliente, produto, quantidade, valor FROM vendas WHERE data >= ?";
$stmt_vendas = $conn->prepare($sql_vendas);
$stmt_vendas->bind_param("s", $data_limite);
$stmt_vendas->execute();
$result_vendas = $stmt_vendas->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras e Vendas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 600px;
        }

        h1 {
            color: #00A0FA;
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h2 {
            color: #333333;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #dddddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #00A0FA;
            color: #ffffff;
        }

        td {
            background-color: #f9f9f9;
        }

        .back-button {
            background-color: #00A0FA;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            display: flex;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            width: 7%;
        }

        .back-button:hover {
            background-color: #007acc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Compras e Vendas</h1>

        <!-- Compras -->
        <div class="section">
            <h2>Compras (Últimos 6 Meses)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Fornecedor</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_compras->fetch_assoc()) : ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['data'])); ?></td>
                            <td><?= htmlspecialchars($row['fornecedor']); ?></td>
                            <td><?= htmlspecialchars($row['produto']); ?></td>
                            <td><?= $row['quantidade']; ?></td>
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Vendas -->
        <div class="section">
            <h2>Vendas (Últimos 6 Meses)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_vendas->fetch_assoc()) : ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['data'])); ?></td>
                            <td><?= htmlspecialchars($row['cliente']); ?></td>
                            <td><?= htmlspecialchars($row['produto']); ?></td>
                            <td><?= $row['quantidade']; ?></td>
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="relatorios.html" class="back-button">Voltar</a>
    </div>
</body>
</html>

<?php
// Fechar conexões
$stmt_compras->close();
$stmt_vendas->close();
$conn->close();
?>


