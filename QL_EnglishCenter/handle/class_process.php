<?php
// session_start();
require_once __DIR__ . '/../functions/class_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateClass();
        break;
    case 'edit':
        handleEditClass();
        break;
    case 'delete':
        handleDeleteClass();
        break;
    // default:
    //     header("Location: ../views/class.php?error=Hành động không hợp lệ");
    //     exit();
}
/**
 * Lấy tất cả danh sách lớp
 */
function handleGetAllClasses()
{
    return getAllClasses();
}

function handleGetClassById($id)
{
    return getClassById($id);
}

/**
 * Xử lý tạo lớp mới
 */
function handleCreateClass()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['class_code']) || !isset($_POST['class_name'])) {
        header("Location: ../views/class/create_class.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $class_code = trim($_POST['class_code']);
    $class_name = trim($_POST['class_name']);

    // Validate dữ liệu
    if (empty($class_code) || empty($class_name)) {
        header("Location: ../views/class/create_class.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi hàm thêm lớp
    $result = addClass($class_code, $class_name);

    if ($result) {
        header("Location: ../views/class.php?success=Thêm lớp thành công");
    } else {
        header("Location: ../views/class/create_class.php?error=Có lỗi xảy ra khi thêm lớp");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa lớp
 */
function handleEditClass()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['id']) || !isset($_POST['class_code']) || !isset($_POST['class_name'])) {
        header("Location: ../views/class.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = $_POST['id'];
    $class_code = trim($_POST['class_code']);
    $class_name = trim($_POST['class_name']);

    // Validate dữ liệu
    if (empty($class_code) || empty($class_name)) {
        header("Location: ../views/edit_class.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi function để cập nhật lớp
    $result = updateClass($id, $class_code, $class_name);

    if ($result) {
        header("Location: ../views/class.php?success=Cập nhật lớp thành công");
    } else {
        header("Location: ../views/edit_class.php?id=" . $id . "&error=Cập nhật lớp thất bại");
    }
    exit();
}

/**
 * Xử lý xóa lớp
 */
function handleDeleteClass()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/class.php?error=Không tìm thấy ID lớp");
        exit();
    }

    $id = $_GET['id'];

    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/class.php?error=ID lớp không hợp lệ");
        exit();
    }

    // Gọi function để xóa lớp
    $result = deleteClass($id);

    if ($result) {
        header("Location: ../views/.php?success=Xóa lớp thành công");
    } else {
        header("Location: ../views/class.php?error=Xóa s thất bại");
    }
    exit();
}
?>