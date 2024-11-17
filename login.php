<?php
session_start(); // 啟動 Session

// 連線到資料庫
$servername = "25.34.64.227";
$username = "wei";
$password = "table0813";
$dbname = "table0813";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查資料庫連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

$message = ''; // 用於顯示訊息

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 檢查用戶是否存在
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 取得用戶資料
        $row = $result->fetch_assoc();
        // 驗證密碼
        if (password_verify($password, $row['password'])) {
            // 登入成功，設置 Session
            $_SESSION['loggedin'] = true; // 設置登入狀態
            $_SESSION['user_id'] = $row['id']; // 儲存用戶 ID
            $_SESSION['username'] = $username; // 儲存用戶名

            // 重定向到 home.php
            header("Location: home.php");
            exit(); // 確保後續代碼不再執行
        } else {
            $message = "密碼錯誤";
        }
    } else {
        $message = "用戶不存在";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
</head>
<body>

<table width="300" align="center" border="0" cellpadding="10" cellspacing="0">
    <tr>
        <td>
            <h2 align="center">登入</h2>
            <form method="POST" action="">
                <table width="100%" border="0">
                    <tr>
                        <td align="left">使用者名稱:</td>
                        <td align="left"><input type="text" name="username" required></td>
                    </tr>
                    <tr>
                        <td align="left">密碼:</td>
                        <td align="left"><input type="password" name="password" required></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <button type="submit">登入</button>
                            <button type="button" onclick="window.location.href='register.php'">註冊</button>
                        </td>
                    </tr>
                </table>
            </form>

            <!-- 顯示訊息 -->
            <?php if ($message): ?>
                <p align="center" style="color: #ff0000;"><?php echo $message; ?></p>
            <?php endif; ?>
        </td>
    </tr>
</table>

</body>
</html>
