**SORU: Etkinlik bilet satış sistemi php projesini nasıl yapacağımı özetler misin**


# 🎟️ Etkinlik Bilet Satış Sistemi – Proje Özeti

---

## ⚙️ Proje Fonksiyonları (CRUD + Oturum)

| İşlem                     | Açıklama                                                                 |
|---------------------------|--------------------------------------------------------------------------|
| Kullanıcı Kaydı           | Kullanıcı ad, soyad, e-posta, telefon ve şifre ile kayıt olur. Şifre güvenli şekilde hash’lenir. |
| Oturum Açma/Kapama        | Kullanıcı e-posta + şifre ile giriş yapar, çıkış yapabilir.             |
| Etkinlik Yönetimi         | Admin veya yetkili kullanıcı etkinlik ekler, günceller, siler, listeler. |
| Bilet Satışı              | Kullanıcı etkinlik seçer, uygun bilet varsa satın alır. Bilet sayısı azalır. |
| Kullanıcı Bilgi Listeleme | Kullanıcı kendi satın aldığı bilet bilgilerini görebilir.               |

---

## 🗂️ Proje Dosya Yapısı (Minimum)

| Dosya Adı         | Açıklama                                                                 |
|-------------------|--------------------------------------------------------------------------|
| register.php      | Kullanıcı kayıt formu ve işlemleri                                       |
| login.php         | Kullanıcı giriş formu ve doğrulaması                                     |
| logout.php        | Oturumu kapatma (çıkış) işlemi                                           |
| index.php         | Ana sayfa, etkinliklerin listelendiği sayfa                             |
| add_event.php     | Yeni etkinlik ekleme (admin için)                                        |
| edit_event.php    | Etkinlik güncelleme işlemi                                               |
| delete_event.php  | Etkinlik silme işlemi                                                    |
| buy_ticket.php    | Kullanıcının bilet satın alma işlemi                                     |
| my_tickets.php    | Kullanıcının satın aldığı biletleri gösterme                             |
| config.php        | Veritabanı bağlantısı ve ortak ayarlar                                   |
| header.php        | Site üst kısmı (menü, oturum kontrol vs.) (opsiyonel)                    |
| footer.php        | Site alt kısmı (opsiyonel)                                               |

---

## 📋 İş Akışı

1. Kullanıcı `register.php` üzerinden kayıt olur.
2. Kullanıcı `login.php` ile giriş yapar, oturum açılır.
3. Giriş yaptıktan sonra `index.php` üzerinde etkinlikler listelenir.
4. Admin:
   - `add_event.php` ile yeni etkinlik ekler.
   - `edit_event.php` ile etkinlik günceller.
   - `delete_event.php` ile etkinlik siler.
5. Kullanıcı:
   - `buy_ticket.php` üzerinden etkinlik seçip bilet satın alır.
   - Bu işlemde `available_tickets` değeri 1 azaltılır.
   - `my_tickets.php` ile satın aldığı biletleri görebilir.
6. Kullanıcı `logout.php` ile oturumu kapatır.

---

## 💡 Ek Notlar

- 🔐 Tüm sayfalarda oturum kontrolü yapılmalı. Giriş yapılmamışsa yetkisiz erişim engellenmeli.
- 🔗 Veritabanı bağlantısı `config.php` dosyasında tek seferde tanımlanmalı ve gerekli dosyalarda `include` edilmelidir.
- 🔑 Şifreler `password_hash()` ile güvenli şekilde kaydedilmeli ve `password_verify()` ile doğrulanmalıdır.
- 🎨 CSS kütüphanesi (örneğin Bootstrap) kullanılmalı, tüm sayfalar stil açısından uyumlu olmalıdır.
- 🛡️ SQL Injection saldırılarına karşı `Prepared Statement` (hazırlanmış ifadeler) kullanılmalıdır.

---

## 📦 Toplam Minimum PHP Dosya Sayısı

| Kategori               | Dosya Sayısı | Açıklama                               |
|------------------------|--------------|----------------------------------------|
| Kullanıcı İşlemleri    | 3            | register, login, logout                |
| Etkinlik Yönetimi      | 3            | add_event, edit_event, delete_event    |
| Bilet İşlemleri        | 2            | buy_ticket, my_tickets                 |
| Ana Sayfa              | 1            | index.php                              |
| Veritabanı Ayarları    | 1            | config.php                              |
| Ortak Sayfa Parçaları  | 1–2          | header.php, footer.php (opsiyonel)     |

Toplam: Yaklaşık 10–12 PHP dosyası yeterlidir.

---




**SORU : etkinlik bilet satış sistemi sitesi için oluşturacağım tabloları yaz**

