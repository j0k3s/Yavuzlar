<?php
session_start(); 

try {
    // Veritabanı bağlantı bilgilerini güncelle
    $host = 'mysql'; // Docker Compose'da MySQL servis adı
    $dbname = 'tezgah';
    $username = 'root';
    $password = ''; // Güvenlik için güçlü bir şifre kullanmanızı öneririm

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Kullanıcı girişi kontrolü
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Oturum bilgilerini ayarla
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Kullanıcı rolüne göre yönlendirme yap
            switch ($user['role']) {
                case 'admin':
                    header("Location: adminPage.php");
                    break;
                case 'shop':
                    header("Location: restaurantPage.php");
                    break;
                case 'customer':
                    header("Location: customerPage.php");
                    break;
                default:
                    echo "Geçersiz kullanıcı rolü.";
                    exit;
            }
            exit;
        } else {
            echo "Geçersiz kullanıcı adı veya şifre.";
        }
    } else {
        echo "Geçersiz istek yöntemi.";
    }

} catch (PDOException $e) {
    // Hata mesajını günlüğe kaydet, kullanıcıya genel bir hata mesajı göster
    error_log("Veritabanı hatası: " . $e->getMessage());
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
}
?>