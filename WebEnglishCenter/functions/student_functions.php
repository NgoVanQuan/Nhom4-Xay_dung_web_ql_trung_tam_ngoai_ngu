<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách students từ database
 * @return array Danh sách students
 */
function getAllStudents()
{
    $conn = getDbConnection();

    // Truy vấn lấy tất cả học viên
    $sql = "SELECT id, full_name, dob, email, phone FROM students ORDER BY id";
    $result = mysqli_query($conn, $sql);

    $students = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }

    mysqli_close($conn);
    return $students;
}

/**
 * Thêm học viên mới
 * @param string $full_name Họ và tên
 * @param string $dob Ngày sinh
 * @param string $email Email
 * @param string $phone Số điện thoại
 * @return bool True nếu thành công, False nếu thất bại
 */
function addStudent($full_name, $dob, $email, $phone)
{
    $conn = getDbConnection();

    $sql = "INSERT INTO students (full_name, dob, email, phone) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $dob, $email, $phone);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin học viên theo ID
 * @param int $id ID của học viên
 * @return array|null Thông tin học viên hoặc null nếu không tìm thấy
 */
function getStudentById($id)
{
    $conn = getDbConnection();

    $sql = "SELECT id, full_name, dob, email, phone FROM students WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $student;
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin học viên
 * @param int $id ID học viên
 * @param string $full_name Họ và tên
 * @param string $dob Ngày sinh
 * @param string $email Email
 * @param string $phone Số điện thoại
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateStudent($id, $full_name, $dob, $email, $phone)
{
    $conn = getDbConnection();

    $sql = "UPDATE students SET full_name = ?, dob = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $dob, $email, $phone, $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Xóa học viên theo ID
 * @param int $id ID học viên cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteStudent($id)
{
    $conn = getDbConnection();

    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}
?>