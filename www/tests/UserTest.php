<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    // Test de la création d'utilisateur avec injection de dépendance
    public function testCreateUser()
    {
        // On crée un mock de la classe PDO
        $dbMock = $this->createMock(PDO::class);

        // On crée un mock de la méthode prepare() qui retourne un autre mock (statement)
        $stmtMock = $this->createMock(PDOStatement::class);

        // On définit le comportement de prepare() et de execute() pour retourner des valeurs simulées
        $dbMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('bindParam')->willReturn(true);

        // On simule que l'ID de l'utilisateur est "1" (retour sous forme de chaîne)
        $dbMock->method('lastInsertId')->willReturn("1");

        // Données simulées pour la création d'un utilisateur
        $userData = [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'password' => 'hashed_password',
            'salt' => 'random_salt'
        ];

        // On appelle la méthode createUser() avec les données simulées et le mock de PDO
        $userId = User::createUser($userData, $dbMock);

        // Vérification que l'ID retourné est bien "1" en tant que chaîne
        $this->assertEquals("1", $userId);
    }
}
