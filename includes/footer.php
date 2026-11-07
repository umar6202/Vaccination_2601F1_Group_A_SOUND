<?php
$basePath = $basePath ?? '';
$loadAmplitude = $loadAmplitude ?? true;
$extraScripts = $extraScripts ?? '';

$vebraGlobalSongs = [];
if (!empty($conn)) {
    $gRes = mysqli_query($conn, "
        SELECT s.id, s.title, s.audio_path, s.artwork, a.name AS artist_name
        FROM songs s
        LEFT JOIN artists a ON a.id = s.artist_id
        WHERE s.status = 'approved' AND s.audio_path IS NOT NULL AND s.audio_path != ''
        ORDER BY s.id ASC
    ");
    if ($gRes) {
        while ($gRow = mysqli_fetch_assoc($gRes)) {
            $gArt = !empty($gRow['artwork']) ? $gRow['artwork'] : 'assets/images/album-1.jpg';
            $vebraGlobalSongs[] = [
                'name' => (string)$gRow['title'],
                'artist' => (string)($gRow['artist_name'] ?? 'VEBRA'),
                'url' => (string)$gRow['audio_path'],
                'cover_art_url' => (string)$gArt,
                'id' => (int)$gRow['id']
            ];
        }
    }
}
?>
    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="site-footer__bg" aria-hidden="true">
            <img src="<?= $basePath ?>assets/images/footer-image.jpg" alt="" loading="lazy">
        </div>
        <div class="site-footer__glow" aria-hidden="true"></div>

        <div class="container-vebra">
            <div class="footer-marquee marquee" data-velocity>
                <div class="marquee__track">
                    <span class="marquee__item">vebra <span class="marquee__sep">✦</span> Sound in its purest form <span class="marquee__sep">✦</span> Nightform out now <span class="marquee__sep">✦</span> On tour 2026 <span class="marquee__sep">✦</span></span>
                    <span class="marquee__item marquee__item--ghost">vebra <span class="marquee__sep">✦</span> Sound in its purest form <span class="marquee__sep">✦</span> Nightform out now <span class="marquee__sep">✦</span> On tour 2026 <span class="marquee__sep">✦</span></span>
                    <span class="marquee__item" aria-hidden="true">vebra <span class="marquee__sep">✦</span> Sound in its purest form <span class="marquee__sep">✦</span> Nightform out now <span class="marquee__sep">✦</span> On tour 2026 <span class="marquee__sep">✦</span></span>
                    <span class="marquee__item marquee__item--ghost" aria-hidden="true">vebra <span class="marquee__sep">✦</span> Sound in its purest form <span class="marquee__sep">✦</span> Nightform out now <span class="marquee__sep">✦</span> On tour 2026 <span class="marquee__sep">✦</span></span>
                </div>
            </div>

            <div class="footer-cta">
                <h2 class="footer-cta__title" data-split="lines">Find your next <em>sound</em></h2>
                <a href="mailto:hello@vebra.com" class="btn btn--solid btn--lg" data-magnetic="0.3">
                    <span class="btn__label">Get in touch <i class="ri-arrow-right-up-line"></i></span>
                </a>
            </div>

            <div class="footer-grid">
                <div class="footer-col footer-col--brand">
                    <a href="<?= $basePath ?>index.php" class="site-logo"><span class="site-logo__word">vebra<span class="site-logo__dot">.</span></span></a>
                    <p class="footer-text">Discover releases, music videos, artists and live experiences in one place.</p>
                    <div class="socials">
                        <a href="https://facebook.com/" target="_blank" rel="noopener" aria-label="Facebook"><i class="ri-facebook-fill"></i></a>
                        <a href="https://x.com/" target="_blank" rel="noopener" aria-label="X / Twitter"><i class="ri-twitter-fill"></i></a>
                        <a href="https://instagram.com/" target="_blank" rel="noopener" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                        <a href="https://youtube.com/" target="_blank" rel="noopener" aria-label="YouTube"><i class="ri-youtube-fill"></i></a>
                        <a href="https://spotify.com/" target="_blank" rel="noopener" aria-label="Spotify"><i class="ri-spotify-fill"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h3 class="footer-col__title">Explore</h3>
                    <div class="footer-links">
                        <a href="<?= $basePath ?>discography.php">Music &amp; Releases</a>
                        <a href="<?= $basePath ?>artists.php">Artists &amp; Band</a>
                        <a href="<?= $basePath ?>video-gallery.php">Videos</a>
                        <a href="<?= $basePath ?>events.php">Live Shows</a>
                    </div>
                </div>

                <div class="footer-col">
                    <h3 class="footer-col__title">Discover</h3>
                    <div class="footer-links">
                        <a href="<?= $basePath ?>news-page.php">News &amp; Stories</a>
                        <a href="<?= $basePath ?>shop-page.php">Shop &amp; Vinyl</a>
                        <a href="<?= $basePath ?>gallery.php">Photo Gallery</a>
                        <a href="<?= $basePath ?>search.php">Search Catalog</a>
                    </div>
                </div>

                <div class="footer-col">
                    <h3 class="footer-col__title">Stay in the loop</h3>
                    <p class="footer-text">Music and updates, without the noise.</p>
                    <a href="mailto:hello@vebra.com" class="link-arrow">Email us <i class="ri-arrow-right-up-line"></i></a>
                </div>
            </div>

            <div class="footer-wordmark-wrap">
                <a href="<?= $basePath ?>index.php" class="footer-wordmark" aria-hidden="true" tabindex="-1">vebra</a>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> VEBRA. All rights reserved.</p>
                <div class="footer-bottom__links">
                    <a href="<?= $basePath ?>cart.php">Cart (<?= vebra_store_cart_count() ?>)</a>
                    <a href="<?= $basePath ?>order-lookup.php">Track an order</a>
                    <a href="<?= $basePath ?>store-policy.php?policy=shipping">Shipping</a>
                    <a href="<?= $basePath ?>store-policy.php?policy=returns">Returns</a>
                    <a href="<?= $basePath ?>store-policy.php?policy=privacy">Privacy</a>
                    <a href="<?= $basePath ?>store-policy.php?policy=terms">Terms</a>
                    <a href="<?= $basePath ?>sitemap.php">Sitemap</a>
                    <a href="<?= $basePath ?>contact.php">Contact</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="<?= $basePath ?>admin/index.php">Admin Panel</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <?php if ($loadAmplitude && empty($hasPageBottomPlayer)): ?>
    <div class="bottom-player" id="vebra-docked-bottom-player">
        <div class="bottom-player__rail"><span class="bottom-player__rail-fill"></span></div>
        <div class="bottom-player__inner">
            <div class="bottom-player__track">
                <div class="bottom-player__cover">
                    <img data-amplitude-song-info="cover_art_url" src="<?= $basePath ?>assets/images/album-1.jpg" alt="Track artwork">
                </div>
                <div class="bottom-player__meta">
                    <span class="bottom-player__title" data-amplitude-song-info="name">Select a track</span>
                    <span class="bottom-player__artist" data-amplitude-song-info="artist">VEBRA</span>
                </div>
            </div>

            <div class="bottom-player__controls">
                <button type="button" class="player-btn amplitude-prev" aria-label="Previous track"><i class="ri-skip-back-fill"></i></button>
                <button type="button" class="player-btn player-btn--play amplitude-play-pause" aria-label="Play or pause">
                    <i class="ri-play-fill icon-play"></i>
                    <i class="ri-pause-fill icon-pause"></i>
                </button>
                <button type="button" class="player-btn player-btn--next amplitude-next" aria-label="Next track"><i class="ri-skip-forward-fill"></i></button>
            </div>

            <div class="bottom-player__side">
                <span class="bottom-player__time"><span data-role="current-time">0:00</span> / <span data-role="duration">0:00</span></span>
                <div class="player-volume">
                    <button type="button" class="player-volume__btn" id="vebra-mute-btn" aria-label="Mute or unmute" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;display:inline-flex;align-items:center;">
                        <i class="ri-volume-down-line" id="vebra-volume-icon" aria-hidden="true"></i>
                    </button>
                    <input type="range" class="amplitude-volume-slider player-range" data-role="volume" aria-label="Volume" value="70" min="0" max="100">
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

        <!-- VEBRA PERSISTENT VIDEO MINI PLAYER -->
    <div id="vebra-video-mini" class="vebra-video-mini" aria-label="Floating video player" hidden>
        <div class="vebra-video-mini__screen" id="vebra-video-mini-screen"></div>

        <div class="vebra-video-mini__bar">
            <span class="vebra-video-mini__label">NOW PLAYING</span>

            <div class="vebra-video-mini__controls">
                <button type="button" id="vebra-video-mini-play" aria-label="Play or pause video">
                    <i class="ri-pause-fill"></i>
                </button>

                <button type="button" id="vebra-video-mini-expand" aria-label="Expand video">
                    <i class="ri-expand-up-down-line"></i>
                </button>

                <button type="button" id="vebra-video-mini-close" aria-label="Close video">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
    </div>
    <!-- END VEBRA PERSISTENT VIDEO MINI PLAYER -->

    <!-- JS Scripts -->
    <script src="<?= $basePath ?>assets/js/vendor/gsap.min.js"></script>
    <script src="<?= $basePath ?>assets/js/vendor/ScrollTrigger.min.js"></script>
    <script src="<?= $basePath ?>assets/js/vendor/SplitText.min.js"></script>
    <script src="<?= $basePath ?>assets/js/vendor/lenis.min.js"></script>
    <script src="<?= $basePath ?>assets/js/vendor/swiper-bundle.min.js"></script>
    <?php if ($loadAmplitude): ?>
    <script src="<?= $basePath ?>assets/js/vendor/amplitude.min.js"></script>
    <?php endif; ?>
    <script>
    window.vebraGlobalSongs = <?= json_encode($vebraGlobalSongs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="<?= $basePath ?>assets/js/main.js"></script>
    <script src="<?= $basePath ?>assets/js/video-persistent.js"></script>
    
    <script>
    // Universal dropdown accessibility & touch handling
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownItems = document.querySelectorAll('.nav__item.has-children');
        dropdownItems.forEach(function(item) {
            var link = item.querySelector('.nav__link');
            if (!link) return;

            // Touch / click handling for mobile and touch devices
            link.addEventListener('click', function(e) {
                var isTouch = ('ontouchstart' in window || navigator.maxTouchPoints > 0 || window.matchMedia('(pointer: coarse)').matches);
                if (isTouch) {
                    if (!item.classList.contains('is-open')) {
                        e.preventDefault();
                        dropdownItems.forEach(function(other) { if (other !== item) other.classList.remove('is-open'); });
                        item.classList.add('is-open');
                    }
                }
            });

            // Keep open while pointer is inside item or submenu on desktop
            var closeTimer = null;
            item.addEventListener('mouseenter', function() {
                if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
                item.classList.add('is-open');
            });
            item.addEventListener('mouseleave', function() {
                closeTimer = setTimeout(function() {
                    item.classList.remove('is-open');
                }, 150);
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav__item.has-children')) {
                dropdownItems.forEach(function(item) { item.classList.remove('is-open'); });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownItems.forEach(function(item) { item.classList.remove('is-open'); });
            }
        });
    });
    </script>
   
    <?= $extraScripts ?>
</body>
</html>
