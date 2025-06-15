<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$messageType = "";

if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    
    // Önce biletin event_id'sini ve kullanıcıya ait olup olmadığını kontrol et
    $stmt = $mysqli->prepare("SELECT event_id, user_id FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $message = "Bilet bulunamadı!";
        $messageType = "danger";
    } else {
        $ticket = $result->fetch_assoc();
        
        if ($ticket['user_id'] != $_SESSION['user_id']) {
            $message = "Bu bilet size ait değil!";
            $messageType = "danger";
        } else {
            $event_id = $ticket['event_id'];
            
            // Transaction başlat
            $mysqli->begin_transaction();
            
            try {
                // Bileti sil
                $stmt = $mysqli->prepare("DELETE FROM tickets WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $ticket_id, $_SESSION['user_id']);
                $stmt->execute();
                
                // Available tickets sayısını arttır
                $stmt = $mysqli->prepare("UPDATE events SET available_tickets = available_tickets + 1 WHERE id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                
                // Transaction'ı tamamla
                $mysqli->commit();
                
                $message = "Bilet başarıyla iptal edildi!";
                $messageType = "success";
                
            } catch (Exception $e) {
                // Hata durumunda rollback yap
                $mysqli->rollback();
                $message = "Bilet iptal edilirken bir hata oluştu!";
                $messageType = "danger";
            }
        }
    }
} else {
    $message = "Bilet ID'si belirtilmedi!";
    $messageType = "danger";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bilet İptal</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">ŞuBilet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="event_list.php">Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_tickets.php">Biletlerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_event.php">Yeni Etkinlik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Bilet İptal</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-3">
                            <a href="my_tickets.php" class="btn btn-primary">Biletlerime Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>