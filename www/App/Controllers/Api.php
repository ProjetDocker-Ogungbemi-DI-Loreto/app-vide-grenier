<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use \Core\View;
use Exception;

/**
 * API controller
 */
class Api extends \Core\Controller
{

    /**
     * Affiche la liste des articles / produits pour la page d'accueil
     *
     * @throws Exception
     */
    public function ProductsAction()
    {
        $query = $_GET['sort'] ?? '';

        try {
            $articles = Articles::getAll($query);
            if (!empty($articles)) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($articles);
            } else {
                header('Content-Type: application/json', true, 404);
                echo json_encode(['message' => 'Aucun article trouvé.']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        }
    }

    public function CitiesAutocompleteAction(){
        
        if(!isset($_GET['query']) || $_GET['query'] == "")
            echo json_encode([]);

        $cities = Cities::searchLike($_GET['query']);

        header('Content-Type: application/json');
        echo json_encode($cities);
    }

    /**
     * Recherche des villes dans la table ville_frances
     */
    public function Cities()
    {
        $query = $_GET['ville_nom_reel'] ?? '';

        try {
            if ($query) {
                $cities = Cities::search($query);
                header('Content-Type: application/json', true, 200);
                echo json_encode($cities);
            } else {
                header('Content-Type: application/json', true, 400);
                echo json_encode(['error' => 'Paramètre de ville manquant.']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        }
    }

    /**
     * Recherche d'articles
     */
    public function SearchArticles()
    {
        $query = $_GET['name'] ?? '';

        try {
            if ($query) {
                $articles = Articles::search($query);
                header('Content-Type: application/json', true, 200);
                echo json_encode($articles);
            } else {
                // Default behaviour if no search term is provided
                $articles = Articles::getAll('');
                header('Content-Type: application/json', true, 200);
                echo json_encode($articles);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        }
    }


    /**
     * Récupère les articles "À la une" (les plus populaires).
     */
    public function Featured()
    {
        try {
            $articles = Articles::getFeatured();
            if (!empty($articles)) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($articles);
            } else {
                header('Content-Type: application/json', true, 404);
                echo json_encode(['message' => 'Aucun article "À la une" trouvé.']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        }
    }

    /**
     * Récupère les articles "Autour de moi" en fonction de la ville de l'utilisateur.
     */
    public function Nearby()
    {
        $city = $_GET['ville'] ?? '';

        try {
            if ($city) {
                $articles = Articles::getByCity($city);
                if (!empty($articles)) {
                    header('Content-Type: application/json', true, 200);
                    echo json_encode($articles);
                } else {
                    header('Content-Type: application/json', true, 404);
                    echo json_encode(['message' => 'Aucun article trouvé pour cette ville.']);
                }
            } else {
                header('Content-Type: application/json', true, 400);
                echo json_encode(['error' => 'Paramètre de ville manquant.']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        }
    }

}
