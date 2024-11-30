<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['delivery_name'])) {
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

// تحديث حالة الطلب
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order_status'])) {
    $id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);  // تصحيح المتغير إلى $id بدلاً من $order_id
    if ($stmt->execute()) {
        header("Location: delivery_dashboard.php");
    } else {
        echo "فشل في تحديث حالة الطلب.";
    }
    $stmt->close();
}

// جلب الطلبات الموكلة لموظف التوصيل مع اسم المحل ورقم هاتفه
$delivery_name = $_SESSION['delivery_name'];
$sql = "
    SELECT o.id AS order_id, o.customer_name, o.customer_phone, o.area, o.price, o.status, 
           s.shop_name, s.phone_number AS shop_phone
    FROM orders o
    LEFT JOIN shops s ON o.shop_id = s.id
    WHERE o.delivery_person_id = (SELECT id FROM delivery_staff WHERE delivery_name = ?) AND o.status = 'pending'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $delivery_name);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة موظف التوصيل</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
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
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        form {
            display: flex;
            flex-direction: column;
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
    </style>
</head>
<body>
    <h1>لوحة موظف التوصيل</h1>
    <div class="container">
        <div class="section">
            <h2>الطلبات الموكلة إليك</h2>
            <table>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم الزبون</th>
                    <th>رقم الهاتف (زبون)</th>
                    <th>المنطقة</th>
                    <th>السعر</th>
                    <th>اسم المحل</th>
                    <th>رقم هاتف المحل</th>
                    <th>الإجراء</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['order_id']; ?></td>
                    <td><?= $row['customer_name']; ?></td>
                    <td><?= $row['customer_phone']; ?></td>
                    <td><?= $row['area']; ?></td>
                    <td><?= $row['price']; ?></td>
                    <td><?= $row['shop_name']; ?></td>
                    <td><?= $row['shop_phone']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                            <select name="status">
                                <option value="delivered">تم التسليم</option>
                                <option value="returned">طلب راجع</option>
                            </select>
                            <button type="submit" name="update_order_status">تحديث</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
