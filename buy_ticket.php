<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$messageType = "";
$event = null;

if (isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    
    // Etkinlik bilgilerini al
    $stmt = $mysqli->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    
    if (!$event) {
        $message = "Etkinlik bulunamadı!";
        $messageType = "danger";
    } elseif ($event['available_tickets'] <= 0) {
        $message = "Üzgünüz, tüm biletler tükenmiştir!";
        $messageType = "warning";
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Transaction başlat
        $mysqli->begin_transaction();
        
        try {
            // Bilet al
            $stmt = $mysqli->prepare("INSERT INTO tickets (user_id, event_id, purchase_date) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $_SESSION['user_id'], $event_id);
            $stmt->execute();
            
            // Available tickets sayısını azalt
            $stmt = $mysqli->prepare("UPDATE events SET available_tickets = available_tickets - 1 WHERE id = ? AND available_tickets > 0");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $mysqli->commit();
                $message = "Bilet başarıyla alındı!";
                $messageType = "success";
            } else {
                throw new Exception("Bilet alınamadı!");
            }
            
        } catch (Exception $e) {
            $mysqli->rollback();
            $message = "Bilet alınırken bir hata oluştu!";
            $messageType = "danger";
        }
    }
} else {
    $message = "Etkinlik ID'si belirtilmedi!";
    $messageType = "danger";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bilet Al</title>
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
                        <h2 class="card-title mb-4">Bilet Al</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($event && $event['available_tickets'] > 0 && $messageType != "success"): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="card-text">Konum: <?php echo htmlspecialchars($event['location']); ?></p>
                                    <p class="card-text">Tarih: <?php echo htmlspecialchars($event['event_date']); ?></p>
                                    <p class="card-text">Kalan Bilet: <?php echo htmlspecialchars($event['available_tickets']); ?></p>
                                    
                                    <form method="post" class="mt-3">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Bileti Onayla</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <a href="event_list.php" class="btn btn-secondary">Etkinliklere Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>