<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_shop'])) {
        $shop_name = $_POST['shop_name'];
        $owner_name = $_POST['owner_name'];
        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];

        $sql = "INSERT INTO shops (shop_name, owner_name, phone_number, password) 
                VALUES ('$shop_name', '$owner_name', '$phone_number', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "تم إضافة المحل بنجاح!";
        } else {
            echo "خطأ: " . $conn->error;
        }
    }
}

$result = $conn->query("SELECT * FROM shops");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>إدارة المحلات</title>
</head>
<body>
    <h1>إدارة المحلات</h1>
    <form method="POST">
        <input type="text" name="shop_name" placeholder="اسم المحل" required>
        <input type="text" name="owner_name" placeholder="اسم المالك" required>
        <input type="text" name="phone_number" placeholder="رقم الهاتف" required>
        <input type="password" name="password" placeholder="كلمة السر" required>
        <button type="submit" name="add_shop">إضافة محل</button>
    </form>

    <h2>قائمة المحلات</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?= $row['shop_name'] ?> - <?= $row['phone_number'] ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
