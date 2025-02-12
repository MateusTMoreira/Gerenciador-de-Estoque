<?php
session_start();
$message = "";

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "projeto_integrador");

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Processa atualização de quantidade e preço
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $registro = intval($_POST['registro']);
    $quantidade = intval($_POST['quantidade']);
    $preco = str_replace(',', '.', $_POST['preco']); // Converte vírgula para ponto
    $preco = floatval($preco);

    $sql_update = "UPDATE produto SET quantidade = ?, preco = ? WHERE registro = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("idi", $quantidade, $preco, $registro);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Produto atualizado com sucesso!";
    } else {
        $_SESSION['message'] = "Erro ao atualizar o produto.";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Processa venda de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sell_product'])) {
    $registro = intval($_POST['registro']);
    $quantidade_vendida = intval($_POST['quantidade_vendida']);
    $cliente_id = intval($_POST['cliente_id']);
    $data_venda = $_POST['data_venda'];

    // Consulta o nome do produto e verifica a quantidade em estoque
    $sql_check_stock = "SELECT nome, quantidade, preco FROM produto WHERE registro = ?";
    $stmt_check = $conn->prepare($sql_check_stock);
    $stmt_check->bind_param("i", $registro);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stock_row = $result_check->fetch_assoc();

    if ($stock_row['quantidade'] >= $quantidade_vendida) {
        // Consulta o nome do cliente
        $sql_get_cliente = "SELECT nome FROM clientes WHERE id = ?";
        $stmt_cliente = $conn->prepare($sql_get_cliente);
        $stmt_cliente->bind_param("i", $cliente_id);
        $stmt_cliente->execute();
        $result_cliente = $stmt_cliente->get_result();
        $cliente_row = $result_cliente->fetch_assoc();

        // Inicia uma transação para garantir a integridade dos dados
        $conn->begin_transaction();

        try {
            // Reduz a quantidade do produto no estoque
            $sql_sell = "UPDATE produto SET quantidade = quantidade - ? WHERE registro = ?";
            $stmt_sell = $conn->prepare($sql_sell);
            $stmt_sell->bind_param("ii", $quantidade_vendida, $registro);
            $stmt_sell->execute();

            // Calcula o valor total da venda
            $valor_total = $stock_row['preco'] * $quantidade_vendida;

            // Registra a venda com nome do cliente e produto
            $sql_registro_venda = "INSERT INTO vendas (produto, cliente, quantidade, data, valor) VALUES (?, ?, ?, ?, ?)";
            $stmt_venda = $conn->prepare($sql_registro_venda);
            $stmt_venda->bind_param(
                "ssisd", 
                $stock_row['nome'],       // Nome do produto
                $cliente_row['nome'],     // Nome do cliente
                $quantidade_vendida, 
                $data_venda, 
                $valor_total
            );
            $stmt_venda->execute();

            // Confirma a transação
            $conn->commit();

            $_SESSION['message'] = "Venda de {$quantidade_vendida} unidades do produto " . 
                                   htmlspecialchars($stock_row['nome']) . 
                                   " para o cliente " . 
                                   htmlspecialchars($cliente_row['nome']) . 
                                   " registrada com sucesso!";
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $conn->rollback();
            $_SESSION['message'] = "Erro ao registrar a venda: " . $e->getMessage();
        }
    } else {
        $_SESSION['message'] = "Estoque insuficiente para o produto " . htmlspecialchars($stock_row['nome']) . ". Venda não realizada.";
    }
    $stmt_check->close();
    $stmt_cliente->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// Consulta para buscar clientes
$sql_clientes = "SELECT id, nome FROM clientes";
$result_clientes = $conn->query($sql_clientes);

// Processa exclusão de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro']) && isset($_POST['confirm_delete'])) {
    $registro = intval($_POST['registro']);

    $sql_delete = "DELETE FROM produto WHERE registro = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $registro);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Produto excluído do estoque com sucesso!";
    } else {
        $_SESSION['message'] = "Erro ao excluir o produto.";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Verifica se existe mensagem na sessão
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Consulta SQL
$sql = "SELECT nome, registro, preco, quantidade, dosagem, fabricante FROM produto";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque</title>
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
            width: 1000px;
            overflow: auto;
        }

        h1 {
            color: #00A0FA;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #dddddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
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
            width: 80px; 
            text-align: center;
            display: inline-block;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #007acc;
        }

        .delete-button, .edit-button, .sell-button {
            color: #ffffff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin: 0 2px;
        }

        .delete-button {
            background-color: #FF4C4C;
        }

        .edit-button {
            background-color: #4CAF50;
        }

        .sell-button {
            background-color: #FFA500;
        }

        .delete-button:hover {
            background-color: #cc0000;
        }

        .edit-button:hover {
            background-color: #45a049;
        }

        .sell-button:hover {
            background-color: #FF8C00;
        }

        .message-success {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            font-size: 14px;
            margin-left: 20px;
            display: inline-block;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .modal-close {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 20px;
            cursor: pointer;
            color: #aaa;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-form input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-form button {
            background-color: #00A0FA;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-form button:hover {
            background-color: #007acc;
        }
        
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Estoque da Farmácia</h1>

        <table>
            <thead>
                <tr>
                    <th>Nome do Produto</th>
                    <th>Código</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Dosagem</th>
                    <th>Fabricante</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['registro']) . "</td>";
                        echo "<td>R$ " . htmlspecialchars(number_format($row['preco'], 2, ',', '.')) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['dosagem']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fabricante']) . "</td>";
                        echo "<td>
                                <button onclick='openEditModal(" . json_encode($row) . ")' class='edit-button'>Editar</button>
                                <button onclick='openSellModal(" . json_encode($row) . ")' class='sell-button'>Vender</button>
                                <button onclick='openDeleteModal(" . json_encode($row) . ")' class='delete-button'>Excluir</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Nenhum produto encontrado.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <div>
            <button onclick="window.location.href='relatorios.html'" class="back-button">Voltar</button>
            <?php
            if (!empty($message)) {
                echo "<span id='message' class='message-success'>$message</span>";
            }
            ?>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
            <h2>Editar Produto</h2>
            <form class="modal-form" method="POST" action="">
                <input type="hidden" id="editRegistro" name="registro">
                <input type="hidden" name="update_quantity" value="1">
                
                <div class="form-group">
                    <label for="editQuantidade">Quantidade:</label>
                    <input type="number" id="editQuantidade" name="quantidade" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="editPreco">Preço (R$):</label>
                    <input type="text" id="editPreco" name="preco" pattern="^\d*,?\d*$" required>
                </div>
                
                <button type="submit">Salvar</button>
            </form>
        </div>
    </div>

   <!-- Modal de Venda -->
