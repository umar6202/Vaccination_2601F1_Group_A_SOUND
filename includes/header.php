<?php
require_once __DIR__ . '/store.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$pageTitle = $pageTitle ?? 'VEBRA — Music, Artists & Releases';
$pageDesc = $pageDesc ?? 'Discover VEBRA releases, music videos, artists, live shows and stories.';
$activePage = $activePage ?? '';
$activeSub = $activeSub ?? '';
$basePath = $basePath ?? '';
$bodyClass = $bodyClass ?? 'theme-magenta';
$extraHead = $extraHead ?? '';
$ogImage = $ogImage ?? ($basePath . 'assets/images/hero-slide-1.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A0A0F">
    

    <!-- Five Meta / Site Icons -->
     
    <link href="<?= $basePath ?>favicon.svg" rel="icon" type="image/svg+xml">
    <link href="<?= $basePath ?>assets/images/apple-touch-icon.png" rel="apple-touch-icon" sizes="180x180">
    <link href="<?= $basePath ?>assets/images/favicon-32x32.png" rel="alternate icon" type="image/png" sizes="32x32">
    <link href="<?= $basePath ?>assets/images/icon-192.png" rel="icon" type="image/png" sizes="192x192">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Open Graph & Social Preview -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap">

    <link rel="stylesheet" href="<?= $basePath ?>assets/css/vendor/bootstrap-grid.min.css">
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/vendor/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= $basePath ?>assets/icon-fonts/remixicon/remixicon.css">
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/store.css">
    <script src="<?= $basePath ?>assets/js/no-fouc.js"></script>
    <?= $extraHead ?>
</head>

<body class="<?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') ?>">

    <svg class="svg-defs" aria-hidden="true" focusable="false">
        <filter id="vebra-outline" color-interpolation-filters="sRGB">
            <feComponentTransfer in="SourceGraphic" result="solid">
                <feFuncA type="linear" slope="255"/>
            </feComponentTransfer>
            <feMorphology in="solid" operator="erode" radius="1" result="core"/>
            <feComposite in="solid" in2="core" operator="out" result="rim"/>
            <feComposite in="rim" in2="SourceGraphic" operator="in"/>
        </filter>
    </svg>

    <!-- Original animated preloader -->
    <div class="preloader">
        <div class="preloader__curtain">
            <div class="preloader__panel"></div>
            <div class="preloader__panel"></div>
            <div class="preloader__panel"></div>
            <div class="preloader__panel"></div>
        </div>
        <div class="preloader__inner">
            <div class="preloader__word">vebra</div>
            <p class="preloader__tagline">Sound in its purest form</p>
            <div class="preloader__bar">
                <span class="preloader__bar-fill"></span>
            </div>
        </div>
        <div class="preloader__count">00</div>
    </div>

    <div class="grain" aria-hidden="true"></div>
    <div class="cursor" aria-hidden="true"></div>
    <div class="cursor-ring" aria-hidden="true">
        <span class="cursor-ring__label"></span>
    </div>

    <!-- HEADER -->
    <header class="site-header site-header--float">
        <div class="site-header__inner">
            <a href="<?= $basePath ?>index.php" class="site-logo" aria-label="VEBRA home">
                <span class="site-logo__word">vebra<span class="site-logo__dot">.</span></span>
            </a>

            <nav class="nav" aria-label="Primary">
                <ul class="nav__list">
                    <!-- Home -->
                    <li class="nav__item<?= $activePage === 'home' ? ' is-active' : '' ?>">
                        <a href="<?= $basePath ?>index.php" class="nav__link">
                            <span class="nav__link-inner">
                                <span class="nav__link-text" data-text="Home">Home</span>
                            </span>
                        </a>
                    </li>

                  
<!-- Music -->
<li class="nav__item has-children<?= $activePage === 'music' ? ' is-active' : '' ?>">
    <a href="<?= $basePath ?>all-music.php" class="nav__link">
        <span class="nav__link-inner">
            <span class="nav__link-text" data-text="Music">Music</span>
        </span>
        <i class="ri-arrow-down-s-line"></i>
    </a>

    <ul class="nav__sub">
        <li class="<?= $activeSub === 'all-music' ? 'is-active' : '' ?>">
            <a href="<?= $basePath ?>all-music.php">All Songs</a>
        </li>
        <li class="<?= $activeSub === 'discography' ? 'is-active' : '' ?>">
            <a href="<?= $basePath ?>discography.php">Discography</a>
        </li>
        <li class="<?= $activeSub === 'albums' ? 'is-active' : '' ?>">
            <a href="<?= $basePath ?>albums.php">Albums</a>
        </li>
        <li class="<?= $activeSub === 'singles' ? 'is-active' : '' ?>">
            <a href="<?= $basePath ?>singles.php">Singles</a>
        </li>
    </ul>
</li>

                    <!-- Videos -->
                    <li class="nav__item has-children<?= $activePage === 'videos' ? ' is-active' : '' ?>">
                        <a href="<?= $basePath ?>video-gallery.php" class="nav__link">
                            <span class="nav__link-inner">
                                <span class="nav__link-text" data-text="Videos">Videos</span>
                            </span>
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul class="nav__sub">
                            <li class="<?= $activeSub === 'video-gallery' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>video-gallery.php">Video Gallery</a></li>
                            <li class="<?= $activeSub === 'single-video' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>single-video.php">Featured Video</a></li>
                        </ul>
                    </li>

                    <!-- Artists -->
                    <li class="nav__item has-children<?= $activePage === 'artists' ? ' is-active' : '' ?>">
                        <a href="<?= $basePath ?>artists.php" class="nav__link">
                            <span class="nav__link-inner">
                                <span class="nav__link-text" data-text="Artists">Artists</span>
                            </span>
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul class="nav__sub">
                            <li class="<?= $activeSub === 'artists' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>artists.php">All Artists</a></li>
                            <li class="<?= $activeSub === 'single-artist' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>single-artist.php">Featured Artist</a></li>
                        </ul>
                    </li>

                    <!-- Events -->
                    <li class="nav__item has-children<?= $activePage === 'events' ? ' is-active' : '' ?>">
                        <a href="<?= $basePath ?>events.php" class="nav__link">
                            <span class="nav__link-inner">
                                <span class="nav__link-text" data-text="Events">Events</span>
                            </span>
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul class="nav__sub">
                            <li class="<?= $activeSub === 'events' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>events.php">All Events</a></li>
                            <li class="<?= $activeSub === 'single-event' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>single-event.php">Featured Event</a></li>
                        </ul>
                    </li>

                    <!-- News -->
                    <li class="nav__item has-children<?= $activePage === 'news' ? ' is-active' : '' ?>">
                        <a href="<?= $basePath ?>news-page.php" class="nav__link">
                            <span class="nav__link-inner">
                                <span class="nav__link-text" data-text="News">News</span>
                            </span>
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul class="nav__sub">
                            <li class="<?= $activeSub === 'news' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>news-page.php">All News</a></li>
                            <li class="<?= $activeSub === 'single-news' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>single-news.php">Single News</a></li>
                        </ul>
                    </li>

                    <!-- More (Shop, Gallery, Typography) -->
                    <li class="nav__item has-children<?= $activePage === 'more' ? ' is-active' : '' ?>">
                        <a href="<?= $basePath ?>shop-page.php" class="nav__link">
                            <span class="nav__link-inner">
                                <span class="nav__link-text" data-text="More">More</span>
                            </span>
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul class="nav__sub">
                            <li class="<?= $activeSub === 'shop' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>shop-page.php">Shop / Vinyl</a></li>
                            <li class="<?= $activeSub === 'single-product' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>single-product.php">Single Product</a></li>
                            <li class="<?= $activeSub === 'gallery' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>gallery.php">Photo Gallery</a></li>
                            <li class="<?= $activePage === 'sitemap' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>sitemap.php">Sitemap</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="header-actions">
                <!-- Search link -->
                <a href="<?= $basePath ?>search.php" class="nav__link<?= $activePage === 'search' ? ' is-active' : '' ?>" title="Search Music, Videos & Artists" style="padding: 0.75rem 0.5rem;">
                    <i class="ri-search-line" style="font-size: 1.15rem;"></i>
                </a>
                <a href="<?= $basePath ?>cart.php" class="nav__link<?= $activePage === 'cart' ? ' is-active' : '' ?>" title="Shopping cart" style="padding: 0.75rem 0.5rem;">
                    <i class="ri-shopping-bag-line" style="font-size: 1.15rem;"></i>
                    <span><?= vebra_store_cart_count() ?></span>
                </a>

                <!-- Account / Contact Dropdown -->
                <div class="nav__item has-children<?= in_array($activePage, ['contact', 'auth', 'admin', 'account']) ? ' is-active' : '' ?>">
                    <a href="<?= $basePath ?>contact.php" class="nav__link">
                        <span class="nav__link-inner">
                            <span class="nav__link-text" data-text="<?= $isLoggedIn ? htmlspecialchars($_SESSION['username'] ?? 'Account') : 'Account' ?>">
                                <?= $isLoggedIn ? htmlspecialchars($_SESSION['username'] ?? 'Account') : 'Account' ?>
                            </span>
                        </span>
                        <i class="ri-arrow-down-s-line"></i>
                    </a>
                    <ul class="nav__sub nav__sub--right">
                        <li class="<?= $activePage === 'contact' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>contact.php"><i class="ri-mail-line"></i> Contact</a></li>
                        <li class="<?= $activePage === 'sitemap' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>sitemap.php"><i class="ri-map-2-line"></i> Sitemap</a></li>
                        <?php if ($isLoggedIn): ?>
                            <?php if ($isAdmin): ?>
                                <li><a href="<?= $basePath ?>admin/index.php"><i class="ri-dashboard-line"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><a href="<?= $basePath ?>auth/logout.php"><i class="ri-logout-box-r-line"></i> Logout</a></li>
                        <?php else: ?>
                            <li class="<?= $activeSub === 'login' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>auth/login.php"><i class="ri-login-box-line"></i> Login</a></li>
                            <li class="<?= $activeSub === 'register' ? 'is-active' : '' ?>"><a href="<?= $basePath ?>auth/register.php"><i class="ri-user-add-line"></i> Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <button type="button" class="menu-trigger" data-magnetic="0.25"
                    aria-expanded="false" aria-controls="overlay-menu">
                    <span class="menu-trigger__label btn__label">Menu</span>
                    <span class="menu-trigger__bars" aria-hidden="true"><span></span><span></span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- OVERLAY MENU -->
    <div class="overlay-menu" id="overlay-menu" aria-hidden="true">
        <div class="overlay-menu__inner">
            <nav class="overlay-nav" aria-label="Fullscreen navigation">
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>index.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">01</span> Home
                    </a>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>discography.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">02</span> Music
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle Music links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>all-music.php">All Songs</a></li>
                            <li><a href="<?= $basePath ?>discography.php">Discography</a></li>
                            <li><a href="<?= $basePath ?>albums.php">Albums</a></li>
                            <li><a href="<?= $basePath ?>singles.php">Singles</a></li>
                            <li><a href="<?= $basePath ?>single-album.php">Featured Album</a></li>
                        </ul>
                    </div></div>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>video-gallery.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">03</span> Videos
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle Videos links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>video-gallery.php">Video Gallery</a></li>
                            <li><a href="<?= $basePath ?>single-video.php">Featured Video</a></li>
                        </ul>
                    </div></div>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>artists.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">04</span> Artists
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle Artists links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>artists.php">All Artists</a></li>
                            <li><a href="<?= $basePath ?>single-artist.php">Featured Artist</a></li>
                        </ul>
                    </div></div>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>events.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">05</span> Events
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle Events links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>events.php">All Events</a></li>
                            <li><a href="<?= $basePath ?>single-event.php">Featured Event</a></li>
                        </ul>
                    </div></div>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>news-page.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">06</span> News
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle News links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>news-page.php">All News</a></li>
                            <li><a href="<?= $basePath ?>single-news.php">Single News</a></li>
                        </ul>
                    </div></div>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>shop-page.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">07</span> Shop & More
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle Shop links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>shop-page.php">Shop Products</a></li>
                            <li><a href="<?= $basePath ?>single-product.php">Single Product</a></li>
                            <li><a href="<?= $basePath ?>gallery.php">Photo Gallery</a></li>
                            <li><a href="<?= $basePath ?>sitemap.php">Sitemap</a></li>
                        </ul>
                    </div></div>
                </div>
                <div class="overlay-nav__item">
                    <a href="<?= $basePath ?>contact.php" class="overlay-nav__link">
                        <span class="overlay-nav__index">08</span> Contact & Account
                    </a>
                    <button type="button" class="overlay-nav__toggle" aria-expanded="false" aria-label="Toggle Contact links"><i class="ri-add-line"></i></button>
                    <div class="overlay-nav__sub"><div>
                        <ul class="overlay-nav__sub-list">
                            <li><a href="<?= $basePath ?>contact.php">Contact Us</a></li>
                            <li><a href="<?= $basePath ?>sitemap.php">Sitemap</a></li>
                            <li><a href="<?= $basePath ?>search.php">Search Catalog</a></li>
                            <?php if ($isLoggedIn): ?>
                                <?php if ($isAdmin): ?>
                                    <li><a href="<?= $basePath ?>admin/index.php">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a href="<?= $basePath ?>auth/logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a href="<?= $basePath ?>auth/login.php">Login</a></li>
                                <li><a href="<?= $basePath ?>auth/register.php">Sign Up</a></li>
                            <?php endif; ?>
                        </ul>
                    </div></div>
                </div>
            </nav>

            <aside class="overlay-aside">
                <div class="overlay-aside__group">
                    <span class="label">Bookings & enquiries</span>
                    <a href="mailto:hello@vebra.com" class="overlay-aside__value">hello@vebra.com</a>
                </div>
                <div class="overlay-aside__group">
                    <span class="label">Follow</span>
                    <div class="socials">
                        <a href="https://facebook.com/" target="_blank" rel="noopener" aria-label="Facebook"><i class="ri-facebook-fill"></i></a>
                        <a href="https://x.com/" target="_blank" rel="noopener" aria-label="X / Twitter"><i class="ri-twitter-fill"></i></a>
                        <a href="https://instagram.com/" target="_blank" rel="noopener" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                        <a href="https://youtube.com/" target="_blank" rel="noopener" aria-label="YouTube"><i class="ri-youtube-fill"></i></a>
                        <a href="https://spotify.com/" target="_blank" rel="noopener" aria-label="Spotify"><i class="ri-spotify-fill"></i></a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
