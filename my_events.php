<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kullanıcının kendi etkinliklerini çek (öne çıkan etkinlikler hariç)
$stmt = $mysqli->prepare("SELECT * FROM events WHERE user_id = ? AND title NOT IN ('ATI242 Konseri', 'Cem Yılmaz Stand-up', 'Serdar Ortaç Konseri') ORDER BY event_date ASC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Oluşturduğum Etkinlikler</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .event-card {
            transition: transform 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Silme Onayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bu etkinliği silmek istediğinizden emin misiniz?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <a href="#" class="btn btn-danger" id="confirmDelete">Sil</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">ŞuBilet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">                <ul class="navbar-nav ms-auto">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Oluşturduğum Etkinlikler</h2>
            <a href="add_event.php" class="btn btn-primary">Yeni Etkinlik Ekle</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($event = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100 shadow">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="event-info">
                                <p class="card-text">
                                    <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($event['location']); ?><br>
                                    <i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($event['event_date']); ?><br>
                                    <i class="bi bi-ticket-perforated"></i> Kalan Bilet: <?php echo htmlspecialchars($event['available_tickets']); ?>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-warning flex-grow-1">Düzenle</a>
                                <a href="#" 
                                   class="btn btn-danger flex-grow-1 delete-event"
                                   data-bs-toggle="modal" data-bs-target="#deleteModal"
                                   data-id="<?php echo $event['id']; ?>">Sil</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Henüz oluşturduğunuz bir etkinlik bulunmamaktadır.
                <a href="add_event.php" class="alert-link">Hemen yeni bir etkinlik oluşturun!</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal işleyicisi
        const deleteModal = document.getElementById('deleteModal');
        const confirmDelete = document.getElementById('confirmDelete');

        // Silme butonlarına tıklandığında
        document.querySelectorAll('.delete-event').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const eventId = e.currentTarget.getAttribute('data-id');
                confirmDelete.setAttribute('href', 'delete_event.php?id=' + eventId);
            });
        });
    </script>
</body>
</html>
