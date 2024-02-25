<?php
// Credenciais do Spotify
$client_id = '22d4b6997d4b4d6faf63ab9d78278fd5';
$client_secret = '8074223b87354205b8c5946589d9aa73';

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



// Função para obter o token de acesso do Spotify
function getSpotifyAccessToken($client_id, $client_secret) {
    $auth_url = 'https://accounts.spotify.com/api/token';
    $auth_data = array(
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret
    );

    $auth_options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($auth_data)
        )
    );

    $auth_context = stream_context_create($auth_options);
    $auth_result = file_get_contents($auth_url, false, $auth_context);
    $auth_response = json_decode($auth_result, true);

    if (isset($auth_response['access_token'])) {
        return $auth_response['access_token'];
    } else {
        return false;
    }
}

// Função para buscar o link do álbum no Spotify
function getSpotifyAlbumLink($access_token, $artist_name, $album_name) {
    $spotify_url = "https://api.spotify.com/v1/search?q=" . urlencode("artist:$artist_name album:$album_name") . "&type=album&limit=1";
    $spotify_options = array(
        'http' => array(
            'method' => 'GET',
            'header' => "Authorization: Bearer $access_token"
        )
    );

    $spotify_context = stream_context_create($spotify_options);
    $spotify_result = file_get_contents($spotify_url, false, $spotify_context);
    $spotify_response = json_decode($spotify_result, true);

    if (isset($spotify_response['albums']['items'][0]['external_urls']['spotify'])) {
        return $spotify_response['albums']['items'][0]['external_urls']['spotify'];
    } else {
        return false;
    }
}


function getSpotifyAlbumImage($access_token, $artist_name, $album_name) {
    $spotify_url = "https://api.spotify.com/v1/search?q=" . urlencode("artist:$artist_name album:$album_name") . "&type=album&limit=1";
    $spotify_options = array(
        'http' => array(
            'method' => 'GET',
            'header' => "Authorization: Bearer $access_token"
        )
    );

    $spotify_context = stream_context_create($spotify_options);
    $spotify_result = file_get_contents($spotify_url, false, $spotify_context);
    $spotify_response = json_decode($spotify_result, true);

    if (isset($spotify_response['albums']['items'][0]['images'][0]['url'])) {
        return $spotify_response['albums']['items'][0]['images'][0]['url'];
    } else {
        return false;
    }
}

// Função para buscar o link do álbum no Deezer
function getDeezerAlbumLink($artist_name, $album_name) {
    $deezer_url = "https://api.deezer.com/search/album/?q=" . urlencode("$artist_name - $album_name") . "&limit=1";
    $deezer_response = file_get_contents($deezer_url);
    $deezer_data = json_decode($deezer_response, true);

    if (!empty($deezer_data['data'][0]['link'])) {
        return $deezer_data['data'][0]['link'];
    } else {
        return false;
    }
}




// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $titulo = $_POST['titulo'];
    $usuario = $_POST['usuario'];
    $descricao = $_POST['descricao'];


    // Separa o título em nome do artista e nome do álbum
    list($artist_name, $album_name) = explode(" - ", $titulo, 2);

    // Obtém o token de acesso do Spotify
    $spotify_access_token = getSpotifyAccessToken($client_id, $client_secret);
    if (!$spotify_access_token) {
        die("Erro ao obter token de acesso do Spotify.");
    }

    // Obtém o link do álbum no Spotify
    $spotify_album_link = getSpotifyAlbumLink($spotify_access_token, $artist_name, $album_name);
    if (!$spotify_album_link) {
        echo "Link do álbum não encontrado no Spotify.";
    } else {
        // Construa e execute a consulta SQL para inserir os dados no banco de dados
        $sql = "INSERT INTO recomendacoes (titulo, usuario, descricao) VALUES ('$titulo', '$usuario', '$descricao')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Dados inseridos com sucesso!";

            // Atualiza o registro no banco de dados com o link do álbum do Spotify
            $update_sql = "UPDATE recomendacoes SET reclink_spotify = '$spotify_album_link' WHERE titulo = '$titulo'";
            if ($conn->query($update_sql) === TRUE) {
                echo "Link do álbum do Spotify salvo com sucesso no banco de dados!";
            } else {
                echo "Erro ao salvar o link do álbum do Spotify no banco de dados: " . $conn->error;
            }
        } else {
            echo "Erro ao inserir dados: " . $conn->error;
        }
    }



    $spotify_album_image = getSpotifyAlbumImage($spotify_access_token, $artist_name, $album_name);

    if (!$spotify_album_image) {
        echo "Imagem do álbum não encontrada no Spotify.";
    } else {
        // Construa e execute a consulta SQL para atualizar a coluna 'img' na tabela 'recomendacoes'
        $update_sql = "UPDATE recomendacoes SET img = '$spotify_album_image' WHERE titulo = '$titulo'";
        
        if ($conn->query($update_sql) === TRUE) {
            echo "Imagem do álbum do Spotify salva com sucesso no banco de dados!";
        } else {
            echo "Erro ao salvar a imagem do álbum do Spotify no banco de dados: " . $conn->error;
        }
    }

    // Obtém o link do álbum no Deezer
    $deezer_album_link = getDeezerAlbumLink($artist_name, $album_name);
    if (!$deezer_album_link) {
        echo "Link do álbum não encontrado no Deezer.";
    } else {
        // Atualiza o registro no banco de dados com o link do álbum do Deezer
        $update_deezer_sql = "UPDATE recomendacoes SET reclink = '$deezer_album_link' WHERE titulo = '$titulo'";
        if ($conn->query($update_deezer_sql) === TRUE) {
            echo "Link do álbum do Deezer salvo com sucesso no banco de dados!";
        } else {
            echo "Erro ao salvar o link do álbum do Deezer no banco de dados: " . $conn->error;
        }
    }


}

header("Location: ../views/recomendacoes.php");
exit;

?>
