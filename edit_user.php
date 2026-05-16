<?php
include 'db.php';

$id = $_GET['id'];
$result = $connect->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit;
}

// Обработка загрузки файла
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['user_file'])) {
    $file = $_FILES['user_file'];
    if ($file['error'] == 0 && $file['size'] > 0) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = time() . "_" . rand(1000,9999) . "." . $ext;
        $destination = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $original = $connect->real_escape_string($file['name']);
            $connect->query("INSERT INTO user_files (user_id, filename, original_name, file_size) 
                           VALUES ($id, '$new_filename', '$original', {$file['size']})");
            header("Location: edit_user.php?id=$id&upload=success");
            exit;
        }
    }
}

// Удаление файла
if (isset($_GET['delete_file'])) {
    $file_id = $_GET['delete_file'];
    $file_result = $connect->query("SELECT filename FROM user_files WHERE id = $file_id AND user_id = $id");
    if ($file = $file_result->fetch_assoc()) {
        @unlink("uploads/" . $file['filename']);
        $connect->query("DELETE FROM user_files WHERE id = $file_id");
    }
    header("Location: edit_user.php?id=$id");
    exit;
}

// Обновление данных пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $name = $_POST['name'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($password != "") {
        $connect->query("UPDATE users SET name='$name', login='$login', email='$email', password='$password' WHERE id=$id");
    } else {
        $connect->query("UPDATE users SET name='$name', login='$login', email='$email' WHERE id=$id");
    }
    header("Location: users.php");
}

// Получаем файлы пользователя
$files_result = $connect->query("SELECT * FROM user_files WHERE user_id = $id ORDER BY upload_date DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <h1>Редактирование пользователя</h1>
            <p>ID: <?php echo $user['id']; ?> | <?php echo $user['name']; ?></p>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="login" value="<?php echo $user['login']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Новый пароль</label>
                    <input type="text" name="password" placeholder="Оставьте пустым, чтобы не менять">
                </div>
                <div class="btn-group">
                    <button type="submit" name="update_user" class="btn-save">Сохранить</button>
                    <a href="users.php" class="btn-cancel">Отмена</a>
                </div>
            </form>
            
            <!-- Кнопка удаления пользователя -->
            <div style="margin-top: 20px;">
                <a href="users.php?delete=<?php echo $id; ?>" onclick="return confirm('Удалить пользователя?')" style="text-decoration: none;">
                    <button class="delete-btn" style="width: 100%; background: #dc3545;">🗑️ Удалить пользователя</button>
                </a>
            </div>
            
            <!-- Загрузка файлов-->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <h3 style="color: white; margin-bottom: 15px;">Файлы пользователя</h3>
                
                <?php if(isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
                    <div class="alert-success" style="padding: 10px; margin-bottom: 15px;">Файл загружен!</div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <input type="file" name="user_file" required style="background: rgba(0,0,0,0.5);">
                    </div>
                    <button type="submit" style="background: #28a745; width: 100%;">⬆ Загрузить файл</button>
                </form>
                
                <?php if($files_result->num_rows > 0): ?>
                    <div style="background: rgba(0,0,0,0.3); border-radius: 10px; padding: 15px;">
                        <?php while($file = $files_result->fetch_assoc()): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; margin-bottom: 10px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="flex: 1;">
                                    <strong style="color: white;"><?php echo $file['original_name']; ?></strong>
                                    <div style="font-size: 12px; color: rgba(255,255,255,0.5);">
                                        <?php echo round($file['file_size']/1024, 2); ?> KB | <?php echo $file['upload_date']; ?>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="uploads/<?php echo $file['filename']; ?>" download style="background: #007bff; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px;">⬇ Скачать</a>
                                    <a href="?id=<?php echo $id; ?>&delete_file=<?php echo $file['id']; ?>" onclick="return confirm('Удалить файл?')" style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px;">🗑 Удалить</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: rgba(255,255,255,0.5); background: rgba(0,0,0,0.2); border-radius: 10px;">
                        Нет загруженных файлов
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>