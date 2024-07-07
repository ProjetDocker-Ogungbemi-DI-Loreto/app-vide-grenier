<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use App\Utility\Flash;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction()
    {
        if(isset($_POST['submit'])) {

            try {
                $f = $_POST;

                // Validation
                if (empty($f['name'])) {
                    Flash::error('Le titre est requis.');
                }
                if (empty($f['description'])) {
                    Flash::error('La description est requise.');
                }
                if (empty($_FILES['picture']['name'])) {
                    Flash::error('L\'image est requise.');
                }
                if (empty($f['city'])) {
                    Flash::error('La ville est requise.');
                }

                $flashMessages = Flash::get();
                if (!empty($flashMessages['error'])) {
                    // Reload the form with error messages
                    View::renderTemplate('Product/Add.html', [
                        'flash' => $flashMessages
                    ]);
                    return;
                }

                $f['user_id'] = $_SESSION['user']['id'];
                $id = Articles::save($f);

                $pictureName = Upload::uploadFile($_FILES['picture'], $id);

                Articles::attachPicture($id, $pictureName);

                header('Location: /product/' . $id);
                exit;
            } catch (\Exception $e){
                var_dump($e);
            }
        }

        View::renderTemplate('Product/Add.html');
    }

    /**
     * Affiche la page d'un produit
     * @return void
     */
    public function showAction()
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch(\Exception $e){
            var_dump($e);
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);
    }
}
