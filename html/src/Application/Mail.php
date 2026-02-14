<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createMail($subject, $body) {
        $stmt = $this->pdo->prepare("INSERT INTO mail (subject, body) VALUES (?, ?) RETURNING id, subject, body");
        $stmt->execute([$subject, $body]);

        //return $stmt->fetchColumn();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMail($id) {
        $stmt = $this->pdo->prepare("SELECT id, subject, body FROM mail WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllMail() {
        $stmt = $this->pdo->query("SELECT id, subject, body FROM mail ORDER BY id");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMail($id, $subject, $body) {
        $stmt = $this->pdo->prepare("UPDATE mail SET subject = ?, body = ? WHERE id = ? RETURNING id, subject, body");
        $stmt->execute([$subject, $body, $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Reference:
    // https://stackoverflow.com/questions/40039725/how-to-know-if-the-delete-query-really-deletes-a-row-using-pdo
    // I used this reference to show me how I can get this function to return a true or false for my testDeleteMail.
    public function deleteMail($id) {
        $stmt = $this->pdo->prepare("DELETE FROM mail WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}