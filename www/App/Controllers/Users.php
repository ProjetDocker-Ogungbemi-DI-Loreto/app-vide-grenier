<?php

namespace App\Controllers;

use App\Config;
use App\Models\User;
use App\Models\Articles;
use App\Utility\Hash;
use App\Utility\Flash;
use \Core\View;
use Exception;
use http\Env\Request;
use http\Exception\InvalidArgumentException;

/**
 * Users controller
 */
class Users extends \Core\Controller
{

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if (isset($_POST['submit'])) {
            $f = $_POST;

            // Validation
            if (empty($f['email']) || empty($f['password'])) {
                Flash::error('L\'Email ou le mot de passe sont requis.');
                View::renderTemplate('User/login.html', ['flash' => Flash::get()]);
                return;
            }

            if ($this->login($f)) {
                // Si login OK, redirige vers le compte
                header('Location: /account');
                exit;
            } else {
                Flash::error('L\'Email ou le mot de passe incorrect.');
            }
        }

        View::renderTemplate('User/login.html', ['flash' => Flash::get()]);
    }

    /**
     * Page de création de compte
     */
    public function registerAction()
    {
        if (isset($_POST['submit'])) {
            $f = $_POST;

            if ($f['password'] !== $f['password-check']) {
                Flash::error('Les mots de passe ne correspondent pas.');
                View::renderTemplate('User/register.html', ['flash' => Flash::get()]);
                return;
            }

            // Validation
            if (empty($f['email']) || empty($f['username']) || empty($f['password'])) {
                Flash::error('Tous les champs sont requis.');
                View::renderTemplate('User/register.html', ['flash' => Flash::get()]);
                return;
            }

            if ($this->register($f)) {
                $this->login($f);
                header('Location: /account');
                exit;
            } else {
                Flash::error('Une erreur est survenue lors de l\'enregistrement.');
            }
        }

        View::renderTemplate('User/register.html', ['flash' => Flash::get()]);
    }

    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles,
            'flash' => Flash::get()
        ]);
    }

    /*
     * Fonction privée pour enregistrer un utilisateur
     */
    private function register($data)
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.
            $salt = Hash::generateSalt(32);

            $userID = User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt
            ]);

            return $userID;

        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
            return false;
        }
    }

    private function login($data)
    {
        try {
            if (!isset($data['email'])) {
                throw new Exception('L\'Email est requis.');
            }

            $user = User::getByLogin($data['email']);

            if (!$user || Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                return false;
            }

            if (isset($data['remember_me'])) {
                setcookie('remember_me', $user['id'], time() + (86400 * 30), "/"); // 30 jours
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];

            return true;

        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
            return false;
        }
    }

    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction()
    {
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, "/");
        }
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header("Location: /");
        return true;
    }

}
