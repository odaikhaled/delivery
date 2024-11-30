<?php
session_start();

// الاتصال بقاعدة البيانات
// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delivery";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التحقق من البيانات المُدخلة
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = $_POST['admin_name'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM company_staff WHERE admin_name = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $admin_name, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_name'] = $admin_name;
        header("Location: admin_dashboard.php"); // الانتقال إلى لوحة الموظف الإداري
        exit();
    } else {
        echo "بيانات تسجيل الدخول غير صحيحة!";
    }

    $stmt->close();
}

$conn->close();
?>
