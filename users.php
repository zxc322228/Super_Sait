<?php
include 'db.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Удаляем файлы пользователя
    $files = $connect->query("SELECT filename FROM user_files WHERE user_id = $id");
    while($file = $files->fetch_assoc()) {
        @unlink("uploads/" . $file['filename']);
    }
    $connect->query("DELETE FROM user_files WHERE user_id = $id");
    $connect->query("DELETE FROM users WHERE id = $id");
    header("Location: users.php");
}

$result = $connect->query("SELECT u.*, COUNT(uf.id) as files_count, MIN(uf.filename) as first_file 
                          FROM users u 
                          LEFT JOIN user_files uf ON u.id = uf.user_id 
                          GROUP BY u.id");
$users = [];
while ($user = $result->fetch_assoc()) {
    $users[] = $user;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список пользователей</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete(userId, userName) {
            return confirm('Вы уверены, что хотите удалить пользователя "' + userName + '" (ID: ' + userId + ') вместе со всеми файлами?');
        }
    </script>
</head>
<body>
<div class="container">
    <a href="index.php" class="back-btn">↩ Назад к регистрации</a>
    <h1>Список зарегистрированных пользователей</h1>
    <div class="counter">Всего пользователей: <?php echo count($users); ?></div>
    
    <div class="row">
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
                <div class="col">
                    <div class="user-card">
                        <div class="card-header">
                            <h3><?php echo $user['name']; ?></h3>
                        </div>
                        <div class="card-body">
                            <?php if($user['first_file']): ?>
                                <div class="user-info" style="text-align: center;">
                                    <?php 
                                    $ext = strtolower(pathinfo($user['first_file'], PATHINFO_EXTENSION));
                                    if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                        <img src="uploads/<?php echo $user['first_file']; ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
                                    <?php else: ?>
                                        <div style="font-size: 40px;"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="user-info">
                                <strong>Логин:</strong>
                                <span><?php echo $user['login']; ?></span>
                            </div>
                            <div class="user-info">
                                <strong>Email:</strong>
                                <span><?php echo $user['email']; ?></span>
                            </div>
                            <div class="user-info">
                                <strong>Пароль:</strong>
                                <span><?php echo $user['password']; ?></span>
                            </div>
                            <div class="user-info">
                                <strong>Файлы:</strong>
                                <span><?php echo $user['files_count']; ?> шт.</span>
                            </div>
                            <div class="user-id">
                                <strong>ID:</strong> <?php echo $user['id']; ?>
                            </div>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="edit-btn">
                                ✏️ Редактировать
                            </a>
                            <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirmDelete(<?php echo $user['id']; ?>, '<?php echo $user['name']; ?>')">
                                <button class="delete-btn">🗑️ Удалить пользователя</button>
                            </a>
                        </div>
                        <div class="card-footer">
                            Зарегистрирован в системе
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-message">
                Пока нет зарегистрированных пользователей
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>