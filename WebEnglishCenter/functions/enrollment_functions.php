<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Lấy toàn bộ danh sách ghi danh (enrollments) kèm thông tin sinh viên & khóa học
 * @return array Danh sách ghi danh
 */
function getAllEnrollments()
{
    $conn = getDbConnection();

    $sql = "SELECT e.id, e.student_id, e.course_id, e.enrollment_date, e.status,
                   s.student_code, s.student_name,
                   c.course_code, c.course_name
            FROM enrollments e
            LEFT JOIN students s ON e.student_id = s.id
            LEFT JOIN courses c ON e.course_id = c.id
            ORDER BY e.id DESC";

    $result = mysqli_query($conn, $sql);

    $enrollments = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $enrollments[] = $row;
        }
    }

    mysqli_close($conn);
    return $enrollments;
}

/**
 * Lấy thông tin ghi danh theo ID
 * @param int $id ID ghi danh
 * @return array|null
 */
function getEnrollmentById($id)
{
    $conn = getDbConnection();

    $sql = "SELECT e.id, e.student_id, e.course_id, e.enrollment_date, e.status,
                   s.student_code, s.student_name,
                   c.course_code, c.course_name
            FROM enrollments e
            LEFT JOIN students s ON e.student_id = s.id
            LEFT JOIN courses c ON e.course_id = c.id
            WHERE e.id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $data ?: null;
    }

    mysqli_close($conn);
    return null;
}

/**
 * Thêm mới ghi danh
 */
function addEnrollment($student_id, $course_id, $enrollment_date, $status = 'active')
{
    $conn = getDbConnection();

    $sql = "INSERT INTO enrollments (student_id, course_id, enrollment_date, status)
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $student_id, $course_id, $enrollment_date, $status);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $success;
}

/**
 * Cập nhật ghi danh
 */
function updateEnrollment($id, $student_id, $course_id, $enrollment_date, $status)
{
    $conn = getDbConnection();

    $sql = "UPDATE enrollments
            SET student_id = ?, course_id = ?, enrollment_date = ?, status = ?
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissi", $student_id, $course_id, $enrollment_date, $status, $id);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $success;
}

/**
 * Xóa ghi danh
 */
function deleteEnrollment($id)
{
    $conn = getDbConnection();

    $sql = "DELETE FROM enrollments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $success;
}

/**
 * Kiểm tra sinh viên đã ghi danh vào khóa học chưa
 */
function checkEnrollmentExists($student_id, $course_id)
{
    $conn = getDbConnection();

    $sql = "SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $student_id, $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $exists;
}

/**
 * Lấy danh sách tất cả sinh viên (để hiển thị dropdown)
 */
function getAllStudentsForDropdown()
{
    $conn = getDbConnection();
    $sql = "SELECT id, student_code, student_name FROM students ORDER BY student_name";
    $result = mysqli_query($conn, $sql);

    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    mysqli_close($conn);
    return $students;
}

/**
 * Lấy danh sách tất cả khóa học (để hiển thị dropdown)
 */
function getAllCoursesForDropdown()
{
    $conn = getDbConnection();
    $sql = "SELECT id, course_code, course_name FROM courses ORDER BY course_name";
    $result = mysqli_query($conn, $sql);

    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    mysqli_close($conn);
    return $courses;
}
?>