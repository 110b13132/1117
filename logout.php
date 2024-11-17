<?php
session_start(); // 開啟會話

// 清除會話中的所有資料
session_unset();
session_destroy();

$message = "您已成功登出，將在 5 秒後自動返回首頁。";

// 設定 5 秒後跳轉到首頁
header("refresh:5;url=home.php");
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登出</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }
        .message {
            font-size: 18px;
            color: green;
        }
    </style>
</head>
<body>

    <h2>登出成功</h2>
    <p class="message"><?php echo $message; ?></p>
    <p>若未自動跳轉，請 <a href="home.php">點擊此處</a> 返回首頁。</p>

</body>
</html>
