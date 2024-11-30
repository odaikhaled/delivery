<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['shop_name'])) {
    header("Location: index.html");
    exit();
}

// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delivery";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// رفع طلب جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_order'])) {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $area = $_POST['area'];
    $price = $_POST['price'];
    $shop_id = $_SESSION['shop_id'];

    $sql = "INSERT INTO orders (customer_name, customer_phone, area, price, shop_id, status) VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $customer_name, $customer_phone, $area, $price, $shop_id);

    if ($stmt->execute()) {
        // إعادة توجيه بعد رفع الطلب لتجنب رفعه مرة أخرى عند التحديث
        header("Location: shop_dashboard.php");
        exit();
    } else {
        echo "فشل في رفع الطلب.";
    }
    $stmt->close();
}

// جلب الطلبات للمحل الحالي
$shop_id = $_SESSION['shop_id'];
$sql = "SELECT o.id, o.customer_name, o.customer_phone, o.area, o.price, o.status, o.delivery_person_id, ds.delivery_name AS delivery_name, ds.phone_number AS phone_number
        FROM orders o
        LEFT JOIN delivery_staff ds ON o.delivery_person_id = ds.id
        WHERE o.shop_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة المحل</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            margin: 20px auto;
        }

        .section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        form input, form button {
            margin: 5px 0;
            padding: 10px;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
        }

        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <h1>لوحة المحل</h1>
    <div class="container">
        <div class="section">
            <h2>رفع طلب جديد</h2>
            <form method="POST">
                <input type="text" name="customer_name" placeholder="اسم الزبون" required>
                <input type="text" name="customer_phone" placeholder="رقم الهاتف" required>
                <input type="text" name="area" placeholder="المنطقة" required>
                <input type="number" name="price" placeholder="السعر" required>
                <button type="submit" name="add_order">رفع الطلب</button>
            </form>
        </div>

        <div class="section">
            <h2>الطلبات الحالية</h2>
            <table>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم الزبون</th>
                    <th>رقم الهاتف</th>
                    <th>المنطقة</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>موظف التوصيل</th>
                    <th>رقم هاتف الموظف</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['customer_name']; ?></td>
                    <td><?= $row['customer_phone']; ?></td>
                    <td><?= $row['area']; ?></td>
                    <td><?= $row['price']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td><?= !empty($row['delivery_name']) ? $row['delivery_name'] : 'غير معين'; ?></td>
                    <td><?= !empty($row['phone_number']) ? $row['phone_number'] : 'غير معين'; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
