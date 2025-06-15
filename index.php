<?php
session_start();
$mysqli = new mysqli("localhost", "dbusr23360859058", "7TSUsWZDHR74", "dbstorage23360859058");

// Öne çıkan etkinlikleri çek
$sql = "SELECT * FROM events WHERE title IN ('ATI242 Konseri', 'Cem Yılmaz Stand-up', 'Serdar Ortaç Konseri') ORDER BY event_date ASC";
$featured = $mysqli->query($sql);

// Etkinliğe özel resim seçme fonksiyonu
function getEventImage($title) {
    if ($title === 'ATI242 Konseri') {
        return 'images/ati242.png';
    } elseif ($title === 'Cem Yılmaz Stand-up') {
        return 'images/cemyilmaz.png';
    } elseif ($title === 'Serdar Ortaç Konseri') {
        return 'images/serdarortac.png';
    } else {
        return 'images/event.png';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ŞuBilet</title>
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
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 40px;
        }
        .event-card {
            transition: transform 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .event-info {
            padding: 10px 0;
        }
        .event-info i {
            width: 20px;
            text-align: center;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4">Etkinlikleri Keşfedin</h1>
            <p class="lead">En iyi konserler, tiyatrolar ve daha fazlası burada!</p>
            <a href="event_list.php" class="btn btn-primary btn-lg">Tüm Etkinlikleri Görüntüle</a>
        </div>
    </section>

    <!-- Öne Çıkan Etkinlikler Section -->
    <section class="container mb-5">
        <h2 class="text-center mb-4">Öne Çıkan Etkinlikler</h2>
        <div class="row">
            <?php while ($event = $featured->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card event-card h-100 shadow">
                    <img src="<?php echo getEventImage($event['title']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($event['title']); ?>">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-info">
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($event['location']); ?><br>
                                <i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($event['event_date']); ?><br>
                                <i class="bi bi-ticket-perforated"></i> Kalan Bilet: <?php echo htmlspecialchars($event['available_tickets']); ?>
                            </p>
                        </div>
                        <?php if ($event['available_tickets'] > 0): ?>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="buy_ticket.php?event_id=<?php echo $event['id']; ?>" 
                                   class="btn btn-primary w-100">Bilet Al</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary w-100">Giriş Yap ve Bilet Al</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Tükendi</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">© 2025 ŞuBilet. Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>