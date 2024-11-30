<?php
session_start();

// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delivery";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التحقق من صحة تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = $_POST['shop_name'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM shops WHERE shop_name = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $shop_name, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // تسجيل الدخول ناجح، تخزين معلومات المحل في الجلسة
        $row = $result->fetch_assoc();
        $_SESSION['shop_name'] = $row['shop_name'];
        $_SESSION['shop_id'] = $row['id']; // تخزين shop_id في الجلسة

        header("Location: shop_dashboard.php"); // توجيه المحل إلى لوحة التحكم
        exit();
    } else {
        echo "بيانات تسجيل الدخول غير صحيحة.";
    }
}
?>
