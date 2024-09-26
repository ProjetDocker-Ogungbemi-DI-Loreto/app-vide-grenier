<?php

namespace App\Models;

use Core\Model;
use App\Core;
use DateTime;
use Exception;
use App\Utility;

/**
 * Articles Model
 */
class Articles extends Model {

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getAll($filter) {
        $db = static::getDB();

        $query = 'SELECT * FROM articles ';

        switch ($filter){
            case 'views':
                $query .= ' ORDER BY articles.views DESC';
                break;
            case 'date':
                $query .= ' ORDER BY articles.published_date DESC';
                break;
            case '':
                break;
        }

        $stmt = $db->query($query);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getOne($id) {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT * FROM articles
            INNER JOIN users ON articles.user_id = users.id
            WHERE articles.id = ? 
            LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = static::getDB();

        // Requête avec une jointure entre articles et users
        $stmt = $db->prepare('
        SELECT articles.*, users.email, users.username 
        FROM articles
        INNER JOIN users ON articles.user_id = users.id
        WHERE articles.id = ?
        LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC); // Retourne un seul résultat
    }



    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function addOneView($id) {
        $db = static::getDB();

        $stmt = $db->prepare('
            UPDATE articles 
            SET articles.views = articles.views + 1
            WHERE articles.id = ?');

        $stmt->execute([$id]);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getByUser($id) {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            LEFT JOIN users ON articles.user_id = users.id
            WHERE articles.user_id = ?');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function getSuggest() {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            INNER JOIN users ON articles.user_id = users.id
            ORDER BY published_date DESC LIMIT 10');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function save($data, $db = null) {
        $db = $db ?? static::getDB();  // Utilise getDB si aucune DB n'est fournie

        $stmt = $db->prepare('INSERT INTO articles(name, description, user_id, published_date) VALUES (:name, :description, :user_id,:published_date)');

        $published_date =  new DateTime();
        $published_date = $published_date->format('Y-m-d');
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':published_date', $published_date);
        $stmt->bindParam(':user_id', $data['user_id']);

        $stmt->execute();

        return $db->lastInsertId();
    }

    public static function attachPicture($articleId, $pictureName){
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET picture = :picture WHERE articles.id = :articleid');

        $stmt->bindParam(':picture', $pictureName);
        $stmt->bindParam(':articleid', $articleId);


        $stmt->execute();
    }


    public static function search($query) {
        $db = static::getDB();

        // Requête SQL pour chercher dans plusieurs colonnes
        $stmt = $db->prepare("
        SELECT * FROM articles 
        WHERE name LIKE :query 
        OR description LIKE :query 
        OR ville LIKE :query
    ");

        $searchTerm = "%".$query."%";
        $stmt->bindParam(':query', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByCity($city)
    {
        $db = static::getDB();

        $stmt = $db->prepare('
        SELECT * FROM articles 
        WHERE ville = :city 
        ORDER BY published_date DESC');  // Articles les plus récents d'abord

        $stmt->bindParam(':city', $city);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function getFeatured()
    {
        $db = static::getDB();

        $stmt = $db->prepare('
        SELECT * FROM articles 
        ORDER BY views DESC 
        LIMIT 10');  // On limite à 10 articles à la une

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
