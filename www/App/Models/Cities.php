<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * City Model:
 */
class Cities extends Model {

    public static function searchLike($str) {
        $db = static::getDB();

        $query = 'SELECT ville_nom_reel FROM villes_france
        WHERE ville_nom LIKE :query
        OR ville_nom_reel LIKE :query
        OR ville_nom_simple LIKE :query
        OR ville_code_postal LIKE :query ;';

        $queryParam = $str . '%';

        $stmt = $db->prepare($query);

        $stmt->bindParam(':query', $queryParam);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    public static function search($str) {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT ville_id FROM villes_france WHERE ville_nom_reel LIKE :query');

        $query = $str . '%';

        $stmt->bindParam(':query', $query);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }
}
