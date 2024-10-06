<?php
session_start();
require 'db.php'; 


if (!isset($_SESSION['user_id'])) {
    echo "Lütfen önce giriş yapın.";
    exit;
}

$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['profile_image']['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    if (in_array(strtolower($filetype), $allowed)) {
        $newname = uniqid() . '.' . $filetype;
        $upload_dir = 'uploads/profile_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $upload_path = $upload_dir . $newname;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
          
            $stmt = $pdo->prepare("UPDATE users SET image_path = :image_path WHERE id = :user_id");
            $stmt->execute([':image_path' => $upload_path, ':user_id' => $user_id]);

           
            $user['image_path'] = $upload_path;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sayfası</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="profile-page">
    <h2>Profil Bilgileri</h2>
    <img src="images/yavuzlarlogo.jpg" alt="yavuzlar" style="width:200px;height:auto;">

    <?php if ($user): ?>
        <?php if (!empty($user['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($user['image_path']); ?>" alt="Profil Fotoğrafı" style="max-width: 200px;">
        <?php else: ?>
            <p>Profil fotoğrafı yüklenmemiş.</p>
        <?php endif; ?>
        
        <p>Ad: <?php echo htmlspecialchars($user['name']); ?></p>
        <p>Soyad: <?php echo htmlspecialchars($user['surname']); ?></p>
        <p>Kullanıcı Adı: <?php echo htmlspecialchars($user['username']); ?></p>
        <p>Bakiye: <?php echo htmlspecialchars($user['balance']); ?> TL</p>
        <p>Rol: <?php echo htmlspecialchars($user['role']); ?></p>
        
        <h3>Profil Fotoğrafı Yükle</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_image" accept="image/*">
            <button type="submit">Fotoğrafı Yükle</button>
        </form>
    <?php else: ?>
        <p>Kullanıcı bilgileri bulunamadı.</p>
    <?php endif; ?>

    <form action="customerPage.php" method="GET">
        <button type="submit" name="view_profile">Anasayfaya Dön</button>
    </form>
</body>
</html>
