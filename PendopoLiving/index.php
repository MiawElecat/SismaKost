<?php
session_start();

include 'koneksi.php';


// Query untuk mendapatkan total kamar mandi dalam
$query_mandi_dalam = "SELECT COUNT(k.idKamar) AS total_kamar
                      FROM kamar k
                      JOIN kamar_fasilitas kf ON k.idKamar = kf.idKamar
                      JOIN fasilitas f ON kf.idFasilitas = f.idFasilitas
                      WHERE f.namaFasilitas = 'kamar mandi dalam'
                      AND k.status = 'tersedia'";
$result_mandi_dalam = mysqli_query($koneksi, $query_mandi_dalam);
$data_mandi_dalam = mysqli_fetch_assoc($result_mandi_dalam);
$total_mandi_dalam = $data_mandi_dalam['total_kamar'];

// Query untuk mendapatkan total kamar mandi luar
$query_mandi_luar = "SELECT COUNT(k.idKamar) AS total_kamar
                     FROM kamar k
                     JOIN kamar_fasilitas kf ON k.idKamar = kf.idKamar
                     JOIN fasilitas f ON kf.idFasilitas = f.idFasilitas
                     WHERE f.namaFasilitas = 'kamar mandi luar'
                     AND k.status = 'tersedia'";
$result_mandi_luar = mysqli_query($koneksi, $query_mandi_luar);
$data_mandi_luar = mysqli_fetch_assoc($result_mandi_luar);
$total_mandi_luar = $data_mandi_luar['total_kamar'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Elisa Kost</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assetss/img/favicon.png" rel="icon">
  <link href="assetss/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assetss/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assetss/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assetss/vendor/aos/aos.css" rel="stylesheet">
  <link href="assetss/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assetss/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assetss/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assetss/img/logo.png" alt=""> -->
        <h1 class="sitename">Elisa Kost</h1>
      </a>

      <nav id="navmenu" class="navmenu">
  <ul>
    <li><a href="#hero" class="active">Beranda</a></li>
    <li><a href="#about">Tentang</a></li>
    <li><a href="#team">Data Kamar</a></li> 
    <!-- <li><a href="#testimonials">Fasilitas Kamar</a></li> -->
    <!-- <li><a href="#pemesanan">Pemesanan</a></li> -->
    <li><a href="#contact">Kontak</a></li>

    <?php if (isset($_SESSION['namaPenyewa'])): ?>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle">Selamat Datang <?php echo $_SESSION['namaPenyewa']; ?>!</a>
      <ul class="dropdown-menu">
        <li><a href="Profil.php">Profil</a></li> 
        <li><a href="pesananku.php">Pesananku</a></li>  
        <li><a href="logout.php">Keluar</a></li>
      </ul>
    </li>
    <?php
      // Menyimpan idPenyewa dalam variabel tanpa menampilkannya
      $idPenyewa = $_SESSION['idPenyewa'];
    ?>
<?php else: ?>
    <li><a href="login.php">Login</a></li>
<?php endif; ?>

  </ul>
  <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
            <h1>Kost Elisa - By Tim Resit</h1>
            <p>Terbuka Layanan Kost Putri</p>
            <!-- <div class="d-flex">
              <a href="#about" class="btn-get-started">Get Started</a>
              <a href="https://www.youtube.com/watch?v=LXb3EKWsInQ" class="glightbox btn-watch-video d-flex align-items-center"><i class="bi bi-play-circle"></i><span>Watch Video</span></a>
            </div> -->
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="200">
            <img src="assetss/img/hero-img.png" class="img-fluid animated" alt="">
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->


    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>About Us</h2>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <p>
            Website Elisa Kost adalah platform online yang memudahkan Anda menemukan tempat tinggal yang nyaman dan sesuai dengan kebutuhan. Temukan berbagai pilihan kost dengan mudah dan cepat melalui fitur-fitur menarik yang kami sediakan.
            </p>
            <ul>
            </ul>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <p>Website ini digunakan untuk anda dalam mencari tempat nyaman sesuai kebutuhan anda. ini adapalah pilihan yang tepat untuk menemukan tempat paling nyaman dan sesuai dengan budget anda.  </p>
            <!-- <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a> -->
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

   
    <!-- Team Section -->
    <section id="team" class="team section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Pencarian Kost Ternyaman</h2>
    <p>Kamar Kost Ternyaman dan Termurah Kami Sediakan Di Bawah ini</p>
  </div><!-- End Section Title -->

  <div class="container">
    <div class="row gy-4">
      <div class="row">
        <!-- Kamar Mandi Dalam -->
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <div class="team-member d-flex align-items-start">
            <a href="daftar_kamar.php?fasilitas=kamar mandi dalam">
              <div class="pic"><img src="assetss/img/appartment.jpeg" class="img-fluid" alt=""></div>
            </a>
            <div class="member-info">
              <h4><a href="daftar_kamar.php?fasilitas=kamar mandi dalam">Kamar Mandi Dalam</a></h4>
              <span>Semua Kamar: <?php echo $total_mandi_dalam; ?></span>
            </div>
          </div>
        </div><!-- End Team Member -->

        <!-- Kamar Mandi Luar -->
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="team-member d-flex align-items-start">
            <a href="daftar_kamar.php?fasilitas=kamar mandi luar">
              <div class="pic"><img src="assetss/img/appartment.jpeg" class="img-fluid" alt=""></div>
            </a>
            <div class="member-info">
              <h4><a href="daftar_kamar.php?fasilitas=kamar mandi luar">Kamar Mandi Luar</a></h4>
              <span>Semua Kamar: <?php echo $total_mandi_luar; ?></span>
            </div>
          </div>
        </div><!-- End Team Member -->
      </div>      
    </div>
  </div>
</section><!-- /Team Section -->


 

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <!-- <div class="container section-title" data-aos="fade-up">
        <h2>fasilitas kamar</h2>
        <p>Halo</p>
      </div>End Section Title -->

      <!-- <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script> -->

          
          <!-- <div class="swiper-wrapper">

            <div class="swiper-slide">
            <div class="container section-title" data-aos="fade-up">
              <div class="testimonial-item">
                <h2>Profil Perusahaan</h2>
                <img src="assetss/img/appartment.jpeg" class="testimonial-img" alt="">
                <h3>By Tim Resit</h3>
                <h4>Website Elisa Kos</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Website ini dibuat untuk memudahkan para pencari kos yang murah dan nyaman.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div>End testimonial item

          </div> -->
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->


    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Halo</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-5">

            <div class="info-wrap">
              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h3>Address</h3>
                  <p>Jember, Jawa Timur</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-telephone flex-shrink-0"></i>
                <div>
                  <h3>Contac us</h3>
                  <p>085649958936</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h3>Email Us</h3>
                  <p>kostelisa@gmail.com</p>
                </div>
              </div><!-- End Info Item -->
              <iframe src = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.0650512605586!2d113.7204238!3d-8.1638266!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd695b7ef3d0d19%3A0x66cc368ecf37c79b!2sKOS%20ELISA!5e0!3m2!1sen!2sid!4v1700466219402!5m2!1sen!2sid" 
                frameborder = "0" 
                style = "border:0; width: 100%; height: 270px;" 
                allowfullscreen = "" 
                loading = "lazy" 
                referrerpolicy = "no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>

          <div class="col-lg-7">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
              <div class="row gy-4">

              <div class="col-md-6">
                <label for="name-field" class="pb-2">Your Name</label>
                <input type="text" name="name" id="name-field" class="form-control" 
                      value="<?php echo isset($_SESSION['namaPenyewa']) ? htmlspecialchars($_SESSION['namaPenyewa']) : ''; ?>" 
                      <?php echo isset($_SESSION['namaPenyewa']) ? 'readonly' : ''; ?> required>
              </div>

              <div class="col-md-6">
                <label for="email-field" class="pb-2">Your Email</label>
                <input type="email" name="email" id="email-field" class="form-control" 
                      value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                      <?php echo isset($_SESSION['email']) ? 'readonly' : ''; ?> required>
              </div>

                <div class="col-md-12">
                  <label for="subject-field" class="pb-2">Subject</label>
                  <input type="text" class="form-control" name="subject" id="subject-field" required="">
                </div>

                <div class="col-md-12">
                  <label for="message-field" class="pb-2">Message</label>
                  <textarea class="form-control" name="message" rows="10" id="message-field" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>

                  <button type="submit">Send Message</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer">

    <div class="footer-newsletter">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-6">
            <h4>Join Our Newsletter</h4>
            <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
            <form action="forms/newsletter.php" method="post" class="php-email-form">
              <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
              <div class="loading">Loading</div>
              <div class="error-message"></div>
              <div class="sent-message">Your subscription request has been sent. Thank you!</div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="d-flex align-items-center">
            <span class="sitename">Elisa Kost</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Jember, Jawa Timur</p>
            <p class="mt-3"><strong>No Telp:</strong> <span>083134628726</span></p>
            <p><strong>Email:</strong> <span>ElisaKost@gmail.com</span></p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="home">Home</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">About us</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Kamar</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Fasilitas</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          
        </div>

        <div class="col-lg-4 col-md-12">
          <h4>Follow Us</h4>
          <p>Jangan lupa follow</p>
          <div class="social-links d-flex">
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p><span>Web Elisa Kost</span> <strong class="px-1 sitename">By</strong> <span>Tim Resit</span></p>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assetss/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assetss/vendor/php-email-form/validate.js"></script>
  <script src="assetss/vendor/aos/aos.js"></script>
  <script src="assetss/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assetss/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assetss/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assetss/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assetss/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assetss/js/main.js"></script>

</body>

</html>