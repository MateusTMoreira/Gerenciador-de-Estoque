<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleção de Áreas</title>
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
            flex-direction: column;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            
        }

        h1 {
            color: #00A0FA;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        button {
            background-color: #00A0FA;
            color: #ffffff;
            border: none;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #007acc;
        }

        a {
            text-decoration: none;
        }

        #back {
            color: #00A0FA;
           display: block;
           text-align: center;
           margin-top: 20px;
           transition: color 0.3s ease;
        }
        #back:hover {
            color: #007acc; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Escolha uma Área</h1>
        <div class="button-container">
            <a href="cadastros.html">
                <button>Cadastros</button>
            </a>
            <a href="relatorios.html">
                <button>Relatórios</button>
            </a>
        </div>
    </div>
    <a href="login.php" id="back">Voltar para login</a>
</body>
</html>

