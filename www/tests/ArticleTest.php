<?php

use PHPUnit\Framework\TestCase;
use App\Models\Articles;

class ArticlesTest extends TestCase
{
    public function testSaveArticle()
    {
        // Créer un mock de la classe PDO
        $dbMock = $this->createMock(PDO::class);

        // Créer un mock de la méthode prepare() qui retourne un autre mock (statement)
        $stmtMock = $this->createMock(PDOStatement::class);

        // Simuler le comportement de prepare() pour qu'il retourne le mock de PDOStatement
        $dbMock->method('prepare')->willReturn($stmtMock);

        // Simuler le comportement de execute() pour qu'il retourne true
        $stmtMock->method('execute')->willReturn(true);

        // Simuler le comportement de lastInsertId() pour qu'il retourne l'ID "1"
        $dbMock->method('lastInsertId')->willReturn("1");

        // Données simulées pour la sauvegarde d'un article
        $articleData = [
            'name' => 'Titre de test',
            'description' => 'Description de test',
            'user_id' => 1
        ];

        // Appeler la méthode save() avec les données et le mock PDO
        $articleId = Articles::save($articleData, $dbMock);

        // Vérifier que l'ID retourné est bien "1"
        $this->assertEquals("1", $articleId);
    }
}
