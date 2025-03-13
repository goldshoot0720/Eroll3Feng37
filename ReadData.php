<?php
// 顯示錯誤訊息
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 資料庫連接資訊
$servername = "127.0.0.1";
$username = "root"; // 根據你的資料庫設定
$password = ""; // 根據你的資料庫設定
$dbname = "feng37enroll3";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die(json_encode(["error" => "連線失敗：" . $conn->connect_error]));
}

$examYear = isset($_GET['examYear']) ? $_GET['examYear'] : '';
$examLevel = isset($_GET['examLevel']) ? $_GET['examLevel'] : '';
$jobSystem = isset($_GET['jobSystem']) ? $_GET['jobSystem'] : '';
$jobCategory = isset($_GET['jobCategory']) ? $_GET['jobCategory'] : '';
$column = isset($_GET['column']) ? $_GET['column'] : '';

// 查詢符合條件的資料
if ($column) {
    // 查詢下拉選單資料
    $stmt = $conn->prepare("SELECT DISTINCT `$column` FROM `enroll3data`");
    if ($stmt === false) {
        die(json_encode(["error" => "準備查詢失敗：" . $conn->error]));
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row[$column];
    }

    // 回傳資料
    echo json_encode($data);
} elseif ($examYear && $examLevel && $jobSystem && $jobCategory) {
    // 查詢根據條件資料
    $stmt = $conn->prepare("SELECT `enroll3name`, `enroll3total` FROM `enroll3data` WHERE `enroll3year` = ? AND `enroll3level` = ? AND `enroll3grade` = ? AND `enroll3class` = ?");
    if ($stmt === false) {
        die(json_encode(["error" => "準備查詢失敗：" . $conn->error]));
    }

    $stmt->bind_param("ssss", $examYear, $examLevel, $jobSystem, $jobCategory);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "enroll3name" => $row['enroll3name'],
                "enroll3total" => $row['enroll3total']
            ];
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "沒有符合條件的資料"]);
    }
} else {
    echo json_encode(["error" => "請提供所有查詢條件"]);
}

// 關閉資料庫連接
$conn->close();
?>
