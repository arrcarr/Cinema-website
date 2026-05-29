<?php
// 1. Mocking the 'movies' data array that you had in React (../data/movies)
// In a real application, this would likely come from a database (MySQL).
$movies = [
    [
        "id" => 1,
        "title" => "Dune: Part Two",
        "description" => "Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.",
        "image" => "https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1200", // Placeholder
        "status" => "now-showing",
        "genre" => "Sci-Fi",
        "rating" => "PG-13"
    ],
    [
        "id" => 2,
        "title" => "Oppenheimer",
        "description" => "The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.",
        "image" => "https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=1200", // Placeholder
        "status" => "now-showing",
        "genre" => "Biography",
        "rating" => "R"
    ],
    [
        "id" => 3,
        "title" => "Spider-Man: Beyond the Spider-Verse",
        "description" => "Miles Morales catapults across the Multiverse, where he encounters a team of Spider-People charged with protecting its very existence.",
        "image" => "https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=1200", // Placeholder
        "status" => "coming-soon",
        "genre" => "Animation",
        "rating" => "PG"
    ],
    [
        "id" => 4,
        "title" => "Interstellar",
        "description" => "A team of explorers travel through a wormhole in space in an attempt to ensure humanity's survival.",
        "image" => "https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1200", // Placeholder
        "status" => "now-showing",
        "genre" => "Sci-Fi",
        "rating" => "PG-13"
    ]
];

// 2. Filter movies by status using PHP
$promoMovies = array_slice($movies, 0, 3); // Takes first 3 for the carousel

$nowShowing = array_filter($movies, function($movie) {
    return $movie['status'] === 'now-showing';
});

$comingSoon = array_filter($movies, function($movie) {
    return $movie['status'] === 'coming-soon';
});
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <title>Absolute Cinema - Home Page</title>
    
    <style>
        /* Custom styles to bridge Tailwind classes used in React to raw CSS */
        body {
            background-color: #000 !important;
            color: #fff;
        }
        .text-red-600 { color: #dc3545 !important; }
        .text-red-600:hover { color: #bb2d3b !important; }
        .bg-red-600 { background-color: #dc3545 !important; }
        .bg-red-600:hover { background-color: #bb2d3b !important; }
        
        /* Custom Carousel Styles imitating React logic */
        .hero-carousel {
            position: relative;
            height: 500px;
            overflow: hidden;
            background-color: #000;
        }
        .carousel-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            z-index: 1;
        }
        .carousel-slide.active {
            opacity: 1;
            z-index: 2;
        }
        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(0,0,0,1) 0%, rgba(0,0,0,0.7) 50%, rgba(0,0,0,0) 100%);
            z-index: 10;
        }
        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .carousel-content {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            z-index: 20;
        }
        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 30;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            cursor: pointer;
        }
        .carousel-btn:hover { background: rgba(0,0,0,0.8); }
        .carousel-btn.prev { left: 20px; }
        .carousel-btn.next { right: 20px; }
        
        .carousel-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 30;
            display: flex;
            gap: 8px;
        }
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            border: none;
            padding: 0;
            transition: all 0.3s;
            cursor: pointer;
        }
        .dot.active {
            background: #dc3545;
            width: 32px;
            border-radius: 4px;
        }

        /* Movie Card styling */
        .movie-card {
            background-color: #111;
            border: 1px solid #222;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .movie-card:hover {
            transform: translateY(-5px);
        }
        .movie-card img {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
        }
        .movie-card-body {
            padding: 15px;
        }
    </style>
</head>

<body>

    <div class="min-vh-screen">
        <div class="hero-carousel">
            
            <?php foreach ($promoMovies as $index => $movie): ?>
                <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                    <div class="carousel-overlay"></div>
                    <img src="<?php echo $movie['image']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <div class="carousel-content">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-8 col-lg-6">
                                    <h1 class="display-4 fw-bold text-white mb-3">
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </h1>
                                    <p class="lead text-secondary mb-4">
                                        <?php echo htmlspecialchars($movie['description']); ?>
                                    </p>
                                    <div class="d-flex gap-3">
                                        <a href="/movie.php?id=<?php echo $movie['id']; ?>" class="btn bg-red-600 text-white px-4 py-2">
                                            Book Now
                                        </a>
                                        <a href="/movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-outline-light px-4 py-2" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(4px);">
                                            More Info
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button class="carousel-btn prev" onclick="prevSlide()" aria-label="Previous slide">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button class="carousel-btn next" onclick="nextSlide()" aria-label="Next slide">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>

            <div class="carousel-dots">
                <?php foreach ($promoMovies as $index => $movie): ?>
                    <button 
                        class="dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                        onclick="goToSlide(<?php echo $index; ?>)" 
                        aria-label="Go to slide <?php echo $index + 1; ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <section class="container py-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="h3 fw-bold text-white mb-0">Now Showing</h2>
                <a href="/movies.php" class="text-red-600 text-decoration-none fw-semibold">
                    View All →
                </a>
            </div>
            
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($nowShowing as $movie): ?>
                    <div class="col">
                        <div class="movie-card h-100">
                            <img src="<?php echo $movie['image']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <div class="movie-card-body">
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($movie['genre']); ?></span>
                                <h5 class="card-title text-white text-truncate"><?php echo htmlspecialchars($movie['title']); ?></h5>
                                <a href="/movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm bg-red-600 text-white w-100 mt-2">View Movie</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="container py-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="h3 fw-bold text-white mb-0">Coming Soon</h2>
                <a href="/movies.php" class="text-red-600 text-decoration-none fw-semibold">
                    View All →
                </a>
            </div>
            
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($comingSoon as $movie): ?>
                    <div class="col">
                        <div class="movie-card h-100">
                            <img src="<?php echo $movie['image']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <div class="movie-card-body">
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($movie['genre']); ?></span>
                                <h5 class="card-title text-white text-truncate"><?php echo htmlspecialchars($movie['title']); ?></h5>
                                <a href="/movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-outline-light w-100 mt-2">More Info</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = slides.length;

        function updateCarousel() {
            slides.forEach((slide, idx) => {
                if (idx === currentSlide) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });

            dots.forEach((dot, idx) => {
                if (idx === currentSlide) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }

        // Optional: Auto-play carousel every 5 seconds
        setInterval(nextSlide, 5000);
    </script>
</body>

</html>