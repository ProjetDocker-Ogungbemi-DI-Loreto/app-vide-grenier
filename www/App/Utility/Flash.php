<?php

namespace App\Utility;

class Flash {
    public static function success($message) {
        $_SESSION['flash']['success'][] = $message;
    }

    public static function error($message) {
        $_SESSION['flash']['error'][] = $message;
    }

    public static function get() {
        $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
        unset($_SESSION['flash']);
        return $flash;
    }
}
