<?php

// Conexão com o banco de dados (substitua os valores conforme necessário)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recomendacoes";

// Crie uma conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define o cabeçalho para permitir requisições de qualquer origem
header("Access-Control-Allow-Origin: *");
// Define o cabeçalho para permitir os métodos GET, POST, PUT e DELETE
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// Define o cabeçalho para permitir os cabeçalhos Content-Type e Authorization
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Rota para manipulação de recomendações
if ($_SERVER["REQUEST_URI"] == "/recomendacoes") {
    switch ($method) {
        case 'GET':
            // Método GET para buscar todas as recomendações
            $sql = "SELECT * FROM recomendacoes";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $recomendacoes = array();
                while($row = $result->fetch_assoc()) {
                    $recomendacoes[] = $row;
                }
                echo json_encode($recomendacoes);
            } else {
                http_response_code(404); // Código de resposta 404 Not Found
                echo json_encode(array("message" => "Nenhuma recomendação encontrada"));
            }
            break;
        case 'POST':
            // Método POST para criar uma nova recomendação
            $data = json_decode(file_get_contents("php://input"), true);

            // Verifique se os dados foram recebidos corretamente e insira no banco de dados
            // Implemente a lógica para inserir os dados da recomendação no banco de dados
            // usando os dados recebidos do corpo da requisição
            // ...

            break;
        case 'PUT':
            // Método PUT para atualizar uma recomendação existente
            // Implemente a lógica para atualizar a recomendação no banco de dados
            // usando os dados recebidos do corpo da requisição e o ID recebido como parâmetro na URL
            // ...

            break;
        case 'DELETE':
            // Método DELETE para excluir uma recomendação existente
            // Implemente a lógica para excluir a recomendação do banco de dados
            // com base no ID recebido como parâmetro na URL
            // ...

            break;
        default:
            // Método não suportado
            http_response_code(405); // Código de resposta 405 Method Not Allowed
            echo json_encode(array("message" => "Método não permitido"));
            break;
    }
} else {
    // Rota desconhecida
    http_response_code(404); // Código de resposta 404 Not Found
    echo json_encode(array("message" => "Rota não encontrada"));
}

// Fecha a conexão com o banco de dados
$conn->close();

?>
