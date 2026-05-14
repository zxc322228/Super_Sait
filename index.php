<?php
include 'db.php';
$msg = '';
$error = '';
$old_name = $old_login = $old_email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n = $_POST['name'];
    $l = $_POST['login'];
    $e = $_POST['email'];
    $p = $_POST['password'];
    
    // Сохраняем старые значения для формы
    $old_name = $n;
    $old_login = $l;
    $old_email = $e;
    
    // имя не занято
    $check = $connect->query("SELECT id FROM users WHERE name='$n'");
    if($check->num_rows > 0) {
        $error = "Имя '$n' уже занято!";
    }
    
    // логин не занят
    $check = $connect->query("SELECT id FROM users WHERE login='$l'");
    if($check->num_rows > 0 && !$error) {
        $error = "Логин '$l' уже занят!";
    }
    
    // email не занят
    $check = $connect->query("SELECT id FROM users WHERE email='$e'");
    if($check->num_rows > 0 && !$error) {
        $error = "Email '$e' уже занят!";
    }
    
    // Проверка 4: пароль минимум 6 символов
    if(strlen($p) < 6 && !$error) {
        $error = "Пароль должен быть не менее 6 символов!";
    }
    
    // Если ошибок нет 
    if($error == '') {
        $connect->query("INSERT INTO users (name, login, email, password) VALUES ('$n','$l','$e','$p')");
        
        // Редирект через GET параметр
        header("Location: index.php?success=1");
        exit();
    }
}

// Показываем сообщение из GET параметра
if(isset($_GET['success'])) {
    $msg = "Регистрация успешна!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="register-page">
<div class="container">
    <div class="card">
        <div class="card-header"><h1>Регистрация</h1></div>
        <div class="card-body">
            <?php if($msg): ?>
                <div class="alert-success" style="background:#d4edda;padding:10px;margin-bottom:15px;border-radius:5px;text-align:center"><?php echo $msg; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert-error" style="background:#f8d7da;color:#721c24;padding:10px;margin-bottom:15px;border-radius:5px;text-align:center"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group"><label>Имя</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($old_name); ?>" required>
                </div>
                <div class="form-group"><label>Логин</label>
                    <input type="text" name="login" value="<?php echo htmlspecialchars($old_login); ?>" required>
                </div>
                <div class="form-group"><label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($old_email); ?>" required>
                </div>
                <div class="form-group"><label>Пароль</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Зарегистрироваться</button>
            </form>
            <div class="users-link"><a href="users.php">Список пользователей ↪</a></div>
        </div>
    </div>
</div>
</body>
</html>