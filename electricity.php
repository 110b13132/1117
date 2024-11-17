<?php
session_start(); // 啟動 Session

// 檢查用戶是否已登入，未登入則重定向至登入頁面
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

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

$user_id = $_SESSION['user_id']; // 取得登入用戶的 ID
$message = ''; // 用於顯示訊息

// 檢查是否有提交用電數據進行電費計算
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usage_kwh = filter_var($_POST['usage_kwh'], FILTER_VALIDATE_FLOAT);
    $billing_type = $_POST['billing_type'];
    $is_summer = isset($_POST['is_summer']); // 判斷是否勾選夏季
    $bill_amount = 0;

    if ($usage_kwh === false) {
        $message = "請輸入有效的用電量（度數）";
    } else {
        // 非時間電價計算邏輯
        function calculate_non_time_bill($usage_kwh, $is_summer) {
            $rates_summer = [
                120 => 1.63,
                330 => 2.38,
                500 => 3.52,
                700 => 4.80,
                1000 => 5.66,
                PHP_INT_MAX => 6.41
            ];
            
            $rates_non_summer = [
                120 => 1.63,
                330 => 2.10,
                500 => 2.89,
                700 => 3.94,
                1000 => 4.60,
                PHP_INT_MAX => 5.03
            ];
            
            $rates = $is_summer ? $rates_summer : $rates_non_summer;
            
            $total_bill = 0;
            $remaining_usage = $usage_kwh;
            
            foreach ($rates as $limit => $rate) {
                if ($remaining_usage <= 0) break;
                $usage_in_band = min($remaining_usage, $limit);
                $total_bill += $usage_in_band * $rate;
                $remaining_usage -= $usage_in_band;
            }
            
            return $total_bill;
        }

        // 時間電價計算邏輯
        function calculate_time_bill($usage_kwh, $is_summer) {
            $peak_rate = $is_summer ? 4.44 : 4.23;
            $off_peak_rate = $is_summer ? 1.80 : 1.73;
            
            $peak_usage = $usage_kwh * 0.60;
            $off_peak_usage = $usage_kwh * 0.40;
            
            $bill = ($peak_usage * $peak_rate) + ($off_peak_usage * $off_peak_rate);
            
            if ($usage_kwh > 2000) {
                $extra_rate = $is_summer ? 0.96 : 0.96; // 這裡應該有不同的邏輯
                $bill += ($usage_kwh - 2000) * $extra_rate;
            }
            
            return $bill;
        }

        // 計算電費
        if ($billing_type == 'non-time') {
            $bill_amount = calculate_non_time_bill($usage_kwh, $is_summer);
        } else if ($billing_type == 'time') {
            $bill_amount = calculate_time_bill($usage_kwh, $is_summer);
        }

        // 插入計算結果到資料庫
        $stmt = $conn->prepare("INSERT INTO electricity_usage (user_id, usage_kwh, bill_amount, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("idd", $user_id, $usage_kwh, $bill_amount);

        if ($stmt->execute()) {
            $message = "電費計算完成並已儲存。總電費: $bill_amount 元";
        } else {
            $message = "錯誤: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電費計算和查詢</title>
</head>
<body>

<!-- 導覽列 -->
<table width="100%" border="0" cellpadding="10" cellspacing="0">
    <tr>
        <td align="center" bgcolor="#79FF79">
            <a href="home.php" style="margin: 0 15px; text-decoration: none; color: #000;">首頁</a>
            <a href="electricity.php" style="margin: 0 15px; text-decoration: none; color: #000;">查詢用電紀錄和計算電費</a>
            
            <?php if (isset($_SESSION['username'])): ?>
                <a href="logout.php" style="margin: 0 15px; text-decoration: none; color: #000;">登出</a>
            <?php endif; ?>
        </td>
    </tr>
</table>

<!-- 主要內容 -->
<table width="500" align="center" border="1" cellpadding="20" cellspacing="0" style="background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px;">
    <tr>
        <td>
            <h2 align="center">查詢用電紀錄和計算電費</h2>

            <form method="POST" action="">
                <table width="100%">
                    <tr>
                        <td align="right">輸入用電量（度數）:</td>
                        <td><input type="number" name="usage_kwh" id="usage_kwh" required step="0.01"></td>
                    </tr>
                    <tr>
                        <td align="right">選擇電價類型:</td>
                        <td>
                            <select name="billing_type" id="billing_type" required>
                                <option value="non-time">非時間電價</option>
                                <option value="time">時間電價</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">是否為夏季（6月-9月）:</td>
                        <td><input type="checkbox" name="is_summer" id="is_summer"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <button type="submit">計算電費</button>
                        </td>
                    </tr>
                </table>
            </form>

            <!-- 顯示訊息 -->
            <?php if ($message): ?>
                <div style="text-align: center; margin-top: 20px; color: green;"><?php echo $message; ?></div>
            <?php endif; ?>
			 <div style="text-align: center; margin-top: 20px;">
                <a href="http://25.34.64.227:5000/" target="_blank" style="text-decoration: none; color: #000; font-size: 16px;">及時電表影像</a>
            </div>
        </td>
    </tr>
</table>

</body>
</html>

