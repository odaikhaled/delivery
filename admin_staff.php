<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_staff'])) {
        $admin_name = $_POST['admin_name'];
        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];

        $sql = "INSERT INTO company_staff (admin_name, phone_number, password) 
                VALUES ('$admin_name', '$phone_number', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "تم إضافة موظف الشركة بنجاح!";
        } else {
            echo "خطأ: " . $conn->error;
        }
    }
}

$result = $conn->query("SELECT * FROM company_staff");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>إدارة موظفي الشركة</title>
</head>
<body>
    <h1>إدارة موظفي الشركة</h1>
    <form method="POST">
        <input type="text" name="admin_name" placeholder="اسم الموظف" required>
        <input type="text" name="phone_number" placeholder="رقم الهاتف" required>
        <input type="password" name="password" placeholder="كلمة السر" required>
        <button type="submit" name="add_staff">إضافة موظف</button>
    </form>

    <h2>قائمة موظفي الشركة</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?= $row['admin_name'] ?> - <?= $row['phone_number'] ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
