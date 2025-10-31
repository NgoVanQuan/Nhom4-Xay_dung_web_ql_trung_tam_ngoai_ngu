<?php
include_once 'db_connection.php';

// Lấy tất cả lịch học (JOIN với khóa học để hiển thị tên khóa)
function getAllSchedules()
{
    $conn = getDbConnection();
    $sql = "SELECT s.id, c.course_name, s.schedule_date, s.start_time, s.end_time, s.location
            FROM schedules s
            JOIN courses c ON s.course_id = c.id
            ORDER BY s.schedule_date DESC";
    $result = $conn->query($sql);

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    $conn->close();
    return $schedules;
}

// Lấy thông tin 1 lịch học theo ID
function getScheduleById($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT s.id, s.course_id, c.course_name, s.schedule_date, s.start_time, s.end_time, s.location
                            FROM schedules s
                            JOIN courses c ON s.course_id = c.id
                            WHERE s.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
    return $schedule;
}

// Thêm lịch học mới
function addSchedule($course_id, $schedule_date, $start_time, $end_time, $location)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO schedules (course_id, schedule_date, start_time, end_time, location)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $course_id, $schedule_date, $start_time, $end_time, $location);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return true;
}

// Cập nhật lịch học
function updateSchedule($id, $course_id, $schedule_date, $start_time, $end_time, $location)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE schedules
                            SET course_id = ?, schedule_date = ?, start_time = ?, end_time = ?, location = ?
                            WHERE id = ?");
    $stmt->bind_param("issssi", $course_id, $schedule_date, $start_time, $end_time, $location, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return true;
}

// Xóa lịch học
function deleteSchedule($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return true;
}
?>