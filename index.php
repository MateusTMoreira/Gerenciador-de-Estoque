<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Droga Lar</title>
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
            margin-bottom: 200px; 
        }

        h5 {
            margin-top: 0; 
            margin-bottom: 25px; 
        }

        a {
            background-color: white; 
            color: #00A0FA; 
            padding: 10px 20px; 
            font-size: 16px; 
            cursor: pointer; 
            border-radius: 5px; 
            text-decoration: none;
            transition: background-color 0.3s;
            margin: 5px; /* Adicionado para espaçamento entre os botões */
        }

        a:hover {
            background-color: #e0e0e0; 
        }

        .options {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>
    <div>
        <h1>SISTEMA DE INFORMAÇÃO - FARMACIA DROGA LAR</h1>
        
        <div class="options">
            <h5>CLIQUE ABAIXO PARA PROSSEGUIR</h5>
            <a href="login.php">FAÇA LOGIN</a>
        </div><br>

        <div>
            <h5>CASO NÃO TENHA CONTA CLIQUE ABAIXO</h5>
            <a href="criarconta.php">CRIAR CONTA</a>
        </div>
    </div>
</body>
</html>