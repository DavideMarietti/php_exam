<?php

class Post
{

    private $conn;

    public $id;
    public $titolo;
    public $testo;
    public $autore;
    private $like;
    private $dislike;
    public $creato;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->like = "[]";
        $this->dislike = "[]";
        $this->creato = date("Y-m-d H:i:s");
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id_par)
    {
        $this->id = $id_par;
    }

    public function getTitolo()
    {
        return $this->titolo;
    }

    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;
    }

    public function getTesto()
    {
        return $this->testo;
    }

    public function setTesto($testo)
    {
        $this->testo = $testo;
    }

    public function getAutore()
    {
        return $this->autore;
    }

    public function setAutore($autore)
    {
        $this->autore = $autore;
    }

    public function getLike()
    {
        return $this->like;
    }

    public function setLike($like)
    {
        $this->like = $like;
    }

    public function giveLike($userid)
    {
        $d = (array)$this->dislike;
        $l = (array)$this->like;
        if (in_array($userid, $d)) {
            $dislikeIndex = array_search($userid, $d);
            unset($d[$dislikeIndex]);
            // Re-index the array after removing the element
            $this->dislike = json_encode($d);
        } else {
            $this->dislike = json_encode($d);
        }

        if (in_array($userid, $l)) {
            $likeIndex = array_search($userid, $l);
            unset($l[$likeIndex]);
            // Re-index the array after removing the element
            $this->like = json_encode($l);
        } else {
            $l[] = $userid;
            $this->like = json_encode($l);
        }
    }

    public function getDislike()
    {
        return $this->dislike;
    }

    public function setDislike($dislike)
    {
        $this->dislike = $dislike;
    }

    public function giveDislike($userid)
    {
        if (in_array($userid, $this->like)) {
            $likeIndex = array_search($userid, $this->like);
            unset($this->like[$likeIndex]);
            // Re-index the array after removing the element
            $this->like = array_values($this->like);
        }

        if (in_array($userid, $this->dislike)) {
            $dislikeIndex = array_search($userid, $this->dislike);
            unset($this->dislike[$dislikeIndex]);
            // Re-index the array after removing the element
            $this->dislike = array_values($this->dislike);
        } else {
            $this->dislike[] = $userid;
        }
    }

    public function getCreato()
    {
        return $this->creato;
    }

    public function setCreato($creato)
    {
        $this->creato = $creato;
    }

    function read()
    {
        $query = "SELECT * FROM posts";
        // query preparation
        $stmt = $this->conn->prepare($query);
        // query execution
        $stmt->execute();

        return $stmt;
    }

    function readByAutore()
    {
        $query = "SELECT * FROM posts WHERE posts.autore = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->autore);
        $stmt->execute();

        return $stmt;
    }

    function readById()
    {
        $query = "SELECT * FROM posts WHERE posts.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        return $stmt;
    }

    function create()
    {
        $query = "INSERT INTO posts SET
				  titolo=:titolo, testo=:testo, autore=:autore, posts.like= :like, posts.dislike=:dislike, creato=:creato";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":testo", $this->testo);
        $stmt->bindParam(":autore", $this->autore);
        $stmt->bindParam(":like", $this->like);
        $stmt->bindParam(":dislike", $this->dislike);
        $stmt->bindParam(":creato", $this->creato);

        $stmt->execute();

        return $stmt;
    }


    function updateLikeDislike()
    {
        $query = "UPDATE posts SET
					posts.like = :l,
					posts.dislike = :d
					WHERE
					id = :i";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':l', $this->like);
        $stmt->bindParam(':d', $this->dislike);
        $stmt->bindParam(':i', $this->id);
        $stmt->execute();

        return $stmt;
    }

    function delete()
    {
        $query = "DELETE FROM posts WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    function deleteAll()
    {
        //aggiungere eliminazione commenti sotto il post
        $query = "DELETE FROM posts";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}

?>