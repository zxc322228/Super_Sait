<?php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .card {
            background: white;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, rgb(224, 14, 231) 0%, rgb(231, 11, 165) 100%);
            padding: 30px;
            text-align: center;
        }

        .card-header h1 {
            color: white;
            font-size: 24px;
            font-weight: normal;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: rgb(224, 14, 231);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, rgb(224, 14, 231) 0%, rgb(231, 11, 165) 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        button:hover {
            opacity: 0.9;
        }
        
        .card-footer {
            background: #f5f5f5;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .users-link {
            text-align: center;
            margin-top: 15px;
        }

        .users-link a {
            color: rgb(224, 14, 231);
            text-decoration: none;
            font-size: 14px;
        }

        .users-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <h1>Регистрация</h1>
    </div>

    <div class="card-body">
        <form action="" method="POST">
            <div class="form-group">
                <label>Имя</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="login" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Зарегистрироваться</button>
        </form>
        
        <div class="users-link">
            <a href="users.php">Посмотреть всех пользователей ↪</a>
        </div>
    </div>

    <div class="card-footer">
        <p>© 2025</p>
    </div>
</div>

<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connect = new mysqli("localhost", "root", "12345678", "rabota");
    if ($connect->connect_error) {
        die("Ошибка подключения: " . $connect->connect_error);
    }
    
    $name = $connect->real_escape_string($_POST['name']);
    $login = $connect->real_escape_string($_POST['login']);
    $email = $connect->real_escape_string($_POST['email']);
    $password = $connect->real_escape_string($_POST['password']);
    
    $sql = "INSERT INTO users (name, login, email, password) VALUES ('$name', '$login', '$email', '$password')";
    
    if ($connect->query($sql) === TRUE) {
        $_SESSION['message'] = "Регистрация успешна!";
    } else {
        $_SESSION['message'] = "Ошибка: " . $connect->error;
    }
    
    $connect->close();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}
?>

</body>
</html>