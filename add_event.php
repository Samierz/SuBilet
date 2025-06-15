<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $loc = $_POST['location'];
    $date = $_POST['event_date'];
    $tickets = (int)$_POST['total_tickets'];
    $user_id = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("INSERT INTO events (title, location, event_date, total_tickets, available_tickets, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssiii", $title, $loc, $date, $tickets, $tickets, $user_id);
    
    if ($stmt->execute()) {
        header("Location: event_list.php");
        exit();
    } else {
        $error = "Etkinlik eklenirken bir hata oluştu!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Yeni Etkinlik Ekle</title>
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
            </button>            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="my_events.php">Oluşturduğum Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="event_list.php">Tüm Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_tickets.php">Biletlerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_event.php">Yeni Etkinlik</a>
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
                        <h2 class="card-title mb-4">Yeni Etkinlik Ekle</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Etkinlik Adı:</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konum:</label>
                                <input type="text" name="location" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tarih:</label>
                                <input type="date" name="event_date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Toplam Bilet Sayısı:</label>
                                <input type="number" name="total_tickets" class="form-control" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Etkinlik Ekle</button>
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