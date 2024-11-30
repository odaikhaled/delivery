<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_delivery'])) {
        $delivery_name = $_POST['delivery_name'];
        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];

        $sql = "INSERT INTO delivery_staff (delivery_name, phone_number, password) 
                VALUES ('$delivery_name', '$phone_number', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "تم إضافة موظف التوصيل بنجاح!";
        } else {
            echo "خطأ: " . $conn->error;
        }
    }
}

$result = $conn->query("SELECT * FROM delivery_staff");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>إدارة موظفي التوصيل</title>
</head>
<body>
    <h1>إدارة موظفي التوصيل</h1>
    <form method="POST">
        <input type="text" name="delivery_name" placeholder="اسم الموظف" required>
        <input type="text" name="phone_number" placeholder="رقم الهاتف" required>
        <input type="password" name="password" placeholder="كلمة السر" required>
        <button type="submit" name="add_delivery">إضافة موظف</button>
    </form>

    <h2>قائمة موظفي التوصيل</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?= $row['delivery_name'] ?> - <?= $row['phone_number'] ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
