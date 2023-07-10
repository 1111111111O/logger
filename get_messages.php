<?php
// Veritabanı bağlantısı yapılır
$servername = 'sql111.infinityfree.com';
$username = 'if0_34571938';
$password = 'FrnKH2kLSz6U6jC';
$dbname = 'if0_34571938_chat';

// Bağlantı oluşturulur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı hatasını kontrol edin
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Mesajları veritabanından alınır
$sql = "SELECT * FROM gpt";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
                $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
                echo '<strong>' . $username . ':</strong> ' . $message . '<br>';
            }
        } else {
            echo 'Henüz mesaj bulunmamaktadır.';
        }
        $result->free_result(); // Sonuç kümesini serbest bırak
    } else {
        echo 'Sorgu hatası: ' . $stmt->error;
    }
    $stmt->close();
} else {
    echo 'Sorgu hazırlama hatası: ' . $conn->error;
}

// Bağlantıyı kapat
$conn->close();
?>
