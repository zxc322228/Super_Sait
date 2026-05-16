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
    
    // Проверка пароля
    if(strlen($p) < 6 && !$error) {
        $error = "Пароль должен быть не менее 6 символов!";
    }
    
    // Если ошибок нет - создаем пользователя
    if($error == '') {
        // Экранируем данные
        $n_safe = $connect->real_escape_string($n);
        $l_safe = $connect->real_escape_string($l);
        $e_safe = $connect->real_escape_string($e);
        $p_safe = $connect->real_escape_string($p);
        
        $connect->query("INSERT INTO users (name, login, email, password) VALUES ('$n_safe','$l_safe','$e_safe','$p_safe')");
        $user_id = $connect->insert_id;
        
        // Загружаем файл если есть
        if(isset($_FILES['user_file']) && $_FILES['user_file']['error'] == 0 && $_FILES['user_file']['size'] > 0) {
            $file = $_FILES['user_file'];
            $original_name = $file['name'];
            $file_size = $file['size'];
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $new_filename = time() . "_" . rand(1000,9999) . "." . $ext;
            
            if($file['size'] <= 10 * 1024 * 1024) { // 10MB лимит
                if(move_uploaded_file($file['tmp_name'], "uploads/" . $new_filename)) {
                    $original_name_escaped = $connect->real_escape_string($original_name);
                    $connect->query("INSERT INTO user_files (user_id, filename, original_name, file_size) 
                                   VALUES ($user_id, '$new_filename', '$original_name_escaped', $file_size)");
                }
            }
        }
        
        // Редирект через GET параметр
        header("Location: index.php?success=1");
        exit();
    }
}

// Показываем сообщение из GET параметра
if(isset($_GET['success'])) {
    $msg = "Регистрация успешна! <a href='users.php' style='color: #88ff88;'>Перейти к списку пользователей →</a>";
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
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($old_name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="login" value="<?php echo htmlspecialchars($old_login); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($old_email); ?>" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Аватар/Файл (необязательно)</label>
                    <input type="file" name="user_file" accept="image/*,.pdf,.txt,.doc">
                    <div class="help-text">Можно загрузить фото, документ или любой файл (макс. 10MB)</div>
                </div>
                <button type="submit">Зарегистрироваться</button>
            </form>
            <div class="users-link"><a href="users.php">Список пользователей ↪</a></div>
        </div>
    </div>
</div>
</body>
</html>