<?php
session_start(); 

require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  
  $sql = "SELECT * FROM users WHERE username = :username AND password = :password";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':password', $password);
  $stmt->execute();

  $result = $stmt->fetch();

 
  if ($result) {
    
    $_SESSION['user_id'] = $result['user_id'];
    $_SESSION['username'] = $result['username'];
    $_SESSION['role'] = $result['role'];

    if ($result['role'] == 'admin') {
      header('Location: index.html');
    } else {
      header('Location: studentPage.html');
    }
    exit; 
  } else {
    echo 'Geçersiz kullanıcı adı veya parola';
  }
}
?>