<div id="sellModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeSellModal()">&times;</span>
        <h2>Vender Produto</h2>
        <form class="modal-form" method="POST" action="">
            <input type="hidden" id="sellRegistro" name="registro">
            <input type="hidden" name="sell_product" value="1">
            
            <div class="form-group">
                <label for="sellNome">Produto:</label>
                <input type="text" id="sellNome" readonly>
            </div>
            
            <div class="form-group">
                <label for="sellEstoque">Estoque Atual:</label>
                <input type="number" id="sellEstoque" readonly>
            </div>
            
            <div class="form-group">
                <label for="sellCliente">Cliente:</label>
                <select id="sellCliente" name="cliente_id" required>
                    <option value="">Selecione um cliente</option>
                    <?php
                    if ($result_clientes->num_rows > 0) {
                        while ($cliente = $result_clientes->fetch_assoc()) {
                            echo "<option value='" . $cliente['id'] . "'>" . htmlspecialchars($cliente['nome']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="sellData">Data da Venda:</label>
                <input type="date" id="sellData" name="data_venda" required>
            </div>
            
            <div class="form-group">
                <label for="sellQuantidade">Quantidade a Vender:</label>
                <input type="number" id="sellQuantidade" name="quantidade_vendida" min="1" required>
            </div>
            
            <button type="submit">Registrar Venda</button>
        </form>
    </div>
</div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
            <h2>Confirmar Exclusão</h2>
            <form class="modal-form" method="POST" action="">
                <input type="hidden" id="deleteRegistro" name="registro">
                <input type="hidden" name="confirm_delete" value="1">
                
                <div class="form-group">
                    <label for="deleteNome">Tem certeza que deseja excluir o produto:</label>
                    <input type="text" id="deleteNome" readonly>
                </div>
                
                <button type="submit" class="delete-button">Sim, excluir</button>
                <button type="button" onclick="closeDeleteModal()" class="back-button">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        // Funções para o modal de edição
        function openEditModal(row) {
            const modal = document.getElementById('editModal');
            const registroInput = document.getElementById('editRegistro');
            const quantidadeInput = document.getElementById('editQuantidade');
            const precoInput = document.getElementById('editPreco');
            
            registroInput.value = row.registro;
            quantidadeInput.value = row.quantidade;
            // Formata o preço para exibição com vírgula
            precoInput.value = row.preco.toString().replace('.', ',');
            
            modal.style.display = 'block';
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.style.display = 'none';
        }

        // Funções para o modal de venda
        function openSellModal(row) {
    const modal = document.getElementById('sellModal');
    const registroInput = document.getElementById('sellRegistro');
    const nomeInput = document.getElementById('sellNome');
    const estoqueInput = document.getElementById('sellEstoque');
    const quantidadeInput = document.getElementById('sellQuantidade');
    const dataInput = document.getElementById('sellData');
    
    registroInput.value = row.registro;
    nomeInput.value = row.nome;
    estoqueInput.value = row.quantidade;
    quantidadeInput.max = row.quantidade;
    quantidadeInput.value = 1;
    
    // Define a data atual como padrão
    const hoje = new Date().toISOString().split('T')[0];
    dataInput.value = hoje;
    
    modal.style.display = 'block';
}

        function closeSellModal() {
            const modal = document.getElementById('sellModal');
            modal.style.display = 'none';
        }

        // Funções para o modal de exclusão
        function openDeleteModal(row) {
            const modal = document.getElementById('deleteModal');
            const registroInput = document.getElementById('deleteRegistro');
            const nomeInput = document.getElementById('deleteNome');
            
            registroInput.value = row.registro;
            nomeInput.value = row.nome;
            
            modal.style.display = 'block';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
        }

        // Fecha os modais ao clicar fora
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const sellModal = document.getElementById('sellModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target == editModal) {
                closeEditModal();
            }
            
            if (event.target == sellModal) {
                closeSellModal();
            }

            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }

        // Remove a mensagem após 2 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.getElementById('message');
            if (messageElement) {
                setTimeout(() => {
                    messageElement.style.display = 'none';
                }, 2000);
            }
        });

        // Formata o campo de preço para aceitar apenas números e vírgula
        document.getElementById('editPreco').addEventListener('input', function(e) {
            let value = e.target.value;
            // Remove tudo que não for número ou vírgula
            value = value.replace(/[^\d,]/g, '');
            // Garante que só tenha uma vírgula
            const parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts.slice(1).join('');
            }
            // Limita a 2 casas decimais
            if (parts.length === 2 && parts[1].length > 2) {
                value = parts[0] + ',' + parts[1].slice(0, 2);
            }
            e.target.value = value;
        });

        // Validação de quantidade de venda
        document.getElementById('sellQuantidade').addEventListener('input', function(e) {
    const estoqueAtual = parseInt(document.getElementById('sellEstoque').value);
    let quantidadeVenda = parseInt(e.target.value);
    
    if (quantidadeVenda > estoqueAtual) {
        e.target.value = estoqueAtual;
        alert(`Quantidade máxima disponível: ${estoqueAtual}`);
    }
});
    </script>
</body>
</html>