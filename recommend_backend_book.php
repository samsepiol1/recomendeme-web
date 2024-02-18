<?php

// Função para obter a URL da capa do livro da Open Library API
function getUrl($title) {
    // Formata o título para a pesquisa na API
    $query = urlencode($title);

    // URL da pesquisa no Google
    $google_search_url = "https://www.google.com/search?q=$query";

    // Retorna a URL da pesquisa no Google
    return $google_search_url;
}


function getBookCoverUrl($book_title) {
    // Formata o título para a pesquisa na API
    $query = urlencode($book_title);
    
    // URL da API do Google Books
    $api_url = "https://www.googleapis.com/books/v1/volumes?q=$query";

    // Obtém os dados da API
    $response_json = file_get_contents($api_url);
    $response_data = json_decode($response_json, true);

    // Verifica se a resposta foi bem-sucedida e se existem itens nos resultados
    if ($response_data && isset($response_data['items']) && count($response_data['items']) > 0) {
        // Obtém a primeira capa de livro encontrada
        $book_cover_url = $response_data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
        return $book_cover_url;
    } else {
        return null; // Caso não seja encontrada nenhuma imagem de capa
    }
}

// Conexão com o banco de dados (substitua os valores conforme necessário)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recomendacoes_livros";

// Crie uma conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $titulo = $_POST['titulo'];
    $usuario = $_POST['usuario'];
    $descricao = $_POST['descricao'];
    
    // Obtém a URL da pesquisa no Google
    $query_url = getUrl($titulo);

    $query_img = getBookCoverUrl($titulo);

    // Consulta SQL para inserir os dados no banco de dados
    $sql = "INSERT INTO recomendacoes_livros (usuario, titulo, descricao, img, reclink) VALUES ('$usuario', '$titulo', '$descricao', '$query_img', '$query_url')";

    if ($conn->query($sql) === TRUE) {
        echo "Registro inserido com sucesso!";
    } else {
        echo "Erro ao inserir registro: " . $conn->error;
    }
}

$conn->close();


header("Location: recomendacoes_livros.php");
exit;

?>
