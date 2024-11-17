<?php
// 連線到資料庫
$servername = "25.34.64.227";
$username = "wei";
$password = "table0813";
$dbname = "table0813";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

$message = ''; // 用於顯示訊息

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // 使用 BCRYPT 加密

    // 檢查電子郵件格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "無效的電子郵件格式";
    } else {
        // 檢查用戶名或郵件是否已存在
        $checkQuery = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            $message = "用戶名或郵件已存在";
        } else {
            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";

            if ($conn->query($sql) === TRUE) {
                // 註冊成功後重定向到登入頁面
                header("Location: login.php");
                exit(); // 確保後續代碼不再執行
            } else {
                $message = "錯誤: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊</title>
</head>
<body>

<table width="300" align="center" border="0" cellpadding="10" cellspacing="0">
    <tr>
        <td>
            <h2 align="center">註冊</h2>
            <form method="POST" action="">
                <table width="100%" border="0">
                    <tr>
                        <td align="left">使用者名稱:</td>
                        <td align="left"><input type="text" name="username" required></td>
                    </tr>
                    <tr>
                        <td align="left">電子郵件:</td>
                        <td align="left"><input type="email" name="email" required></td>
                    </tr>
                    <tr>
                        <td align="left">密碼:</td>
                        <td align="left"><input type="password" name="password" required></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <button type="submit">註冊</button>
                        </td>
                    </tr>
                </table>
            </form>

            <!-- 顯示訊息 -->
            <?php if ($message): ?>
                <p align="center" style="color: #ff0000;"><?php echo $message; ?></p>
            <?php endif; ?>

            <!-- 返回首頁按鈕 -->
            <div align="center" style="margin-top: 10px;">
                <a href="home.php">
                    <button type="button">返回首頁</button>
                </a>
            </div>
        </td>
    </tr>
</table>

</body>
</html>