Bu bölümde Etkinlik Bilet Satış Sistemi için gerekli olan veritabanı ve tabloları oluşturacağız. Aşağıdaki SQL komutlarını phpMyAdmin veya MySQL terminalinde çalıştırabilirsiniz.

---

## 📂 Veritabanı Oluştur

```sql
CREATE DATABASE etkinlik_sistemi;
USE etkinlik_sistemi;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    event_date DATE,
    total_tickets INT DEFAULT 0,
    available_tickets INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

```
---

**SORU: time_Stamp nedir ve available tickets nasıl azalacak**





TIMESTAMP, MySQL'de tarih ve saat bilgisini saklayan özel bir veri türüdür. Genellikle verinin ne zaman oluşturulduğunu veya güncellendiğini otomatik olarak kaydetmek için kullanılır.

### 📌 Kullanım Örneği

```sql
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

🎟️ available_tickets Nasıl Azalır?
available_tickets sütunu, bir etkinlikte kalan bilet sayısını tutar. Kullanıcı bir bilet aldığında bu sayı PHP kodu ile 1 azaltılır.

🔁 Örnek Senaryo
Kullanıcı bir etkinlik sayfasına gelir.

Sistemde bu etkinlik için available_tickets > 0 mı kontrol edilir.

Eğer varsa:

Bilet kaydı yapılır (tickets tablosuna insert).

events tablosundaki available_tickets değeri 1 azaltılır.

---


**SORU: phpden mysql de olusturduğum tabloya nasıl bağlanabilirim**

# 🐘 PHP ile MySQL Veritabanına Bağlanma ve Veri Çekme Rehberi

Bu rehberde PHP kullanarak MySQL veritabanına nasıl bağlanılır, tabloya sorgu nasıl atılır ve veriler nasıl alınır adım adım anlatılmaktadır.

---

## ✅ 1. Veritabanı Bağlantısını Kurmak

Aşağıdaki kod parçası, PHP ile bir MySQL veritabanına nasıl bağlanacağınızı gösterir:

```php
<?php
$host = "localhost";          // veya 127.0.0.1
$username = "kullanici_adi"; // genellikle "root"
$password = "sifre";          // MySQL şifreniz
$database = "veritabani_adi"; // Hedef veritabanı adı

// Bağlantıyı oluştur
$conn = new mysqli($host, $username, $password, $database);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}
echo "Bağlantı başarılı!";
?>
📌 Not: mysqli yerine PDO da kullanılabilir, ancak mysqli öğrenmesi daha kolay olduğu için yeni başlayanlar için idealdir.

✅ 2. Tablodan Veri Okumak (SELECT)
Aşağıdaki örnek tabloya SELECT sorgusu atıp verileri ekrana yazdırır:

php
Kopyala
Düzenle
$sql = "SELECT * FROM tablo_adi"; // Örnek: kullanicilar
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Tüm satırları yazdır
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . " - İsim: " . $row["isim"] . "<br>";
    }
} else {
    echo "Tabloda veri yok.";
}
✅ 3. Bağlantıyı Kapatmak
Bağlantıyı manuel olarak kapatmak önemlidir:

php
Kopyala
Düzenle
$conn->close();
🔁 Tam Uygulama Örneği
Aşağıda, veritabanına bağlanıp kullanicilar adlı tablodan veri çekilen tam bir örnek yer almaktadır:

php
Kopyala
Düzenle
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "ornek_db";

// Bağlantıyı oluştur
$conn = new mysqli($host, $username, $password, $database);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Karakter setini ayarla
$conn->set_charset("utf8mb4");

// Sorguyu çalıştır
$sql = "SELECT * FROM kullanicilar";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Sonuçları yazdır
    while($row = $result->fetch_assoc()) {
        echo "Kullanıcı Adı: " . $row["kullanici_adi"] . "<br>";
    }
} else {
    echo "Kayıt bulunamadı.";
}

// Bağlantıyı kapat
$conn->close();
?>
🛠️ İpuçları
❌ Eğer bağlantı hatası alırsan, detaylı hata mesajını görmek için mysqli_connect_error() kullanılabilir.

✅ phpMyAdmin'de tablo ve veritabanı adlarının doğru yazıldığından emin ol.

🇹🇷 Türkçe karakter sorunu yaşarsan, karakter setini şu şekilde ayarla:

php
Kopyala
Düzenle
$conn->set_charset("utf8mb4");
Bu yapılarla PHP ile veritabanı işlemlerine hızlıca giriş yapabilir, kullanıcıdan veri alıp veritabanına işleyebilir ve veri listeleyebilirsin.


