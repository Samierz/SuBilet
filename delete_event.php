<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Önce etkinliğin öne çıkan etkinlik olup olmadığını kontrol et
$stmt = $mysqli->prepare("SELECT title FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (in_array($event['title'], ['ATI242 Konseri', 'Cem Yılmaz Stand-up', 'Serdar Ortaç Konseri'])) {
    $_SESSION['error_message'] = "Öne çıkan etkinlikler silinemez!";
    header("Location: event_list.php");
    exit();
}

// Önce etkinliğe ait tüm biletleri sil
$stmt = $mysqli->prepare("DELETE FROM tickets WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Etkinliği sil
$stmt = $mysqli->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Etkinlik başarıyla silindi.";
    header("Location: event_list.php");
    exit();
} else {
    $_SESSION['error_message'] = "Etkinlik silinirken bir hata oluştu!";
    header("Location: event_list.php");
    exit();
}
?>