
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecomendeMe.com.br</title>
    <!-- Seus estilos CSS e links para bibliotecas/frameworks aqui -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   


	<link rel="stylesheet" href="style.css">

    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&ampdisplay=swap" rel="stylesheet">





</head>
<body>
    <center><h1>RecomendeMe.com.br</h1></center>
    <br>
    <br>

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

    // Consulta SQL para obter as recomendações do banco de dados
    $sql = "SELECT * FROM recomendacoes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Saída dos dados de cada linha
        while($row = $result->fetch_assoc()) {
            $album_name = $row["titulo"];
            $user = $row["usuario"];
            $desc = $row["descricao"];
            $album_image_src = $row["img"];

            // Saída HTML dinâmica com os dados do banco de dados
                echo '<div class="album">';
                echo '<p class="teste"><a href=""><img src="' . $album_image_src . '" width="200" height="200"></a></p>';

                echo '<span id="name_album">' . $album_name . '</span>';
                echo '<p style="color:white;">Recomendado por <a class="reference" href="">' . $user . '</a></p>';
                echo '<center>';
                echo '<div class="icons2">';
                echo '<a href="https://open.spotify.com/album/4Q3ad90GtI0dxyE5ZishAS?autoplay=true"><img src="spotify.png"></a>';
              
                echo '<a href="https://www.deezer.com/br/album/12279688"><img src="deezer_f.png"></a>';
                echo '</div>';
                echo '</center>';
                echo '<br>';
                echo '<br>';
                echo '<br>';
                echo '<div class="image__overlay image__overlay--primary">';
                echo '<div class="image__title">@' . $user . '</div>';
                echo '<p class="image__description">' . $desc . '</p>';
                echo '</div>';
                echo '</div>';
        }
    } else {
        echo "0 resultados encontrados.";
    }
    $conn->close();
    ?>


<script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

</body>
</html>