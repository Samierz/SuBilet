<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");

$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT e.title, e.event_date, t.purchase_date, t.id as ticket_id 
                          FROM tickets t
                          JOIN events e ON t.event_id = e.id
                          WHERE t.user_id = ?
                          ORDER BY e.event_date ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Biletlerim</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">İptal Onayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bu bileti iptal etmek istediğinizden emin misiniz?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <a href="#" class="btn btn-danger" id="confirmDelete">İptal Et</a>
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="my_events.php">Oluşturduğum Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="event_list.php">Tüm Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_tickets.php">Biletlerim</a>
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
        <h2 class="mb-4">Biletlerim</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="card-text">Etkinlik Tarihi: <?php echo htmlspecialchars($row['event_date']); ?></p>
                            <p class="card-text">Satın Alım: <?php echo htmlspecialchars($row['purchase_date']); ?></p>                            <button type="button" 
                                    class="btn btn-danger delete-ticket"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal"
                                    data-ticket-id="<?php echo $row['ticket_id']; ?>">
                                Bileti İptal Et
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Henüz bir biletiniz bulunmamaktadır.</div>
        <?php endif; ?>
        
        <a href="event_list.php" class="btn btn-primary">Etkinliklere Dön</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal ve buton elementlerini al
            const deleteModal = document.getElementById('deleteModal');
            const confirmDelete = document.getElementById('confirmDelete');
            
            // Her bir silme butonuna click event listener ekle
            document.querySelectorAll('.delete-ticket').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Tıklanan butondan bilet ID'sini al
                    const ticketId = this.getAttribute('data-ticket-id');
                    // Modal içindeki onay butonunun href'ini güncelle
                    confirmDelete.href = 'delete_ticket.php?id=' + ticketId;
                });
            });
        });
    </script>
</body>
</html>