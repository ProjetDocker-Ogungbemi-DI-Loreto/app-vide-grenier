<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use App\Utility\Flash;
use \Core\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;





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

    /**
     * Méthode pour traiter la soumission du formulaire de contact
     */

    public function contactAction()
    {
        if (isset($_POST['submit']) ||$_SERVER['REQUEST_METHOD'] === 'POST') {
            $articleId = $this->route_params['id'];
            $article = Articles::getById($articleId);

            if ($article) {
                $userEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $userMessage = htmlspecialchars($_POST['message']);
                $userName = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
                $userNumber = filter_var($_POST['number'], FILTER_SANITIZE_NUMBER_INT);

                $mail = new PHPMailer(true);

                try {
                    // Configuration du serveur SMTP
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Affiche les détails du serveur SMTP lors de l'envoi
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'dquentin107@gmail.com';
                    $mail->Password   = 'xexpkqaxtucnqogm';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;

                    $mail->setFrom('noreply@videgrenier.com', 'Administrateur du site');
                    $mail->addAddress($article['email'], $article['username']);
                    $mail->addAddress('dquentin107@gmail.com');


                    // Contenu de l'email
                    $mail->isHTML(true);
                    $mail->Subject = "Nouveau message à propos de votre annonce: " . $article['name'];
                    $mail->Body    = $this->getEmailBody($article['name'], $userMessage, $userNumber, $userName, $userEmail);
                    $mail->AltBody = $this->getEmailAltBody($article['name'], $userMessage, $userNumber, $userName, $userEmail);
                    $mail->setLanguage('fr', '/optionnal/path/to/language/directory/');

                    $mail->send();
                    Flash::success("Votre message a été envoyé avec succès !");
                } catch (Exception $e) {
                    Flash::error("Une erreur s'est produite lors de l'envoi de votre message. Veuillez réessayer plus tard." . $e->getMessage());
                    // Log l'erreur pour le débogage
                    error_log("Erreur d'envoi d'email: " . $e->getMessage());
                }
            } else {
                Flash::error("Article introuvable.");
            }

            header("Location: /product/$articleId");
            exit();
        }
    }

    private function getEmailBody($articleName, $userMessage, $userNumber, $userName, $userEmail)
    {
        return "
            <h1>Nouveau message concernant votre annonce: {$articleName}</h1>
            <p><strong>De:</strong> {$userName} ({$userEmail})</p>
            <p><strong>Numéro de téléphone :</strong> {$userNumber}</p>
            <p><strong>Message:</strong> {$userMessage}</p>
        ";
    }

    private function getEmailAltBody($articleName, $userMessage, $userNumber, $userName, $userEmail)
    {
        return "
            Nouveau message concernant votre annonce: {$articleName}
            De: {$userName} ({$userEmail})
            Numéro de téléphone: {$userNumber}
            Message: {$userMessage}
        ";
    }


}
