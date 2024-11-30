<?php
session_start();

// الاتصال بقاعدة البيانات
include 'db.php';

// التحقق من البيانات المُدخلة
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delivery_name = $_POST['delivery_name'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM delivery_staff WHERE delivery_name = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $delivery_name, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['delivery_name'] = $delivery_name;
        header("Location: delivery_dashboard.php"); // الانتقال إلى لوحة موظف التوصيل
        exit();
    } else {
        echo "بيانات تسجيل الدخول غير صحيحة!";
    }

    $stmt->close();
}

$conn->close();
?>
