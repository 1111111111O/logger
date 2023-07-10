<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Veritabanı bağlantısı yapılır
    $servername = 'sql111.infinityfree.com';
    $username = 'if0_34571938';
    $password = 'FrnKH2kLSz6U6jC';
    $dbname = 'if0_34571938_chat';

    // Veritabanı bağlantısını oluştur
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Bağlantıyı kontrol et
    if ($conn->connect_error) {
        die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
    }

    // HTTP metodu kontrolü
    $method = $_SERVER['REQUEST_METHOD'];

    // Mesaj verilerini al
    if ($method === 'GET') {
        $username = isset($_GET['username']) ? $conn->real_escape_string($_GET['username']) : null;
        $message = isset($_GET['message']) ? $conn->real_escape_string($_GET['message']) : null;
    } elseif ($method === 'POST') {
        $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : null;
        $message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : null;
    }

    // Verilerin boş olup olmadığını kontrol et
    if ($username !== null && $message !== null) {
        // SQL enjeksiyon saldırılarına karşı hazırlıklı bir şekilde sorguyu hazırla
        $stmt = $conn->prepare("INSERT INTO gpt (username, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $message);

        // Sorguyu çalıştır
        if ($stmt->execute() === TRUE) {
            echo "Mesaj başarıyla kaydedildi.";
        } else {
            echo "Hata: " . $stmt->error;
        }

        // Bağlantıyı kapat
        $stmt->close();
    } else {
        echo "GET kullanarak mesaj göndermek istiyorsan parametreler boş olamaz.";
    }

    $conn->close();
}
?>
