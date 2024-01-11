<?php 
include ('config.php');
session_start();

if(!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])){
    header('location:login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Lecture Room/Hall Online Booking System</title>
    <?php require('inc/links.php'); ?>
    
</head>
<body>
    <?php require('inc/header.php'); ?>
    <!-- Swiper Carousal-->
    <div class="container-fluid px-lg-4 mt-4">
        <div class="swiper swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="images/carousel/1.png" class="w-100 d-block" />
                </div>
                <div class="swiper-slide">
                    <img src="images/carousel/2.png" class="w-100 d-block" />
                </div>
                <div class="swiper-slide">
                    <img src="images/carousel/3.png" class="w-100 d-block" />
                </div>
                <div class="swiper-slide">
                    <img src="images/carousel/4.png" class="w-100 d-block" />
                </div>
                <div class="swiper-slide">
                    <img src="images/carousel/5.png" class="w-100 d-block" />
                </div>
                <div class="swiper-slide">
                    <img src="images/carousel/6.png" class="w-100 d-block" />
                </div>
                <div class="swiper-slide">
                    <img src="images/carousel/7.png" class="w-100 d-block" />
                </div>
            </div>
        </div>
    </div>
    
    <h3 class="my-5 px-4 mb-2 text-center fw-bold ">LecRoom Welcomes You: Your Central Source for Learning Space and Tool Reservations.</h3><br>
    <div class="h-line bg-dark"></div>

    <div class="container mt-5 pt-4 mb-4">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="card border-0">
                    <div class="card-body">
                        <h4 class="card-title fw-bold mb-4">Learning Spaces</h4>
                        <h6 class="mb-3">Here are the Learning Spaces that we offer:</h6>
                        <h6 class="mb-1"><i class="bi bi-geo-alt"></i>Bangunan Podium:</h6>
                        <h6>Podium A, Podium B, Podium C</h6><br>
                        <h6 class="mb-1"><i class="bi bi-geo-alt"></i>Bangunan Menara:</h6>
                        <h6>Menara 8A, Menara 8B, Menara 8C</h6><br>
                        <h6 class="mb-1"><i class="bi bi-geo-alt"></i>Dewan Kuliah Pusat:</h6>
                        <h6>Dewan Kuliah 1, Dewan Kuliah 2, Dewan Kuliah 3</h6><br>
                        <h6 class="mb-1"><i class="bi bi-geo-alt"></i>Makmal Komputer:</h6>
                        <h6>Makmal E-Commerce Application, Makmal Computer Graphic, Makmal Windows Computing</h6>
                        <div class="d-flex justify-content-evenly mb-2">
                            <a href ="spaces.php" class="btn text-white custom-bg shadow-none" style="margin-top: 10px;">More details</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="card border-0" style="height: 400px;">
                        <img src="images/homepage_space/1.jpg" style="height: 100%; width: auto;" alt="...">
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 pt-4 mb-4">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="card border-0" style="height: 400px;">
                    <img src="images/homepage_tool/1.jpeg" style="height: 100%; width: auto;" alt="...">
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="card border-0">
                    <div class="card-body">
                        <h4 class="card-title fw-bold mb-4">Tools And Accessories</h4>
                        <h6 class="mb-4">Here are the Tools And Accessories that we offer:<br>
                        <h6 class="mb-1">Tripod, Slider, LED Video Lite Panels 1, LED Video Lite Panels 2, LED Video Light 1, LED Video Light 2, Drawing Pad Wacom</h6><br>
                        <div class="d-flex justify-content-evenly mb-2">
                            <a href ="tools.php" class="btn text-white custom-bg shadow-none" style="margin-top: 10px;">More details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="my-5 fw-bold text-center">MANAGEMENT TEAM</h3>
    <div class="h-line bg-dark"></div><br>

    <div class="container px-4">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper mb-5">
                <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                <img src="images/staff/Staff 1.jpeg" class="w-100" style="max-width: 70%; height: auto;">
                <h5 class="mt-2">MOHIZAM BIN MOHAMAD</h5>
                </div>
                <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                <img src="images/staff/Staff 2.jpeg" class="w-100" style="max-width: 70%; height: auto;">
                <h5 class="mt-2">NORENNA JUFRI</h5>
                </div>
                <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                <img src="images/staff/Staff 3.jpeg" class="w-100" style="max-width: 70%; height: auto;">
                <h5 class="mt-2">EZY @ NOROL ATIKAH SABTU</h5>
                </div>
                <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                <img src="images/staff/Staff 4.jpeg" class="w-100" style="max-width: 70%; height: auto;">
                <h5 class="mt-2">MOHD AZLAN BIN GANI</h5>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <?php require('inc/footer.php') ?>
    
    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 40,
            pagination: {
            el: ".swiper-pagination",
            },
            breakpoints: {
            320: {
                slidesPerView: 1,
            },
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 3,
            },
            }
        });
    </script>
    <script>
        var swiper = new Swiper(".swiper-container", {
        spaceBetween: 30,
        effect: "fade",
        loop: true,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        }
        });

        var swiper = new Swiper(".swiper-testimonials", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "auto",
        slidesPerView: "3",
        loop: true,
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false,
        },
        pagination: {
            el: ".swiper-pagination",
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
            },
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        }
        });
    </script>
    
</body>
</html>