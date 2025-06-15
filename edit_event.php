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
    $_SESSION['error_message'] = "Öne çıkan etkinlikler düzenlenemez!";
    header("Location: event_list.php");
    exit();
}

// Sonra etkinliğin bu kullanıcıya ait olup olmadığını kontrol et
$stmt = $mysqli->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: event_list.php");
    exit();
}

$event = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];
    $total_tickets = (int)$_POST['total_tickets'];

    // Mevcut bilet sayısını kontrol et
    $stmt = $mysqli->prepare("SELECT total_tickets - available_tickets as sold_tickets FROM events WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $sold_tickets = $row['sold_tickets'];
    
    if ($total_tickets < $sold_tickets) {
        $error = "Satılmış bilet sayısından daha az toplam bilet sayısı belirleyemezsiniz!";
    } else {
        $available_tickets = $total_tickets - $sold_tickets;
        
        $stmt = $mysqli->prepare("UPDATE events SET title=?, location=?, event_date=?, total_tickets=?, available_tickets=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sssiiii", $title, $location, $event_date, $total_tickets, $available_tickets, $id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            header("Location: event_list.php");
            exit();
        } else {
            $error = "Güncelleme sırasında bir hata oluştu!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Etkinlik Düzenle</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">ŞuBilet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="my_events.php">Oluşturduğum Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="event_list.php">Tüm Etkinlikler</a>
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
                        <h2 class="card-title mb-4">Etkinlik Düzenle</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id']); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Başlık:</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?php echo htmlspecialchars($event['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konum:</label>
                                <input type="text" name="location" class="form-control" 
                                       value="<?php echo htmlspecialchars($event['location']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tarih:</label>
                                <input type="date" name="event_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Toplam Bilet:</label>
                                <input type="number" name="total_tickets" class="form-control" 
                                       value="<?php echo htmlspecialchars($event['total_tickets']); ?>" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Güncelle</button>
                                <a href="event_list.php" class="btn btn-secondary">Geri Dön</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>