<?php
session_start(); // 啟動 Session

// 假設這裡會有用戶登入的邏輯，並且在成功登入後設定 $_SESSION['username']
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = ''; // 確保 session 變數存在
}

// 定義 API 金鑰和請求網址
$apiKey = '1f40134ace35f95dd086b655533b335c'; // 使用您提供的 API 金鑰
$url = 'https://gnews.io/api/v4/top-headlines?country=tw&token=' . $apiKey;

// 使用 cURL 獲取新聞
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// 解析 JSON 數據
$newsData = json_decode($response, true);
$articles = $newsData['articles'] ?? [];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首頁</title>
    <style>
        /* 整體樣式 */
        body {
            background-image: url('1234.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        /* 導覽列樣式 */
        .navbar {
            background-color: #79FF79;
            text-align: center;
            padding: 10px 0;
            width: 100%;
        }
        .navbar a {
            margin: 0 15px;
            text-decoration: none;
            color: #fff;
            font-size: 16px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        /* 搜尋框樣式 */
        .search-container {
            background-color: #EDF1ED;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            margin-top: 20px;
            text-align: center;
        }
        .search-bar {
            width: 60%;
            height: 40px;
            border: 2px solid #79FF79;
            border-radius: 30px;
            padding-left: 15px;
            font-size: 16px;
            outline: none;
            display: inline-block;
            vertical-align: middle;
        }
        .search-button {
            background-color: #79FF79;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            vertical-align: middle;
            margin-left: 10px;
        }
        .search-button:hover {
            background-color: #66cc66;
        }
        .hot-queries {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
        .hot-queries a {
            color: #333;
            text-decoration: none;
            margin-right: 10px;
        }
        .hot-queries a:hover {
            color: #79FF79;
        }
        /* 新聞容器樣式 */
        .news-container {
            width: 35%; /* 縮小 50% */
            margin-top: 20px;
            position: relative;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .news-item {
            display: none;
            position: relative;
            width: 100%; /* 保持在新聞容器內 */
        }
        .news-item.active {
            display: block;
        }
        .news-item img {
            width: 100%;
            border-radius: 10px;
        }
        /* 標題文字遮罩效果 */
        .news-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            background: rgba(0, 0, 0, 0.6);
            color: #ffffff;
            box-sizing: border-box;
        }
        .news-overlay h3 {
            margin: 0;
            font-size: 18px;
        }
        .news-overlay p {
            font-size: 14px;
            margin-top: 5px;
        }
        /* 左右切換按鈕樣式 */
        .nav-button {
            position: absolute;
            top: 50%;
            width: 30px;
            height: 30px;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            border: none;
            font-size: 18px;
            cursor: pointer;
            transform: translateY(-50%);
            border-radius: 50%;
        }
        .nav-button.left {
            left: 10px;
        }
        .nav-button.right {
            right: 10px;
        }
    </style>
    <script>
        let currentIndex = 0;
        function showNews(index) {
            const items = document.querySelectorAll('.news-item');
            items[currentIndex].classList.remove('active');
            currentIndex = (index + items.length) % items.length;
            items[currentIndex].classList.add('active');
        }
    </script>
</head>
<body>

<!-- 導覽列 -->
<div class="navbar">
    <a href="home.php">首頁</a>
    <a href="electricity.php">查詢用電紀錄和計算電費</a>
    <?php if (empty($_SESSION['username'])): ?>
        <a href="login.php">登入</a>
    <?php else: ?>
        <span>歡迎，<?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <a href="logout.php">登出</a>
    <?php endif; ?>
</div>

<!-- 搜尋框區塊 -->
<div class="search-container">
    <form action="https://www.google.com/search" method="GET" target="_blank">
        <input type="text" name="q" class="search-bar" placeholder="請輸入搜尋關鍵字" required>
        <button type="submit" class="search-button">搜尋</button>
    </form>
    <div class="hot-queries">
        熱門查詢詞：
        <a href="https://www.google.com/search?q=電價表" target="_blank">電價表</a>
        <a href="https://www.google.com/search?q=電子帳單" target="_blank">電子帳單</a>
        <a href="https://www.google.com/search?q=發電量" target="_blank">發電量</a>
        <a href="https://www.google.com/search?q=再生能源" target="_blank">再生能源</a>
        <a href="https://www.google.com/search?q=節電" target="_blank">節電</a>
        <a href="https://www.google.com/search?q=時間電價" target="_blank">時間電價</a>
        <a href="https://www.google.com/search?q=變電所" target="_blank">變電所</a>
        <a href="https://www.google.com/search?q=電動車" target="_blank">電動車</a>
        <a href="https://www.google.com/search?q=儲能" target="_blank">儲能</a>
    </div>
</div>

<!-- 新聞展示區塊 -->
<div class="news-container">
    <button class="nav-button left" onclick="showNews(currentIndex - 1)">&#10094;</button>
    <?php foreach ($articles as $index => $article): ?>
        <div class="news-item <?php echo $index === 0 ? 'active' : ''; ?>">
            <?php if (isset($article['image'])): ?>
                <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="新聞圖片">
            <?php endif; ?>
            <div class="news-overlay">
                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                <p><?php echo htmlspecialchars($article['description']); ?></p>
                <a href="<?php echo htmlspecialchars($article['url']); ?>" target="_blank" style="color: #79FF79;">閱讀更多</a>
            </div>
        </div>
    <?php endforeach; ?>
    <button class="nav-button right" onclick="showNews(currentIndex + 1)">&#10095;</button>
</div>

</body>
</html>
