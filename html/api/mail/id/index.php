<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');
try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

$mail = new Mail($pdo);
$page = new Page();

// Get a mail entry from its id
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $singleMail = $mail->getMail($id);
    if (!$singleMail) {
        $page->notFound();
        exit;
    }
    $page->item($singleMail);
    exit;
}

// Update a mail entry from its id
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    // If subject or body are null send badRequest
    if (!isset($data['subject']) || !isset($data['body'])) {
        $page->badRequest();
        exit;
    }
    $updatedMail = $mail->updateMail($id, $data['subject'], $data['body']);
    if (!$updatedMail) {
        $page->notFound();
        exit;
    }
    $page->item($updatedMail);
    exit;
}

// Delete a mail entry from its id
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $deletedMail = $mail->deleteMail($id);
    if (!$deletedMail) {
        $page->notFound();
        exit;
    }
    exit;
}


$page->badRequest();


