<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách điểm (grades) từ database
 * kèm thông tin sinh viên, khóa học và giáo viên
 * @return array Danh sách điểm
 */
function getAllGrades()
{
    $conn = getDbConnection();

    $sql = "
        SELECT 
            g.id,
            g.student_id,
            g.course_id,
            g.teacher_id,
            g.score,
            g.grade_letter,
            g.notes,
            s.full_name AS student_name,
            c.course_name,
            t.full_name AS teacher_name
        FROM grades g
        LEFT JOIN students s ON g.student_id = s.id
        LEFT JOIN courses c ON g.course_id = c.id
        LEFT JOIN teachers t ON g.teacher_id = t.id
        ORDER BY g.id
    ";

    $result = mysqli_query($conn, $sql);
    $grades = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $grades[] = $row;
        }
    }

    mysqli_close($conn);
    return $grades;
}

/**
 * Thêm điểm mới
 * @param int $student_id
 * @param int $course_id
 * @param int|null $teacher_id
 * @param float $score
 * @param string $grade_letter
 * @param string|null $notes
 * @return bool
 */
function addGrade($student_id, $course_id, $teacher_id, $score, $grade_letter, $notes = null)
{
    $conn = getDbConnection();

    $sql = "
        INSERT INTO grades (student_id, course_id, teacher_id, score, grade_letter, notes)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiidss", $student_id, $course_id, $teacher_id, $score, $grade_letter, $notes);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin điểm theo ID
 * @param int $id
 * @return array|null
 */
function getGradeById($id)
{
    $conn = getDbConnection();

    $sql = "
        SELECT 
            g.id,
            g.student_id,
            g.course_id,
            g.teacher_id,
            g.score,
            g.grade_letter,
            g.notes,
            s.full_name AS student_name,
            c.course_name,
            t.full_name AS teacher_name
        FROM grades g
        LEFT JOIN students s ON g.student_id = s.id
        LEFT JOIN courses c ON g.course_id = c.id
        LEFT JOIN teachers t ON g.teacher_id = t.id
        WHERE g.id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $grade = null;
        if ($result && mysqli_num_rows($result) > 0) {
            $grade = mysqli_fetch_assoc($result);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $grade;
    }

    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật điểm
 * @param int $id
 * @param int $student_id
 * @param int $course_id
 * @param int|null $teacher_id
 * @param float $score
 * @param string $grade_letter
 * @param string|null $notes
 * @return bool
 */
function updateGrade($id, $student_id, $course_id, $teacher_id, $score, $grade_letter, $notes = null)
{
    $conn = getDbConnection();

    $sql = "
        UPDATE grades 
        SET student_id = ?, course_id = ?, teacher_id = ?, score = ?, grade_letter = ?, notes = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiidssi", $student_id, $course_id, $teacher_id, $score, $grade_letter, $notes, $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Xóa điểm theo ID
 * @param int $id
 * @return bool
 */
function deleteGrade($id)
{
    $conn = getDbConnection();

    $sql = "DELETE FROM grades WHERE id = ?";
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

/**
 * Kiểm tra xem sinh viên đã có điểm trong khóa học chưa
 * @param int $student_id
 * @param int $course_id
 * @return bool
 */
function checkGradeExists($student_id, $course_id)
{
    $conn = getDbConnection();

    $sql = "SELECT id FROM grades WHERE student_id = ? AND course_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $student_id, $course_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $exists = mysqli_num_rows($result) > 0;

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $exists;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy danh sách sinh viên cho dropdown
 * @return array
 */
function getAllStudentsForDropdown()
{
    $conn = getDbConnection();

    $sql = "SELECT id, full_name FROM students ORDER BY full_name";
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
 * Lấy danh sách khóa học cho dropdown
 * @return array
 */
function getAllCoursesForDropdown()
{
    $conn = getDbConnection();

    $sql = "SELECT id, course_name FROM courses ORDER BY course_name";
    $result = mysqli_query($conn, $sql);

    $courses = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row;
        }
    }

    mysqli_close($conn);
    return $courses;
}

/**
 * Lấy danh sách giáo viên cho dropdown
 * @return array
 */
function getAllTeachersForDropdown()
{
    $conn = getDbConnection();

    $sql = "SELECT id, full_name FROM teachers ORDER BY full_name";
    $result = mysqli_query($conn, $sql);

    $teachers = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $teachers[] = $row;
        }
    }

    mysqli_close($conn);
    return $teachers;
}
?>