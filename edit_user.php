<?php
include 'db.php';

$id = $_GET['id'];
$result = $connect->query("SELECT * FROM users WHERE id = $id");
// превращает данные с бд в массив
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 600px;">
    <div class="card">
        <div class="card-header">
            <h1>Редактирование пользователя</h1>
            <p>Измените данные и сохраните изменения</p>
        </div>
        <div class="card-body">
            <div class="info-id">
                Редактирование пользователя с ID: <strong><?php echo $user['id']; ?></strong>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label class="required">Имя</label>
                    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="required">Логин</label>
                    <input type="text" name="login" value="<?php echo $user['login']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="required">Email</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Новый пароль</label>
                    <input type="text" name="password" placeholder="Оставьте пустым, чтобы не менять пароль">
                    <div class="help-text">Если хотите изменить пароль, введите новый. Иначе оставьте поле пустым.</div>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn-save">Сохранить изменения</button>
                    <a href="users.php" class="btn-cancel">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>