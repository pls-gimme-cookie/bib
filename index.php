<?php
// Подключение к базе данных
//$mysqli = new mysqli("mysql", "root", "root", "mydb");
$mysqli = new mysqli("postgres.railway.internal", "postgres", "ezYSBIUSpCcnUDLAMJEYQuHRpiEenPbE", "railway");
// Проверка соединения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Создание таблицы, если она не существует
$sql = "CREATE TABLE students (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FIO VARCHAR(255) NOT NULL,
    Number_zach_kn VARCHAR(50) NOT NULL,
    Birthday DATE NOT NULL,
    Postyplenie DATE NOT NULL
)";
$mysqli->query($sql);

// Добавление новой записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $fio = $_POST["FIO"];
    $student_id = $_POST["Number_zach_kn"];
    $dob = $_POST["Birthday"];
    $enrollment_date = $_POST["Postyplenie"];

    // Проверка, что даты не больше текущего дня
    $current_date = date("Y-m-d");
    if ($dob <= $current_date && $enrollment_date <= $current_date) {
        $sql = "INSERT INTO students (FIO, Number_zach_kn, Birthday, Postyplenie)
                VALUES ('$fio', '$student_id', '$dob', '$enrollment_date')";
        $mysqli->query($sql);
    } else {
        echo "Ошибка: Дата рождения и дата поступления не могут быть в будущем.";
    }
}

// Удаление записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM students WHERE ID = $id";
    $mysqli->query($sql);
}

// Фильтрация данных
$filter_query = "SELECT * FROM students WHERE 1=1"; // 1=1 чтобы не было синтаксических ошибок при отсутствии фильтров

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['filter'])) {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $age_start = $_POST["age_start"];
    $age_end = $_POST["age_end"];
    
    $current_year = date("Y");
    
    // Проверяем и добавляем условия для даты поступления
    if (!empty($start_date)) {
        $filter_query .= " AND Postyplenie >= '$start_date'";
    }
    if (!empty($end_date)) {
        $filter_query .= " AND Postyplenie <= '$end_date'";
    }

    // Проверяем и добавляем условия для возраста
    if (!empty($age_start)) {
        $dob_end = date("Y-m-d", strtotime(($current_year - $age_start) . "-12-31"));
        $filter_query .= " AND Birthday <= '$dob_end'";
    }
    if (!empty($age_end)) {
        $dob_start = date("Y-m-d", strtotime(($current_year - $age_end) . "-01-01"));
        $filter_query .= " AND Birthday >= '$dob_start'";
    }
}

// Если нажата кнопка сброса фильтров
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    $filter_query = "SELECT * FROM students"; // Сбрасываем фильтры
}

// Получение данных для отображения
$result = $mysqli->query($filter_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление студентами</title>
</head>
<body>
<h1>Студенты</h1>

<!-- Форма добавления -->
<form method="post">
    <h2>Добавить студента</h2>
    FIO: <input type="text" name="FIO" required><br>
    Номер зачетной книжки: <input type="text" name="Number_zach_kn" required><br>
    Дата рождения: <input type="date" name="Birthday" max="<?= date('Y-m-d') ?>" required><br>
    Дата поступления: <input type="date" name="Postyplenie" max="<?= date('Y-m-d') ?>" required><br>
    <input type="submit" name="add" value="Добавить">
</form>

<!-- Форма удаления -->
<form method="post">
    <h2>Удалить студента</h2>
    ID для удаления: <input type="number" name="id" required><br>
    <input type="submit" name="delete" value="Удалить">
</form>

<!-- Форма фильтрации -->
<form method="post">
    <h2>Фильтрация данных</h2>
    Поступившие после: <input type="date" name="start_date"><br>
    Поступившие до: <input type="date" name="end_date"><br>
    Возраст от: <input type="number" name="age_start"><br>
    Возраст до: <input type="number" name="age_end"><br>
    <input type="submit" name="filter" value="Фильтровать">
    <input type="submit" name="reset" value="Сбросить фильтры">
</form>

<!-- Отображение таблицы -->
<h2>Список студентов</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>FIO</th>
        <th>Номер зачетной книжки</th>
        <th>Дата рождения</th>
        <th>Дата поступления</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["ID"] ?></td>
                <td><?= $row["FIO"] ?></td>
                <td><?= $row["Number_zach_kn"] ?></td>
                <td><?= $row["Birthday"] ?></td>
                <td><?= $row["Postyplenie"] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">Нет данных</td></tr>
    <?php endif; ?>
</table>

</body>
</html>

<?php
$mysqli->close();
?>
