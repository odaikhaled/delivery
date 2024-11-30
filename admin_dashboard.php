<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_name'])) {
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

// إضافة موظف توصيل جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_delivery_staff'])) {
    $delivery_name = $_POST['delivery_name'];
    $delivery_password = $_POST['password'];
    $phone_number = $_POST['phone_number'];

    $sql = "INSERT INTO delivery_staff (delivery_name, password, phone_number) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $delivery_name, $delivery_password, $phone_number);
    if ($stmt->execute()) {
        echo "تم إضافة موظف التوصيل بنجاح.";
    } else {
        echo "فشل في إضافة موظف التوصيل.";
    }
    $stmt->close();
}

// جلب الطلبات الحالية
$current_orders_sql = "SELECT * FROM orders WHERE status = 'pending'";
$current_orders_result = $conn->query($current_orders_sql);

// جلب الطلبات المنجزة
$completed_orders_sql = "SELECT * FROM orders WHERE status = 'delivered'";
$completed_orders_result = $conn->query($completed_orders_sql);

// جلب الطلبات الراجعة
$returned_orders_sql = "SELECT * FROM orders WHERE status = 'returned'";
$returned_orders_result = $conn->query($returned_orders_sql);

// تعيين موظف توصيل لعدة طلبات
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_delivery_person'])) {
    $selected_orders = $_POST['selected_orders'];  // استلام الطلبات المحددة
    $delivery_person_id = $_POST['delivery_person_id']; // استلام موظف التوصيل المحدد

    foreach ($selected_orders as $order_id) {
        $update_sql = "UPDATE orders SET delivery_person_id = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $delivery_person_id, $order_id);
        $stmt->execute();
    }

    
    if ($stmt->execute()) {
        // إعادة توجيه بعد رفع الطلب لتجنب رفعه مرة أخرى عند التحديث
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "فشل تعيين موظف التوصييل.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        form input, form button {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .action-buttons a {
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .action-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>لوحة تحكم موظفي الشركة</h1>
    <div class="container">
        <!-- إضافة موظف توصيل -->
        <div class="section">
            <h2>إضافة موظف توصيل جديد</h2>
            <form method="POST">
                <input type="text" name="delivery_name" placeholder="اسم الموظف" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <input type="text" name="phone_number" placeholder="رقم الهاتف" required>
                <button type="submit" name="add_delivery_staff">إضافة الموظف</button>
            </form>
        </div>

        <!-- عرض الطلبات الحالية -->
        <div class="section">
            <h2>الطلبات الحالية</h2>
            <form method="POST">
                <table>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"> تحديد الكل</th>
                        <th>رقم الطلب</th>
                        <th>اسم الزبون</th>
                        <th>رقم الهاتف</th>
                        <th>المنطقة</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                    </tr>
                    <?php while ($row = $current_orders_result->fetch_assoc()) { ?>
                    <tr>
                        <td><input type="checkbox" name="selected_orders[]" value="<?= $row['id']; ?>"></td>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['customer_name']; ?></td>
                        <td><?= $row['customer_phone']; ?></td>
                        <td><?= $row['area']; ?></td>
                        <td><?= $row['price']; ?></td>
                        <td><?= $row['status']; ?></td>
                    </tr>
                    <?php } ?>
                </table>
                <div class="action-buttons">
                    <button type="submit" name="assign_delivery_person">تعيين موظف توصيل</button>
                    <select name="delivery_person_id" required>
                        <?php
                        $delivery_staff_sql = "SELECT * FROM delivery_staff";
                        $delivery_staff_result = $conn->query($delivery_staff_sql);
                        while ($staff = $delivery_staff_result->fetch_assoc()) {
                            echo "<option value='" . $staff['id'] . "'>" . $staff['delivery_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>

        <!-- عرض الطلبات المنجزة -->
        <div class="section">
            <h2>الطلبات المنجزة</h2>
            <table>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم الزبون</th>
                    <th>رقم الهاتف</th>
                    <th>المنطقة</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                </tr>
                <?php while ($row = $completed_orders_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['customer_name']; ?></td>
                    <td><?= $row['customer_phone']; ?></td>
                    <td><?= $row['area']; ?></td>
                    <td><?= $row['price']; ?></td>
                    <td><?= $row['status']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- عرض الطلبات الراجعة -->
        <div class="section">
            <h2>الطلبات الراجعة</h2>
            <table>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم الزبون</th>
                    <th>رقم الهاتف</th>
                    <th>المنطقة</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                </tr>
                <?php while ($row = $returned_orders_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['customer_name']; ?></td>
                    <td><?= $row['customer_phone']; ?></td>
                    <td><?= $row['area']; ?></td>
                    <td><?= $row['price']; ?></td>
                    <td><?= $row['status']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script>
        // تحديد الكل أو إلغاء التحديد
        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const selectAll = document.getElementById('selectAll');
            checkboxes.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
