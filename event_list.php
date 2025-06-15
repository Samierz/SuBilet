<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");

// Öne çıkan etkinlikleri çek
$featured_titles = ['ATI242 Konseri', 'Cem Yılmaz Stand-up', 'Serdar Ortaç Konseri'];
$sql = "SELECT * FROM events WHERE title IN ('ATI242 Konseri', 'Cem Yılmaz Stand-up', 'Serdar Ortaç Konseri') ORDER BY event_date ASC";
$featured_result = $mysqli->query($sql);

// Diğer etkinlikleri çek
$sql = "SELECT e.*, CASE WHEN e.user_id = ? THEN true ELSE false END as can_edit 
        FROM events e 
        WHERE title NOT IN ('ATI242 Konseri', 'Cem Yılmaz Stand-up', 'Serdar Ortaç Konseri') 
        ORDER BY event_date ASC";
$stmt = $mysqli->prepare($sql);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$stmt->bind_param("i", $user_id);
$stmt->execute();
$other_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tüm Etkinlikler</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-brand i {
            color: #ffc107;
            margin-right: 5px;
            font-size: 1.5em;
            vertical-align: middle;
        }
        .navbar-brand {
            font-size: 1.5em;
            font-weight: 600;
        }
    </style>
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
            <a class="navbar-brand" href="index.php"><i class="bi bi-ticket-perforated-fill"></i>ŞuBilet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_events.php">Oluşturduğum Etkinlikler</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="event_list.php">Tüm Etkinlikler</a>
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
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Toast mesajları -->
        <?php if(isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['error_message']);
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Öne Çıkan Etkinlikler -->
        <h2 class="mb-4">Öne Çıkan Etkinlikler</h2>
        <div class="row">
        <?php while ($row = $featured_result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card event-card h-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="event-info">
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($row['location']); ?><br>
                                <i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($row['event_date']); ?><br>
                                <i class="bi bi-ticket-perforated"></i> Kalan Bilet: <?php echo htmlspecialchars($row['available_tickets']); ?>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <?php if ($row['available_tickets'] > 0 && isset($_SESSION['user_id'])): ?>
                                <a href="buy_ticket.php?event_id=<?php echo $row['id']; ?>" class="btn btn-primary flex-grow-1">Bilet Al</a>
                            <?php elseif ($row['available_tickets'] > 0): ?>
                                <a href="login.php" class="btn btn-primary flex-grow-1">Giriş Yap ve Bilet Al</a>
                            <?php else: ?>
                                <button class="btn btn-secondary flex-grow-1" disabled>Tükendi</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

        <!-- Diğer Etkinlikler -->
        <?php if ($other_result->num_rows > 0): ?>
            <h2 class="mb-4 mt-5">Diğer Etkinlikler</h2>
            <div class="row">
            <?php while ($row = $other_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100 shadow">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <div class="event-info">
                                <p class="card-text">
                                    <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($row['location']); ?><br>
                                    <i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($row['event_date']); ?><br>
                                    <i class="bi bi-ticket-perforated"></i> Kalan Bilet: <?php echo htmlspecialchars($row['available_tickets']); ?>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if ($row['available_tickets'] > 0 && isset($_SESSION['user_id'])): ?>
                                    <a href="buy_ticket.php?event_id=<?php echo $row['id']; ?>" class="btn btn-primary flex-grow-1">Bilet Al</a>
                                <?php elseif ($row['available_tickets'] > 0): ?>
                                    <a href="login.php" class="btn btn-primary flex-grow-1">Giriş Yap ve Bilet Al</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary flex-grow-1" disabled>Tükendi</button>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['user_id']) && $row['can_edit']): ?>
                                    <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Düzenle</a>
                                    <a href="delete_event.php?id=<?php echo $row['id']; ?>" class="btn btn-danger delete-event">Sil</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            const confirmDelete = document.getElementById('confirmDelete');
            
            document.querySelectorAll('.delete-event').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    confirmDelete.href = this.href;
                    new bootstrap.Modal(deleteModal).show();
                });
            });
        });
    </script>
</body>
</html>