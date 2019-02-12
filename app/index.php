<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once 'simplehtmldom_1_8_1/simple_html_dom.php';
include_once 'database.php';
include_once 'books.php';

$database = new Database();
$db = $database->getConnection();

$books = new Books($db);

$stmt = $books->ler();
$num = $stmt->rowCount();

/*************************************/
//busca na base por id
/*************************************/
if (isset($_GET['id'])) {
    $books->id = $_GET['id'];
    $books->lerUm();

    if ($books->title != null) {
        // create array
        $book_arr = array(
            "title" => $books->title,
            "description" => $books->description,
            "isbn" => $books->isbn,
            "language" => $books->language
        );

        http_response_code(200);

        echo json_encode($book_arr, JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        // Se não encontrar no banco
        echo json_encode(array("message" => "Livro não encontrado."), JSON_PRETTY_PRINT);
    }
/*************************************/
//puxa os dados da url:https://kotlinlang.org/docs/books.html 
/*************************************/    
} else if (isset($_GET['url'])) {
    $html = file_get_html($_GET['url']);
    if ($html) {
        $books_url = array();
        
        foreach ($html->find('h2') as $el) {
            $h2 = $el;
            $titles[] = $h2->plaintext;
            while ($el = $el->next_sibling()) {
                $langs[] = $el->plaintext;
                if ('h2' != $el->tag)
                    break;
                
            }
            while ($a = $el->next_sibling()) {
                $ib = substr(strstr($a->href, 'dp/'), 3);
                $isbn = ($ib) ? $ib : "Unavailable";
                $isbns[] = $isbn;
                if ('h2' != $el->tag)
                    break;
                
            }
            while ($des = $a->next_sibling()) {
                $descriptions[] = $des->plaintext;
                if ('h2' != $el->tag)
                    break;
                
            }
        }
        $books_url["numberBooks"] = count($titles);
        $books_url["books"] = array();
        $books = array();
        for ($i = 0; $i < count($titles); $i++) {
            $books[$i] = array("id" => $i, "title" => $titles[$i], "descriptions" => trim($descriptions[$i]), "isbns" => $isbns[$i], "leng" => $langs[$i]);
            array_push($books_url["books"], $books);
        }
        echo json_encode($books_url, JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        // caso não encontre a url
        echo json_encode(array("message" => "Url Invalida."), JSON_PRETTY_PRINT);
    }
    
/*************************************/
//lista todos os registros da base
/*************************************/        
} else if (!isset($_GET['id']) && !isset($_GET['url']) && $num > 0) {

    $books_arr = array();
    $books_arr["books"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $books_item = array(
            "id" => $id,
            "title" => $title,
            "description" => html_entity_decode($description),
            "isbn" => $isbn,
            "language" => $language
        );

        array_push($books_arr["books"], $books_item);
    }

    http_response_code(200);

    echo json_encode($books_arr, JSON_PRETTY_PRINT);
} else {

    http_response_code(404);
    echo json_encode(
        // se não encontrar nada
            array("message" => "Nenhum livro encritrado.")
    );
}


