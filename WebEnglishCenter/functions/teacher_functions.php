<?php
include_once 'db_connection.php';

// Lấy danh sách tất cả giảng viên (có join với bảng users)
function getAllTeachers()
{
    $conn = getDbConnection();
    $sql = "SELECT t.id, u.full_name, u.email, t.phone, t.specialization
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            ORDER BY t.id DESC";
    $result = $conn->query($sql);
    $teachers = [];

    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
    $conn->close();
    return $teachers;
}

// Lấy thông tin chi tiết 1 giảng viên theo ID
function getTeacherById($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT t.id, u.full_name, u.email, t.phone, t.specialization 
                            FROM teachers t 
                            JOIN users u ON t.user_id = u.id 
                            WHERE t.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $teacher;
}

// Thêm giảng viên mới
function addTeacher($full_name, $email, $phone, $specialization, $password)
{
    $conn = getDbConnection();

    // Thêm vào bảng users
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'teacher')");
    $stmt->bind_param("sss", $full_name, $email, $hashedPassword);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    // Thêm vào bảng teachers
    $stmt2 = $conn->prepare("INSERT INTO teachers (user_id, phone, specialization) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $user_id, $phone, $specialization);
    $stmt2->execute();
    $stmt2->close();

    $conn->close();
    return true;
}

// Cập nhật thông tin giảng viên
function updateTeacher($id, $full_name, $email, $phone, $specialization)
{
    $conn = getDbConnection();

    // Cập nhật bảng users
    $sql_user = "UPDATE users 
                 JOIN teachers ON users.id = teachers.user_id 
                 SET users.full_name = ?, users.email = ? 
                 WHERE teachers.id = ?";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("ssi", $full_name, $email, $id);
    $stmt->execute();
    $stmt->close();

    // Cập nhật bảng teachers
    $sql_teacher = "UPDATE teachers SET phone = ?, specialization = ? WHERE id = ?";
    $stmt2 = $conn->prepare($sql_teacher);
    $stmt2->bind_param("ssi", $phone, $specialization, $id);
    $stmt2->execute();
    $stmt2->close();

    $conn->close();
    return true;
}

// Xóa giảng viên (và user tương ứng)
function deleteTeacher($id)
{
    $conn = getDbConnection();

    // Lấy user_id
    $stmt = $conn->prepare("SELECT user_id FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Xóa user trước (tránh lỗi khóa ngoại)
        $stmt3 = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
        $stmt3->close();
    }

    // Xóa teacher
    $stmt2 = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    $conn->close();
    return true;
}
?>