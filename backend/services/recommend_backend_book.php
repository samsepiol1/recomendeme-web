<?php




// Arquivo de configuração para conexão com banco de dados
require_once(__DIR__ . '/../db/config.php');




// Função para obter a URL da capa do livro da API do Google Books
function getBookCoverUrl($book_title) {
    $query = urlencode($book_title);
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=$query";

    // Inicia a sessão cURL
    $ch = curl_init();
    // Define a URL e outras opções apropriadas
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Executa a solicitação
    $response_json = curl_exec($ch);
    // Verifica se houve algum erro na requisição
    if(curl_errno($ch)){
        // Se houver um erro, trata-o ou registra-o conforme necessário
        error_log("Erro na requisição da API do Google Books: " . curl_error($ch));
        return null;
    }
    // Fecha a sessão cURL
    curl_close($ch);

    // Decodifica a resposta JSON
    $response_data = json_decode($response_json, true);

    // Verifica se a resposta é válida e se há itens nos resultados
    if ($response_data && isset($response_data['items']) && count($response_data['items']) > 0) {
        // Obtém a URL da primeira capa de livro encontrada
        $book_cover_url = $response_data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
        return $book_cover_url;
    } else {
        return null;
    }
}






// Cria uma nova conexão
$conn = new mysqli($servername, $username, $password, $dbname);



// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se os dados do formulário foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpa e valida os dados de entrada do usuário
    $usuario = isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : '';
    $titulo = isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '';
    $descricao = isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : '';

    // Obtém a URL da pesquisa no Google
    $google_search_url = "https://www.google.com/search?q=" . urlencode($titulo);

    // Obtém a URL da capa do livro
    $book_cover_url = getBookCoverUrl($titulo);

    // Prepara e executa a instrução SQL para inserir os dados no banco de dados de forma segura
    $stmt = $conn->prepare("INSERT INTO recomendacoes_livros (usuario, titulo, descricao, img, reclink) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $usuario, $titulo, $descricao, $book_cover_url, $google_search_url);

    if ($stmt->execute()) {
        echo "Registro inserido com sucesso!";
    } else {
        echo "Erro ao inserir registro: " . $conn->error;
    }

    // Fecha a instrução SQL
    $stmt->close();
}

// Fecha a conexão com o banco de dados
$conn->close();

// Redireciona para a página de recomendações de livros
header("Location: ../views/recomendacoes_livros.php");
exit;

?>
