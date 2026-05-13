<?php
$connect = new mysqli("localhost", "root", "12345678", "rabota");
if ($connect->connect_error) {
    die("Ошибка подключения: " . $connect->connect_error);
}
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $stmt = $connect->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}

function getUsers($conn) {
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
    $users = [];

    if ($result->num_rows > 0) {
        while ($user = $result->fetch_assoc()) {
            $users[] = $user;
        }
    }

    return $users;
}

$users = getUsers($connect);
$connect->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список пользователей</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, rgb(224, 14, 231) 0%, rgb(231, 11, 165) 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 32px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .back-btn {
            display: inline-block;
            background: white;
            color: rgb(224, 14, 231);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.9);
            transform: scale(1.02);
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .col {
            flex: 0 0 calc(33.333% - 20px);
            min-width: 300px;
        }

        @media (max-width: 768px) {
            .col {
                flex: 0 0 calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .col {
                flex: 0 0 100%;
            }
        }
        .user-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(135deg, rgb(224, 14, 231) 0%, rgb(231, 11, 165) 100%);
            padding: 20px;
            text-align: center;
        }
        .card-header h3 {
            color: white;
            font-size: 20px;
            font-weight: normal;
        }
        .card-body {
            padding: 20px;
            flex: 1;
        }
        .user-info {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .user-info strong {
            display: inline-block;
            width: 80px;
            color: rgb(224, 14, 231);
        }
        .user-info span {
            color: #555;
            word-break: break-all;
        }
        .user-id {
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .user-id strong {
            color: rgb(224, 14, 231);
        }
        .card-footer {
            background: #f5f5f5;
            padding: 12px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            width: 100%;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .empty-message {
            text-align: center;
            color: white;
            background: rgba(0,0,0,0.3);
            padding: 40px;
            border-radius: 10px;
            font-size: 18px;
        }
        .counter {
            text-align: center;
            color: white;
            margin-bottom: 20px;
            font-size: 14px;
            opacity: 0.9;
        }
        form {
            margin-top: 10px;
        }
    </style>
    <script>
        function confirmDelete(userId, userName) {
            return confirm('Вы уверены, что хотите удалить пользователя "' + userName + '" (ID: ' + userId + ')?');
        }
    </script>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-btn">↩ Назад к регистрации</a>
    
    <h1>Список зарегистрированных пользователей</h1>
    
    <div class="counter">
        Всего пользователей: <?php echo count($users); ?>
    </div>

    <div class="row">
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
                <div class="col">
                    <div class="user-card">
                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="user-info">
                                <strong>Логин:</strong>
                                <span><?php echo htmlspecialchars($user['login']); ?></span>
                            </div>
                            <div class="user-info">
                                <strong>Email:</strong>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="user-info">
                                <strong>Пароль:</strong>
                                <span><?php echo htmlspecialchars($user['password']); ?></span>
                            </div>
                            <div class="user-id">
                                <strong>ID:</strong> <?php echo $user['id']; ?>
                            </div>
                            <form method="post" onsubmit="return confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>');">
                                <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="delete-btn">🗑️ Удалить пользователя</button>
                            </form>
                        </div>
                        <div class="card-footer">
                            Зарегистрирован в системе
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-message">
                Пока нет зарегистрированных пользователей<br>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>