<?php
try {
    $pdo = new PDO('sqlite:Quest.db');
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Veritabanına başarıyla bağlanıldı!<br>";

    $login_sql = "CREATE TABLE IF NOT EXISTS users (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                password TEXT NOT NULL,
                role TEXT NOT NULL CHECK(role IN ('admin', 'student'))
            )";
    $pdo->exec($login_sql);
    echo "Users tablosu başarıyla oluşturuldu!<br>";

    $sql = "INSERT INTO users (username, password, role) VALUES 
    ('admin', 'admin', 'admin'),
    ('student1', 'student1', 'student'),
    ('student2', 'student2', 'student'),
    ('student3', 'student3', 'student')";

$pdo->exec($sql);
echo "admin:admin ve student1:student1 bilgileri ile giriş yapabilirsiniz!<br>";


    $questions_sql = "CREATE TABLE IF NOT EXISTS questions (
        question_id INTEGER PRIMARY KEY AUTOINCREMENT,
        question_text TEXT NOT NULL,
        option1 TEXT NOT NULL,
        option2 TEXT NOT NULL,
        option3 TEXT NOT NULL,
        option4 TEXT NOT NULL,
        true_option INTEGER TEXT NOT NULL,
        difficulty TEXT NOT NULL
    )";

    $default_quest = "INSERT INTO questions (question_text, option1, option2, option3, option4, ture_option, difficulty) VALUES

        ('Türkiyenin başkenti neresi?', 'Ankara', 'İstanbul','Bursa','İzmir','Ankara','easy',),
        ('Hayko Cepkin nerede doğdu?', 'Çorum', 'Çankırı','Tekirdağ','İstanbul','İstanbul','hard',),
        ('Türkiyede maraşel rütbesine sahip kaç kişi vardır?', '1', '4','3','2','2','medium',)
        )";



    $pdo->exec($questions_sql);
    echo "Questions tablosu başarıyla oluşturuldu!<br>";

    $pdo->exec($sql);
    
    

    
    $submissions_sql = "CREATE TABLE IF NOT EXISTS submissions (
                submission_id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                question_id INTEGER NOT NULL,
                is_correct INTEGER NOT NULL,
                FOREIGN KEY(user_id) REFERENCES users(user_id),
                FOREIGN KEY(question_id) REFERENCES questions(question_id)
            )";
    $pdo->exec($submissions_sql);
    echo "Submissions tablosu başarıyla oluşturuldu!<br>";

} catch (PDOException $e) {
    
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}
?>
