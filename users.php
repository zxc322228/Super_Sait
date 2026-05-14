<?php
include 'db.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $connect->query("DELETE FROM users WHERE id = $id");
    header("Location: users.php");
}

$result = $connect->query("SELECT * FROM users");
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
        // уточняет то чно ли нужно удалить confirmDelete это функция которая спрашивает тока тут в коде она
        function confirmDelete(userId, userName) {
            return confirm('Вы уверены, что хотите удалить пользователя "' + userName + '" (ID: ' + userId + ')?');
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