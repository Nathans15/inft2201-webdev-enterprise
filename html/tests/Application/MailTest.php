<?php
use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Clean and reinitialize the table
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }

    // Test suite

    // Test 01 - Create a mail entry
    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $createdMail = $mail->createMail("Alice", "Hello world");
        $this->assertIsArray($createdMail);
        $this->assertEquals(1, $createdMail['id']);
        $this->assertEquals("Alice", $createdMail['subject']);
        $this->assertEquals("Hello world", $createdMail['body']);
    }

    // Test 02 - Get a mail entry
    public function testGetMail() {
        $mail = new Mail($this->pdo);
        $createdMail = $mail->createMail("Alice", "Hello world");
        $gottenMail = $mail->getMail($createdMail['id']);
        $this->assertIsArray($gottenMail);
        $this->assertEquals("Alice", $gottenMail['subject']);
        $this->assertEquals("Hello world", $gottenMail['body']);
    }

    // Test 03 - Get all mail entries
    public function testGetAllMail() {
        $mail = new Mail($this->pdo);
        $mail->createMail("First", "Mail1");
        $mail->createMail("Second", "Mail2");
        $allMail = $mail->getAllMail();
        $this->assertCount(2, $allMail);
        $this->assertEquals("First", $allMail[0]['subject']);
        $this->assertEquals("Mail1", $allMail[0]['body']);
        $this->assertEquals("Second", $allMail[1]['subject']);
        $this->assertEquals("Mail2", $allMail[1]['body']);
    }

    // Test 04 - Update a mail entry
    public function testUpdateMail() {
        $mail = new Mail($this->pdo);
        $originalMail = $mail->createMail("OG", "Original Mail");
        $updatedMail = $mail->updateMail($originalMail['id'],"N","New Mail");
        $this->assertIsArray($updatedMail);
        $this->assertEquals("N", $updatedMail['subject']);
        $this->assertEquals("New Mail", $updatedMail['body']);
    }

    // Test 05 - Delete a mail entry
    public function testDeleteMail() {
        $mail = new Mail($this->pdo);
        $createdMail = $mail->createMail("Alice", "Hello world");
        $deletedMail = $mail->deleteMail($createdMail['id']);
        $this->assertTrue($deletedMail);
        $deleteCheck = $mail->getMail($createdMail['id']);
        $this->assertFalse($deleteCheck);
    }
}