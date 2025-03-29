<?php
$servername = "sql301.infinityfree.com";
$username = "if0_38435166";
$password = "gf0Tagood129";
$dbname = "if0_38435166_feng37enroll3";

// 建立資料庫連線
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 確保請求方式為 POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得輸入資料並分行處理
    $data = $_POST["data"] ?? '';
    
    // 檢查是否有資料
    if (empty($data)) {
        die("錯誤：請填寫資料！");
    }

    // 將資料按行分割
    $lines = explode("\n", $data);

    // 檢查每行資料的格式，過濾空行
    $lines = array_filter($lines, function($line) {
        return !empty(trim($line));
    });

    // 預備 SQL 語句（修改後的欄位名稱）
    $stmt = $conn->prepare("INSERT INTO enroll3data (enroll3year, enroll3level, enroll3grade, enroll3class, enroll3name, enroll3now, enroll3notnow1, enroll3notnow2, enroll3notnow3, enroll3total) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("SQL 準備失敗：" . $conn->error);
    }

    // 記錄是否有錯誤
    $errorOccurred = false;
    $lineNumber = 1; // 記錄行數

    // 逐行處理資料
    foreach ($lines as $line) {
        // 使用空格分割每行的資料
        $fields = preg_split('/\s+/', trim($line));
        
        // 檢查每行資料是否有足夠的欄位
        if (count($fields) != 10) {
            echo "錯誤：第 $lineNumber 行資料必須包含 10 個欄位！<br>";
            $errorOccurred = true;
            break; // 出現錯誤後停止處理資料
        }

        // 取得每個欄位的資料
        $考試年度 = intval($fields[0]);

        // 檢查民國年份是否在 93 至 116 年之間
        if ($考試年度 < 93 || $考試年度 > 116) {
            echo "錯誤：第 $lineNumber 行的考試年度必須在民國93年至116年之間！<br>";
            $errorOccurred = true;
            break;
        }

        $考試等級 = $fields[1];
        $職系 = $fields[2];
        $類科 = $fields[3];
        $用人機關名稱 = $fields[4];
        $現缺 = intval($fields[5]);
        $非現缺一 = intval($fields[6]);
        $非現缺二 = intval($fields[7]);
        $非現缺三 = intval($fields[8]);
        $合計 = intval($fields[9]);

        // 綁定參數（對應修改後的欄位名稱）
        $stmt->bind_param("ssssssssii", 
            $考試年度, 
            $考試等級, 
            $職系, 
            $類科, 
            $用人機關名稱, 
            $現缺, 
            $非現缺一, 
            $非現缺二, 
            $非現缺三, 
            $合計
        );

        // 執行 SQL
        if (!$stmt->execute()) {
            echo "第 $lineNumber 行資料新增失敗：" . $stmt->error . "<br>";
            $errorOccurred = true;
            break;
        }

        $lineNumber++;
    }

    // 若無錯誤，顯示資料新增成功並跳轉
    if (!$errorOccurred) {
        echo "資料新增成功！正在跳轉...<br>";
        // 顯示倒數計時
        echo "<script>
                var countdown = 5;
                function updateCountdown() {
                    if (countdown > 0) {
                        document.getElementById('countdown').innerText = countdown;
                        countdown--;
                    } else {
                        window.location.href = 'index.html';
                    }
                }
                setInterval(updateCountdown, 1000);
              </script>";
        echo "您將在 <span id='countdown'>5</span> 秒後跳轉。<br>";
    } else {
        // 若有錯誤，顯示錯誤並倒數回上一頁
        echo "<script>
                var countdown = 5;
                function updateCountdown() {
                    if (countdown > 0) {
                        document.getElementById('countdown').innerText = countdown;
                        countdown--;
                    } else {
                        window.history.back();
                    }
                }
                setInterval(updateCountdown, 1000);
              </script>";
        echo "錯誤：資料格式不正確。您將在 <span id='countdown'>5</span> 秒後返回上一頁。<br>";
    }

    // 關閉連線
    $stmt->close();
    $conn->close();
}
?>
