<?php
/**
 * Description of books
 * Classe controladora da tabela books
 * 
 * @author Wilson Oliveira
 */
class Books {

    private $conn;
    private $table_name = "books";
    public $id;
    public $title;
    public $description;
    public $isbn;
    public $language;

    public function __construct($db) {
        $this->conn = $db;
    }
    function ler() {

        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }
    
    function lerUm() {

        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);
        
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->title = $row['title'];
        $this->description = $row['description'];
        $this->isbn = $row['isbn'];
        $this->language = $row['language'];

    }

}
