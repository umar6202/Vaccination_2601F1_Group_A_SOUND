(function () {
    "use strict";

    /* ======================================================================
       01 — UTILITIES
       ====================================================================== */

    var qs = function (sel, ctx) {
        return (ctx || document).querySelector(sel);
    };
    var qsa = function (sel, ctx) {
        return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
    };

    var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var isTouch = window.matchMedia("(hover: none), (pointer: coarse)").matches;
    var hasGsap = typeof window.gsap !== "undefined";

    var hasScrollTrigger =
    typeof window.ScrollTrigger !== "undefined";

function refreshScrollTriggers() {
    if (
        typeof window.gsap !== "undefined" &&
        typeof window.ScrollTrigger !== "undefined"
    ) {
        window.ScrollTrigger.refresh();
    }
}

    /** Run fn on the next animation frame, coalescing repeat calls. */
    function raf(fn) {
        var queued = false;
        return function () {
            if (queued) return;
            queued = true;
            requestAnimationFrame(function () {
                queued = false;
                fn();
            });
        };
    }

    function debounce(fn, wait) {
        var t;
        return function () {
            var args = arguments,
                self = this;
            clearTimeout(t);
            t = setTimeout(function () {
                fn.apply(self, args);
            }, wait || 200);
        };
    }

    /** Seconds -> "m:ss" */
    function formatTime(seconds) {
        if (!isFinite(seconds) || seconds < 0) seconds = 0;
        var m = Math.floor(seconds / 60);
        var s = Math.floor(seconds % 60);
        return m + ":" + (s < 10 ? "0" : "") + s;
    }

    var Echora = (window.Echora = {
        lenis: null,
        sliders: {},
        reducedMotion: prefersReducedMotion,
        isTouch: isTouch,
    });

    /* ======================================================================
       02 — SMOOTH SCROLL (Lenis)
       Lenis scrolls the window, so ScrollTrigger needs no scrollerProxy —
       just a shared ticker and an update on every scroll event.
       ====================================================================== */

    function initSmoothScroll() {
        if (prefersReducedMotion || isTouch || typeof window.Lenis === "undefined" || !hasGsap) return;

        var lenis = new Lenis({
            duration: 1.1,
            easing: function (t) {
                return Math.min(1, 1.001 - Math.pow(2, -10 * t));
            },
            smoothWheel: true,
            syncTouch: false,
            touchMultiplier: 1.6,
        });

        lenis.on("scroll", ScrollTrigger.update);

        gsap.ticker.add(function (time) {
            lenis.raf(time * 1000);
        });
        gsap.ticker.lagSmoothing(0);

        Echora.lenis = lenis;

        // In-page anchors route through Lenis so easing stays consistent.
        document.addEventListener("click", function (e) {
            var link = e.target.closest('a[href^="#"]');
            if (!link) return;
            var id = link.getAttribute("href");
            if (!id || id === "#" || id.length < 2) return;
            var target = qs(id);
            if (!target) return;
            e.preventDefault();
            lenis.scrollTo(target, { offset: -80 });
        });
    }

    /** Pause/resume page scrolling for overlays. Works with and without Lenis. */
    function lockScroll(locked) {
        if (Echora.lenis) {
            locked ? Echora.lenis.stop() : Echora.lenis.start();
        }
        document.body.classList.toggle("is-locked", !!locked);
    }
    Echora.lockScroll = lockScroll;

    /* ======================================================================
       03 — PRELOADER / PAGE REVEAL
       Fires `echora:reveal` when the page is visible so intro timelines can
       start. Always resolves, even if assets stall.
       ====================================================================== */

    function initPreloader() {
        var el = qs(".preloader");
        var reveal = function () {
            document.documentElement.classList.remove("is-loading");
            window.dispatchEvent(new CustomEvent("echora:reveal"));
            refreshScrollTriggers();
        };

        if (!el || prefersReducedMotion || !hasGsap) {
            if (el) el.remove();
            reveal();
            return;
        }

        var word = qs(".preloader__word", el);
        var count = qs(".preloader__count", el);
if (count) count.style.display = "none";
        var fill = qs(".preloader__bar-fill", el);
        var tagline = qs(".preloader__tagline", el);
        var panels = qsa(".preloader__panel", el);

        // Repeat visits get a shortened intro.
        var seen = sessionStorage.getItem("echora-loaded") === "1";
        var speed = seen ? 0.55 : 1;

        // Only split once the display font is in — otherwise the wordmark
        // reflows mid-animation and GSAP warns about it.
        var chars = [];
        var fontsReady = !document.fonts || document.fonts.status === "loaded";
        if (word && fontsReady && typeof SplitText !== "undefined") {
            var split = SplitText.create(word, { type: "chars", charsClass: "split-char" });
            chars = split.chars;
        }

        var progress = { value: 0 };
        var tl = gsap.timeline({
            onComplete: function () {
                sessionStorage.setItem("echora-loaded", "1");
                el.classList.add("is-done");
                el.remove();
                reveal();
            },
        });

        if (chars.length) {
            tl.from(chars, {
                yPercent: 118,
                duration: 0.9 * speed,
                ease: "expo.out",
                stagger: 0.035 * speed,
            });
        } else if (word) {
            // Fallback when the font hasn't landed yet: animate the whole word.
            tl.from(word, { yPercent: 118, duration: 0.9 * speed, ease: "expo.out" });
        }

        if (tagline) {
            tl.to(tagline, { opacity: 1, duration: 0.5 * speed, ease: "power2.out" }, "-=0.5");
        }

        tl.to(
            progress,
            {
                value: 100,
                duration: 1.2 * speed,
                ease: "power2.inOut",
                onUpdate: function () {
                    var v = Math.round(progress.value);
                    if (count) count.textContent = (v < 10 ? "0" : "") + v;
                    if (fill) gsap.set(fill, { scaleX: progress.value / 100 });
                },
            },
            "-=0.7"
        );

        if (chars.length) {
            tl.to(chars, {
                yPercent: -118,
                duration: 0.6 * speed,
                ease: "expo.in",
                stagger: 0.02 * speed,
            });
        }

        tl.to([count, tagline].filter(Boolean), { opacity: 0, duration: 0.3 * speed }, "<");

        // Curtain: panels wipe up in sequence.
        if (panels.length) {
            tl.to(
                panels,
                {
                    scaleY: 0,
                    transformOrigin: "top center",
                    duration: 0.9 * speed,
                    ease: "expo.inOut",
                    stagger: 0.06 * speed,
                },
                "-=0.2"
            );
        }

        // Safety net: never trap the page behind a stalled timeline.
        setTimeout(function () {
            if (document.body.contains(el)) {
                tl.progress(1);
            }
        }, 6000);
    }

    /* ======================================================================
       04 — HEADER + OVERLAY MENU
       ====================================================================== */

    function initHeader() {
        var header = qs(".site-header");
        if (!header) return;

        var lastY = window.scrollY;
        var threshold = 12;

        var onScroll = raf(function () {
            var y = window.scrollY;
            header.classList.toggle("is-scrolled", y > 40);

            // Only hide once past the hero and while the menu is closed.
            if (!header.classList.contains("is-menu-open")) {
                if (Math.abs(y - lastY) > threshold) {
                    header.classList.toggle("is-hidden", y > lastY && y > 300);
                    lastY = y;
                }
            }
        });

        window.addEventListener("scroll", onScroll, { passive: true });
        onScroll();

        initOverlayMenu(header);
    }

    function initOverlayMenu(header) {
        var menu = qs(".overlay-menu");
        var trigger = qs(".menu-trigger");
        if (!menu || !trigger) return;

        var items = qsa(".overlay-nav__item", menu);
        var links = qsa(".overlay-nav__link", menu);
        var aside = qs(".overlay-aside", menu);
        var isOpen = false;
        var tl = null;

        function buildTimeline() {
            if (!hasGsap) return null;
            var t = gsap.timeline({ paused: true });

            t.to(menu, {
                clipPath: "inset(0% 0% 0% 0%)",
                duration: 0.9,
                ease: "expo.inOut",
            });

            if (links.length) {
                t.from(
                    links,
                    {
                        yPercent: 110,
                        opacity: 0,
                        duration: 0.8,
                        ease: "expo.out",
                        stagger: 0.055,
                    },
                    "-=0.45"
                );
            }

            if (aside) {
                t.from(aside, { opacity: 0, y: 30, duration: 0.7, ease: "power3.out" }, "-=0.5");
            }

            return t;
        }

        function open() {
            isOpen = true;
            menu.classList.add("is-open");
            menu.setAttribute("aria-hidden", "false");
            trigger.classList.add("is-active");
            trigger.setAttribute("aria-expanded", "true");
            header.classList.add("is-menu-open");
            header.classList.remove("is-hidden");
            lockScroll(true);

            if (hasGsap) {
                if (!tl) tl = buildTimeline();
                tl.timeScale(1).play(0);
            } else {
                menu.style.clipPath = "inset(0 0 0 0)";
            }
        }

        function close() {
            isOpen = false;
            trigger.classList.remove("is-active");
            trigger.setAttribute("aria-expanded", "false");
            header.classList.remove("is-menu-open");
            lockScroll(false);

            var finish = function () {
                menu.classList.remove("is-open");
                menu.setAttribute("aria-hidden", "true");
                items.forEach(function (i) {
                    i.classList.remove("is-open");
                });
            };

            if (hasGsap && tl) {
                tl.timeScale(1.9).reverse().eventCallback("onReverseComplete", finish);
            } else {
                menu.style.clipPath = "";
                finish();
            }
        }

        trigger.addEventListener("click", function () {
            isOpen ? close() : open();
        });

        // Close on navigation and on Escape.
        menu.addEventListener("click", function (e) {
            if (e.target.closest("a[href]") && !e.target.closest(".overlay-nav__toggle")) close();
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && isOpen) close();
        });

        // Expandable sub-menus. A group with no page of its own (Pages) opens
        // from its label as well as from the toggle.
        qsa(".overlay-nav__toggle, .overlay-nav__link--group", menu).forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                var item = btn.closest(".overlay-nav__item");
                var willOpen = !item.classList.contains("is-open");
                items.forEach(function (i) {
                    i.classList.remove("is-open");
                    var t = qs(".overlay-nav__toggle", i);
                    if (t) t.setAttribute("aria-expanded", "false");
                });
                item.classList.toggle("is-open", willOpen);
                var toggle = qs(".overlay-nav__toggle", item);
                if (toggle) toggle.setAttribute("aria-expanded", String(willOpen));
            });
        });
    }

    /* ======================================================================
       05 — TEXT REVEAL (SplitText)
       `data-split="lines|words|chars"` — defaults to lines with a mask.
       ====================================================================== */

    function initTextReveal() {
        if (!hasGsap || typeof SplitText === "undefined") {
            qsa("[data-split]").forEach(function (el) {
                el.classList.add("is-split-ready");
            });
            return;
        }

        var targets = qsa("[data-split]");
        if (!targets.length) return;

        if (prefersReducedMotion) {
            targets.forEach(function (el) {
                el.classList.add("is-split-ready");
            });
            return;
        }

        targets.forEach(function (el) {
            var mode = el.getAttribute("data-split") || "lines";
            var type = mode === "chars" ? "chars" : mode === "words" ? "words,lines" : "lines";
            var delay = parseFloat(el.getAttribute("data-delay")) || 0;
            var isHeroText = el.hasAttribute("data-split-hero");

            // autoSplit re-splits on font load and container resize, which
            // matters because every heading size is a clamp().
            SplitText.create(el, {
                type: type,
                mask: mode === "chars" ? "chars" : "lines",
                linesClass: "split-line",
                autoSplit: true,
                onSplit: function (self) {
                    el.classList.add("is-split-ready");
                    var parts = mode === "chars" ? self.chars : mode === "words" ? self.words : self.lines;
                    if (!parts || !parts.length) return;

                    var vars = {
                        yPercent: 110,
                        duration: 1,
                        ease: "expo.out",
                        stagger: mode === "chars" ? 0.02 : 0.09,
                        delay: delay,
                    };

                    // Hero copy animates on page reveal; everything else on scroll.
                    if (isHeroText) {
                        return gsap.from(parts, vars);
                    }

                    vars.scrollTrigger = {
                        trigger: el,
                        start: "top 88%",
                        once: true,
                    };
                    return gsap.from(parts, vars);
                },
            });
        });
    }

    /* ======================================================================
       06 — SCROLL REVEALS
       `data-reveal="up|down|left|right|scale|fade|mask|mask-left"`
       `data-delay`, `data-stagger` on a parent with [data-reveal-group]
       ====================================================================== */

    function initReveals() {
        if (!hasGsap) return;

        var items = qsa("[data-reveal]");
        if (!items.length) return;

        if (prefersReducedMotion) {
            gsap.set(items, { clearProps: "all" });
            return;
        }

        items.forEach(function (el) {
            var type = el.getAttribute("data-reveal") || "up";
            var delay = parseFloat(el.getAttribute("data-delay")) || 0;
            var start = el.getAttribute("data-start") || "top 88%";

            var vars = {
                duration: 1,
                delay: delay,
                ease: "expo.out",
                overwrite: "auto",
                scrollTrigger: { trigger: el, start: start, once: true },
                onComplete: function () {
                    el.classList.add("is-revealed");
                },
            };

            if (type === "mask" || type === "mask-left") {
                vars.clipPath = "inset(0% 0% 0% 0%)";
                vars.duration = 1.3;

                var inner = el.querySelector("img, video, .media__inner");
                if (inner) {
                    gsap.to(inner, {
                        scale: 1,
                        duration: 1.6,
                        ease: "expo.out",
                        delay: delay,
                        scrollTrigger: { trigger: el, start: start, once: true },
                    });
                }
            } else {
                vars.opacity = 1;
                vars.x = 0;
                vars.y = 0;
                vars.scale = 1;
            }

            gsap.to(el, vars);
        });
    }

    /** Staggered entrances for card grids: [data-reveal-group] on the parent. */
    function initRevealGroups() {
        if (!hasGsap || prefersReducedMotion) return;

        qsa("[data-reveal-group]").forEach(function (group) {
            var children = qsa(":scope > *", group).filter(function (c) {
                return !c.hasAttribute("data-reveal");
            });
            if (!children.length) return;

            var stagger = parseFloat(group.getAttribute("data-stagger")) || 0.09;

            gsap.from(children, {
                y: 48,
                opacity: 0,
                duration: 1,
                ease: "expo.out",
                stagger: stagger,
                scrollTrigger: { trigger: group, start: "top 85%", once: true },
            });
        });
    }

    /* ======================================================================
       07 — PARALLAX + SCROLL VELOCITY
       ====================================================================== */

    function initParallax() {
        if (!hasGsap || prefersReducedMotion) return;

        var mm = gsap.matchMedia();

        mm.add("(min-width: 992px)", function () {
            // Scroll-linked parallax on images and decorative layers.
            qsa("[data-parallax-speed]").forEach(function (el) {
                var speed = parseFloat(el.getAttribute("data-parallax-speed")) || 0.2;
                gsap.to(el, {
                    yPercent: speed * 100,
                    ease: "none",
                    scrollTrigger: {
                        trigger: el.closest("[data-parallax-scope]") || el.parentElement,
                        start: "top bottom",
                        end: "bottom top",
                        scrub: true,
                    },
                });
            });

            // Mouse-follow parallax for layered hero compositions.
            qsa("[data-mouse-scope]").forEach(function (scope) {
                var layers = qsa("[data-mouse-parallax]", scope);
                if (!layers.length) return;

                var setters = layers.map(function (layer) {
                    var depth = parseFloat(layer.getAttribute("data-mouse-parallax")) || 0.05;
                    return {
                        depth: depth,
                        x: gsap.quickTo(layer, "x", { duration: 0.9, ease: "power3" }),
                        y: gsap.quickTo(layer, "y", { duration: 0.9, ease: "power3" }),
                    };
                });

                scope.addEventListener("mousemove", function (e) {
                    var r = scope.getBoundingClientRect();
                    var dx = e.clientX - (r.left + r.width / 2);
                    var dy = e.clientY - (r.top + r.height / 2);
                    setters.forEach(function (s) {
                        s.x(dx * s.depth);
                        s.y(dy * s.depth);
                    });
                });

                scope.addEventListener("mouseleave", function () {
                    setters.forEach(function (s) {
                        s.x(0);
                        s.y(0);
                    });
                });
            });

            return function () {
                gsap.set("[data-parallax-speed], [data-mouse-parallax]", { clearProps: "transform" });
            };
        });

        initScrollVelocity();
    }

    /** Marquees lean into the scroll direction; speed tracks scroll velocity. */
    function initScrollVelocity() {
        var marquees = qsa("[data-velocity]");
        if (!marquees.length || !hasGsap || prefersReducedMotion) return;

        var tracks = marquees.map(function (m) {
            return qs(".marquee__track", m) || m;
        });

        ScrollTrigger.create({
            onUpdate: function (self) {
                var v = gsap.utils.clamp(-4, 4, self.getVelocity() / 320);
                tracks.forEach(function (track) {
                    gsap.to(track, {
                        skewX: v,
                        duration: 0.5,
                        ease: "power3.out",
                        overwrite: true,
                    });
                });
            },
        });
    }

    /* ======================================================================
       08 — MAGNETIC BUTTONS
       ====================================================================== */

    function initMagnetic() {
        if (!hasGsap || isTouch || prefersReducedMotion) return;

        qsa("[data-magnetic]").forEach(function (el) {
            var strength = parseFloat(el.getAttribute("data-magnetic")) || 0.35;
            var label = qs(".btn__label", el);

            var xTo = gsap.quickTo(el, "x", { duration: 0.6, ease: "power3" });
            var yTo = gsap.quickTo(el, "y", { duration: 0.6, ease: "power3" });
            var lxTo = label ? gsap.quickTo(label, "x", { duration: 0.7, ease: "power3" }) : null;
            var lyTo = label ? gsap.quickTo(label, "y", { duration: 0.7, ease: "power3" }) : null;

            el.addEventListener("mousemove", function (e) {
                var r = el.getBoundingClientRect();
                var dx = e.clientX - (r.left + r.width / 2);
                var dy = e.clientY - (r.top + r.height / 2);
                xTo(dx * strength);
                yTo(dy * strength);
                // The label trails slightly for depth.
                if (lxTo) {
                    lxTo(dx * strength * 0.4);
                    lyTo(dy * strength * 0.4);
                }
            });

            el.addEventListener("mouseleave", function () {
                xTo(0);
                yTo(0);
                if (lxTo) {
                    lxTo(0);
                    lyTo(0);
                }
            });
        });
    }

 
/* ======================================================================
   09 — CUSTOM CURSOR
   Jelly cursor with smooth trailing ring.
   Shows pink PLAY hover on song and video cards.
   ====================================================================== */

function initCursor() {

    // 1. Get cursor elements from the page
    var dot = qs(".cursor");
    var ring = qs(".cursor-ring");

    // Stop if cursor elements are missing or device does not support it
    if (!dot || !ring || isTouch || prefersReducedMotion || !hasGsap) {
        return;
    }

    // 2. Get the text label inside the cursor ring
    var label = qs(".cursor-ring__label", ring);

    // 3. Smooth movement settings using GSAP
    var dotX = gsap.quickTo(dot, "x", {
        duration: 0.12,
        ease: "power3"
    });

    var dotY = gsap.quickTo(dot, "y", {
        duration: 0.12,
        ease: "power3"
    });

    var ringX = gsap.quickTo(ring, "x", {
        duration: 0.5,
        ease: "power3"
    });

    var ringY = gsap.quickTo(ring, "y", {
        duration: 0.5,
        ease: "power3"
    });

    var visible = false;

    // 4. Follow mouse movement and reveal cursor
    window.addEventListener("mousemove", function (e) {
        dotX(e.clientX);
        dotY(e.clientY);
        ringX(e.clientX);
        ringY(e.clientY);

        if (!visible) {
            visible = true;

            gsap.to([dot, ring], {
                opacity: 1,
                duration: 0.3
            });
        }
    });

    // 5. Hide cursor when mouse leaves the page
    document.addEventListener("mouseleave", function () {
        visible = false;

        gsap.to([dot, ring], {
            opacity: 0,
            duration: 0.2
        });
    });

    // 6. Song and video card hover detection
    // Add more selectors here if other card types need PLAY hover
    var playSelectors = [
        "[data-cursor]",
        ".album-card",
        ".video-card",
        ".vebra-single-video-banner",
        ".mini-item"
    ].join(", ");

    // 7. Activate pink jelly and display PLAY label
    document.addEventListener("mouseover", function (e) {
        var target = e.target.closest(playSelectors);

        if (target) {
            ring.classList.add("is-active");
            ring.classList.remove("is-link");

            if (label) {
                label.textContent =
                    target.getAttribute("data-cursor") || "PLAY";
            }

            return;
        }

        // 8. Normal links and buttons get a smaller ring
        var link = e.target.closest(
            "a, button, input, textarea, select, [role='button']"
        );

        if (link) {
            ring.classList.remove("is-active");
            ring.classList.add("is-link");

            if (label) {
                label.textContent = "";
            }
        }
    });

    // 9. Remove hover effects when leaving a target
    document.addEventListener("mouseout", function (e) {
        var target = e.target.closest(playSelectors);
        var next = e.relatedTarget;

        if (target && (!next || !target.contains(next))) {
            ring.classList.remove("is-active");

            if (label) {
                label.textContent = "";
            }
        }

        // Remove normal link hover state
        var link = e.target.closest(
            "a, button, input, textarea, select, [role='button']"
        );

        if (link && (!next || !link.contains(next))) {
            ring.classList.remove("is-link");
        }
    });

} // END initCursor — SECTION 09
 

    /* ======================================================================
       10 — SLIDERS (Swiper 11)
       One config map keyed by `data-slider`. Markup supplies the nav/pagination
       elements via `data-slider-scope` on a shared ancestor.
       ====================================================================== */

    var SLIDER_PRESETS = {
        hero: {
            effect: "fade",
            fadeEffect: { crossFade: true },
            speed: 1200,
            loop: true,
            autoplay: { delay: 5600, disableOnInteraction: false },
            allowTouchMove: true,
        },
        "hero-carousel": {
            slidesPerView: 1.15,
            centeredSlides: true,
            spaceBetween: 16,
            speed: 900,
            loop: true,
            grabCursor: true,
            breakpoints: {
                768: { slidesPerView: 1.6, spaceBetween: 24 },
                1200: { slidesPerView: 2.2, spaceBetween: 32 },
            },
        },
        disco: {
            slidesPerView: 1.2,
            spaceBetween: 20,
            speed: 800,
            grabCursor: true,
            breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 24 },
                992: { slidesPerView: 3, spaceBetween: 28 },
                1400: { slidesPerView: 4, spaceBetween: 32 },
            },
        },
        disco2: {
            slidesPerView: 1.15,
            spaceBetween: 20,
            speed: 800,
            grabCursor: true,
            breakpoints: {
                768: { slidesPerView: 2.2, spaceBetween: 28 },
                1200: { slidesPerView: 3.2, spaceBetween: 32 },
            },
        },
        testimonial: {
            effect: "fade",
            fadeEffect: { crossFade: true },
            speed: 900,
            loop: true,
            autoHeight: true,
        },
        testimonials: {
            effect: "fade",
            fadeEffect: { crossFade: true },
            speed: 900,
            loop: true,
            autoHeight: true,
        },
        members: {
            slidesPerView: 1.25,
            spaceBetween: 20,
            speed: 800,
            grabCursor: true,
            breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 24 },
                992: { slidesPerView: 3, spaceBetween: 28 },
                1400: { slidesPerView: 4, spaceBetween: 30 },
            },
        },
        news: {
            slidesPerView: 1.1,
            spaceBetween: 20,
            speed: 800,
            grabCursor: true,
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 28 },
                1200: { slidesPerView: 3, spaceBetween: 32 },
            },
        },
        video: {
            slidesPerView: 1,
            spaceBetween: 24,
            speed: 900,
            centeredSlides: true,
            breakpoints: {
                992: { slidesPerView: 1.5, spaceBetween: 32 },
            },
        },
        gallery: {
            slidesPerView: 1.2,
            spaceBetween: 16,
            speed: 800,
            grabCursor: true,
            breakpoints: {
                768: { slidesPerView: 2.4, spaceBetween: 24 },
                1200: { slidesPerView: 3.4, spaceBetween: 28 },
            },
        },
        products: {
            slidesPerView: 1.3,
            spaceBetween: 20,
            speed: 800,
            grabCursor: true,
            breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 24 },
                992: { slidesPerView: 3, spaceBetween: 28 },
                1400: { slidesPerView: 4, spaceBetween: 30 },
            },
        },
    };

    function initSliders() {
        if (typeof Swiper === "undefined") return;

        qsa("[data-slider]").forEach(function (el, i) {
            if (el.swiper) return;
            var name = el.getAttribute("data-slider");
            var preset = SLIDER_PRESETS[name];
            if (!preset) return;

            var scope = el.closest("[data-slider-scope]") || el.parentElement;
            var config = Object.assign({}, preset);

            // Autoplay is opt-out per instance.
            if (el.hasAttribute("data-no-autoplay")) delete config.autoplay;

            var next = qs(".js-slider-next", scope);
            var prev = qs(".js-slider-prev", scope);
            if (next && prev) {
                config.navigation = { nextEl: next, prevEl: prev };
            }

            var pagination = qs(".js-slider-pagination", scope);
            if (pagination) {
                var numbered = pagination.classList.contains("pagination-numbers");
                config.pagination = {
                    el: pagination,
                    clickable: true,
                    bulletClass: numbered ? "pagination-number" : "swiper-pagination-bullet",
                    bulletActiveClass: "swiper-pagination-bullet-active",
                    renderBullet: numbered
                        ? function (index, className) {
                              var n = index + 1;
                              return (
                                  '<button type="button" class="' +
                                  className +
                                  '" aria-label="Slide ' +
                                  n +
                                  '">' +
                                  (n < 10 ? "0" + n : n) +
                                  "</button>"
                              );
                          }
                        : undefined,
                };
            }

            var scrollbar = qs(".js-slider-scrollbar", scope);
            if (scrollbar) {
                config.scrollbar = { el: scrollbar, draggable: true };
            }

            // Fraction counter + autoplay progress rail.
            var current = qs(".slider-counter__current", scope);
            var total = qs(".slider-counter__total", scope);
            var progressFill = qs(".slider-progress__fill", scope);

            config.on = {
                init: function (sw) {
                    if (total) {
                        var n = sw.slides.length - (config.loop ? sw.loopedSlides * 2 || 0 : 0);
                        total.textContent = pad(el.querySelectorAll(".swiper-slide:not(.swiper-slide-duplicate)").length || n);
                    }
                    if (current) current.textContent = pad(sw.realIndex + 1);
                },
                slideChange: function (sw) {
                    if (current) current.textContent = pad(sw.realIndex + 1);
                },
                autoplayTimeLeft: function (sw, time, progress) {
                    if (progressFill) progressFill.style.setProperty("--slider-progress", 1 - progress);
                },
            };

            var swiper = new Swiper(el, config);
            Echora.sliders[name + "-" + i] = swiper;

            // Pause native videos when a slide leaves the viewport.
            if (el.querySelector("video")) {
                swiper.on("slideChange", function () {
                    qsa("video", el).forEach(function (v) {
                        if (!v.paused) {
                            v.pause();
                            var frame = v.closest(".video-frame");
                            if (frame) frame.classList.remove("is-playing");
                        }
                    });
                });
            }
        });
    }

    function pad(n) {
        return n < 10 ? "0" + n : String(n);
    }

 
/* ======================================================================
   11 — MUSIC PLAYERS (AmplitudeJS)
   VEBRA FIX: card play/pause, progress, playlist, dedicated player
   ====================================================================== */

function initPlayers() {
    if (typeof Amplitude === "undefined") return;

    var section = qs(".music-player");
    var bottom = qs(".bottom-player");

    if (!section && !bottom) return;

    var host = section || bottom;
    var rows = qsa(".list-song", host);

    if (!rows.length && bottom) {
        rows = qsa(".list-song", bottom);
    }

    var songs = [];

    if (window.vebraGlobalSongs && window.vebraGlobalSongs.length) {
        songs = window.vebraGlobalSongs.slice();
    } else if (rows.length) {
        songs = rows.map(function(row) {
            return {
                name: row.getAttribute("data-title") || "",
                artist: row.getAttribute("data-artist") || "",
                album: row.getAttribute("data-album") || "",
                url: row.getAttribute("data-url") || "",
                cover_art_url: row.getAttribute("data-cover") || "",
                id: row.getAttribute("data-id") || ""
            };
        });
    }

    if (!songs.length) return;

    /* --------------------------------------------------
       HELPERS
       -------------------------------------------------- */

    function normalizeUrl(url) {
        if (!url) return "";

        try {
            return new URL(url, window.location.href).href;
        } catch (e) {
            return String(url);
        }
    }

    function getAudio() {
        try {
            return Amplitude.getAudio();
        } catch (e) {
            return null;
        }
    }

    function isPlaying() {
        var audio = getAudio();

        if (audio) {
            return !audio.paused && !audio.ended;
        }

        return Amplitude.getPlayerState() === "playing";
    }

    function getActiveSong() {
        var all = Amplitude.getSongs() || [];
        var index = Number(Amplitude.getActiveIndex());

        return Amplitude.getActiveSongMetadata() || all[index] || null;
    }

    function getProgress() {
        var value = Number(Amplitude.getSongPlayedPercentage());

        if (!isFinite(value)) value = 0;

        return Math.max(0, Math.min(100, value));
    }

   function showBottomPlayer() {
    if (bottom) {
        bottom.classList.add("is-visible");
        document.body.classList.add("has-bottom-player");

        bottom.style.transform = "translate3d(0, 0, 0)";
        bottom.style.opacity = "1";
        bottom.style.visibility = "visible";
    }
}

    /* --------------------------------------------------
       CARD PLAY / PAUSE + PROGRESS SYNC
       -------------------------------------------------- */

    function syncTrackCards() {
        var activeSong = getActiveSong();
        var activeUrl = activeSong ? normalizeUrl(activeSong.url) : "";
        var activeId = activeSong ? String(activeSong.id || "") : "";
        var playing = isPlaying();
        var progress = getProgress();

        // Query every time so dynamically added cards also work.
        qsa(".vebra-music-card").forEach(function(card) {
            var buttons = qsa(
                ".vebra-play-card-btn, [data-play-song], .album-card__play, .vebra-song-play",
                card
            );
            if (card.classList.contains("vebra-play-card-btn") || card.hasAttribute("data-url")) {
                buttons.push(card);
            }

            var sameSong = false;
            var cardUrl = normalizeUrl(card.getAttribute("data-url"));
            var cardId = String(card.getAttribute("data-id") || "");

            if (activeSong) {
                if (cardId && activeId && cardId === activeId) {
                    sameSong = true;
                } else if (cardUrl && activeUrl && cardUrl === activeUrl) {
                    sameSong = true;
                }
            }

            buttons.forEach(function(btn) {
                var btnUrl = normalizeUrl(btn.getAttribute("data-url"));
                var btnId = String(btn.getAttribute("data-id") || "");

                if (activeSong) {
                    if (btnId && activeId && btnId === activeId) {
                        sameSong = true;
                    } else if (btnUrl && activeUrl && btnUrl === activeUrl) {
                        sameSong = true;
                    }
                }
            });

            var active = sameSong && playing;

            card.classList.toggle("is-playing", active);
            card.classList.toggle("is-active-track", sameSong);

            // Progress line
            var progressFill = qs(".vebra-track-progress-fill", card);

            if (progressFill) {
                progressFill.style.width = sameSong
                    ? progress + "%"
                    : "0%";
            }

            // Every play button on this card
            buttons.forEach(function(btn) {
                btn.classList.toggle("is-playing", active);
                btn.setAttribute("aria-label", active ? "Pause" : "Play");
                btn.setAttribute("aria-pressed", String(active));

                var icon = qs("i", btn);

                if (icon) {
                    icon.classList.remove(
                        "ri-play-fill",
                        "ri-pause-fill"
                    );

                    icon.classList.add(
                        active ? "ri-pause-fill" : "ri-play-fill"
                    );
                }

                // Update text on the larger PLAY button
                if (btn.classList.contains("vebra-music-button-primary")) {
                    Array.prototype.forEach.call(btn.childNodes, function(node) {
                        if (
                            node.nodeType === 3 &&
                            node.textContent.trim()
                        ) {
                            node.textContent = active ? " PAUSE" : " PLAY";
                        }
                    });
                }
            });
        });
    }

    /* --------------------------------------------------
       PLAYER UI SYNC
       -------------------------------------------------- */

    function onPlayState() {
        var playing = isPlaying();

        if (section) {
            section.classList.toggle("is-playing", playing);
        }

        if (bottom) {
            bottom.classList.toggle("is-playing", playing);
        }

        syncTrackCards();
    }

    function onTimeUpdate() {
        var progress = getProgress();

        qsa(".player-range[data-role='progress']").forEach(function(input) {
            input.value = progress;
            input.style.setProperty("--progress", progress + "%");
        });

        var railFill = qs(".bottom-player__rail-fill");

        if (railFill) {
            railFill.style.setProperty("--progress", progress + "%");
        }

        var audio = getAudio();

        if (audio) {
            qsa("[data-role='current-time']").forEach(function(el) {
                el.textContent = formatTime(audio.currentTime);
            });

            qsa("[data-role='duration']").forEach(function(el) {
                el.textContent = formatTime(audio.duration);
            });
        }

        syncTrackCards();
    }

    function onSongChange() {
        var activeSong = getActiveSong();
        if (activeSong) {
            var coverEl = document.getElementById("vebra-player-cover");
            var titleEl = document.getElementById("vebra-player-title");
            var titleLink = document.getElementById("vebra-player-title-link");
            var artistEl = document.getElementById("vebra-player-artist");
            var artLink = document.getElementById("vebra-player-art-link");

            if (coverEl && activeSong.cover_art_url) {
                coverEl.src = activeSong.cover_art_url;
            }
            if (titleEl && activeSong.name) {
                if (titleLink) {
                    titleLink.textContent = activeSong.name;
                    if (activeSong.id) {
                        titleLink.href = "song-detail.php?id=" + encodeURIComponent(activeSong.id);
                    }
                } else {
                    titleEl.textContent = activeSong.name;
                }
            }
            if (artLink && activeSong.id) {
                artLink.href = "song-detail.php?id=" + encodeURIComponent(activeSong.id);
            }
            if (artistEl && activeSong.artist) {
                artistEl.textContent = activeSong.artist;
            }
        }
        onPlayState();
        onTimeUpdate();
    }

    /* --------------------------------------------------
       AUDIO STATE SAVE
       -------------------------------------------------- */

    var lastSaveTime = 0;

    function saveAudioState() {
        try {
            var meta = getActiveSong();
            var audio = getAudio();

            if (!meta || !meta.url || !audio) return;

            var state = {
                id: meta.id || 0,
                url: meta.url,
                name: meta.name || "Track",
                artist: meta.artist || "VEBRA",
                cover_art_url: meta.cover_art_url || "",
                currentTime: audio.currentTime || 0,
                paused: !!audio.paused,
                index: Amplitude.getActiveIndex(),
                timestamp: Date.now()
            };

            sessionStorage.setItem(
                "vebra_audio_state",
                JSON.stringify(state)
            );
        } catch (e) {}
    }

    function saveAudioStateThrottled() {
        var now = Date.now();

        if (now - lastSaveTime > 1000) {
            lastSaveTime = now;
            saveAudioState();
        }
    }

    /* --------------------------------------------------
       INITIALIZE AMPLITUDE
       -------------------------------------------------- */

    Amplitude.init({
        songs: songs,
        volume: 70,
        continue_next: true,
        repeat: false,
        repeat_song: false,

        callbacks: {
            play: function() {
                qsa("audio").forEach(function(el) {
                    var currentAudio = getAudio();

                    if (el !== currentAudio && !el.paused) {
                        el.pause();
                    }
                });

                showBottomPlayer();
                onPlayState();
                saveAudioState();
            },

            pause: function() {
                onPlayState();
                saveAudioState();
            },

            stop: function() {
                onPlayState();
                saveAudioState();
            },

            timeupdate: function() {
                onTimeUpdate();
                saveAudioStateThrottled();
            },

            song_change: function() {
                onSongChange();
                saveAudioState();
            }
        }
    });

    /* --------------------------------------------------
       STOP AT END OF PLAYLIST
       -------------------------------------------------- */

    var audioEl = getAudio();

    if (audioEl) {
        audioEl.addEventListener("ended", function() {
            var activeIndex = Number(Amplitude.getActiveIndex());
            var total = (Amplitude.getSongs() || []).length;

            if (activeIndex >= total - 1) {
                Amplitude.pause();
                Amplitude.setSongPlayedPercentage(0);

                onPlayState();
                onTimeUpdate();

                try {
                    sessionStorage.removeItem("vebra_audio_state");
                } catch (e) {}
            }
        });
    }

    /* --------------------------------------------------
   GLOBAL TRACK PLAY FUNCTION — FIXED
   URL has priority over ID to prevent wrong songs.
   -------------------------------------------------- */

window.vebraPlayTrack = function(track) {
    if (!track || typeof Amplitude === "undefined") return;

    var all = Amplitude.getSongs() || [];
    var trackUrl = normalizeUrl(track.url);

    // First priority: match the exact audio URL.
    var found = -1;

    if (trackUrl) {
        found = all.findIndex(function(song) {
            return (
                song.url &&
                normalizeUrl(song.url) === trackUrl
            );
        });
    }

    // Only use ID matching when no URL was supplied.
    if (found === -1 && !trackUrl && track.id) {
        found = all.findIndex(function(song) {
            return (
                song.id &&
                String(song.id) === String(track.id)
            );
        });
    }

    if (found !== -1) {
        var activeIndex = Number(Amplitude.getActiveIndex());

        if (activeIndex === found) {
            if (isPlaying()) {
                Amplitude.pause();
            } else {
                Amplitude.play();
            }
        } else {
            Amplitude.playSongAtIndex(found);
        }
    } else {
        // No matching URL: add and play the requested track.
        Amplitude.addSong({
            id: track.id || "",
            name: track.name || "Track",
            artist: track.artist || "VEBRA",
            url: track.url,
            cover_art_url: track.cover_art_url || ""
        });

        Amplitude.playSongAtIndex(all.length);
    }

    showBottomPlayer();

    window.setTimeout(function() {
        onPlayState();
        onTimeUpdate();
        saveAudioState();
    }, 150);
};
    /* --------------------------------------------------
       CARD BUTTON CLICK HANDLER
       -------------------------------------------------- */

    if (!window.vebraCardClickHandlerAdded) {
        window.vebraCardClickHandlerAdded = true;

        document.addEventListener("click", function(e) {
            var btn = e.target.closest(
                ".vebra-play-card-btn, [data-play-song], .album-card__play, .vebra-song-play"
            );

            if (!btn) return;

            var target = btn.hasAttribute("data-url") ? btn : (btn.closest("[data-url]") || btn);
            var url = target.getAttribute("data-url");

            if (!url) return;

            e.preventDefault();
            e.stopPropagation();

            window.vebraPlayTrack({
                id: target.getAttribute("data-id") || "",
                name: target.getAttribute("data-title") || "Track",
                artist: target.getAttribute("data-artist") || "VEBRA",
                url: url,
                cover_art_url:
                    target.getAttribute("data-cover") || ""
            });
        });
    }

    /* --------------------------------------------------
       DEDICATED TRACK PAGE PLAYER
       -------------------------------------------------- */

    (function initDedicatedTrackPlayer() {
        if (!section) return;

        var params = new URLSearchParams(window.location.search);
        var trackId = params.get("id");

        if (!trackId) return;

        var all = Amplitude.getSongs() || [];

        var trackIndex = all.findIndex(function(song) {
            return String(song.id) === String(trackId);
        });

        if (trackIndex === -1) return;

        section.setAttribute("data-current-track-id", trackId);

        if (!section.dataset.vebraDedicatedPlayerInit) {
            section.dataset.vebraDedicatedPlayerInit = "1";

            section.addEventListener("click", function(e) {
                var btn = e.target.closest(
                    ".amplitude-play-pause, " +
                    "[data-role='play'], " +
                    "[data-track-play], " +
                    ".music-player__play, " +
                    ".track-player__play, " +
                    ".player-play, " +
                    ".btn-play, " +
                    "button[aria-label*='play' i]"
                );

                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();

                var currentIndex = Number(Amplitude.getActiveIndex());

                if (currentIndex === trackIndex) {
                    isPlaying() ? Amplitude.pause() : Amplitude.play();
                } else {
                    Amplitude.playSongAtIndex(trackIndex);
                }
            });
        }

        function syncDedicatedPlayer() {
            var isActive =
                Number(Amplitude.getActiveIndex()) === trackIndex;

            var activePlaying = isActive && isPlaying();

            section.classList.toggle("is-playing", activePlaying);
            section.classList.toggle("is-active-track", isActive);
        }

        var dedicatedAudio = getAudio();

        if (
            dedicatedAudio &&
            !dedicatedAudio.dataset.vebraDedicatedListeners
        ) {
            dedicatedAudio.dataset.vebraDedicatedListeners = "1";

            ["play", "pause", "timeupdate", "loadedmetadata", "ended"].forEach(
                function(eventName) {
                    dedicatedAudio.addEventListener(
                        eventName,
                        syncDedicatedPlayer
                    );
                }
            );
        }

        syncDedicatedPlayer();
    })();

    /* --------------------------------------------------
       BOTTOM PLAYER SEEKING
       -------------------------------------------------- */

    var rail = qs(".bottom-player__rail");

    if (rail && !rail.dataset.vebraSeekInit) {
        rail.dataset.vebraSeekInit = "1";
        rail.style.cursor = "pointer";

        rail.addEventListener("click", function(e) {
            var rect = rail.getBoundingClientRect();

            if (!rect.width) return;

            var pct = ((e.clientX - rect.left) / rect.width) * 100;
            pct = Math.max(0, Math.min(100, pct));

            Amplitude.setSongPlayedPercentage(pct);
            onTimeUpdate();
        });
    }

    /* --------------------------------------------------
       PROGRESS INPUT SEEKING
       -------------------------------------------------- */

    qsa(".player-range[data-role='progress']").forEach(function(input) {
        if (input.dataset.vebraSeekInit) return;

        input.dataset.vebraSeekInit = "1";

        input.addEventListener("input", function() {
            var pct = parseFloat(input.value);

            if (isFinite(pct)) {
                Amplitude.setSongPlayedPercentage(pct);
            }
        });
    });

    /* --------------------------------------------------
       VOLUME
       -------------------------------------------------- */

    qsa(".player-range[data-role='volume']").forEach(function(input) {
        if (input.dataset.vebraVolumeInit) return;

        input.dataset.vebraVolumeInit = "1";

        var paint = function() {
            input.style.setProperty("--progress", input.value + "%");
        };

        input.addEventListener("input", function() {
            paint();
            Amplitude.setVolume(parseInt(input.value, 10));
        });

        paint();
    });

    /* --------------------------------------------------
       MUTE / UNMUTE
       -------------------------------------------------- */

    var muteBtn = document.getElementById("vebra-mute-btn");
    var volumeIcon = document.getElementById("vebra-volume-icon");

    if (muteBtn && !muteBtn.dataset.vebraMuteInit) {
        muteBtn.dataset.vebraMuteInit = "1";

        muteBtn.addEventListener("click", function() {
            var audio = getAudio();

            if (!audio) return;

            audio.muted = !audio.muted;

            if (volumeIcon) {
                volumeIcon.className = audio.muted
                    ? "ri-volume-mute-line"
                    : "ri-volume-down-line";
            }
        });
    }

    /* --------------------------------------------------
       RESTORE PLAYER STATE
       -------------------------------------------------- */

    function restoreAudioState() {
        try {
            var raw = sessionStorage.getItem("vebra_audio_state");

            if (!raw) return;

            var state = JSON.parse(raw);

            if (!state || !state.url) return;

            if (Date.now() - (state.timestamp || 0) > 3600000) {
                sessionStorage.removeItem("vebra_audio_state");
                return;
            }

            showBottomPlayer();

            var currentSongs = Amplitude.getSongs() || [];

            var songIndex = currentSongs.findIndex(function(song) {
                return (
                    (state.id && song.id &&
                        String(song.id) === String(state.id)) ||
                    normalizeUrl(song.url) === normalizeUrl(state.url)
                );
            });

            if (songIndex === -1) {
                Amplitude.addSong({
                    id: state.id || "",
                    name: state.name || "Track",
                    artist: state.artist || "VEBRA",
                    url: state.url,
                    cover_art_url: state.cover_art_url || ""
                });

                songIndex = currentSongs.length;
            }

            if (!state.paused) {
                Amplitude.playSongAtIndex(songIndex);

                var audio = getAudio();

                if (audio && state.currentTime > 0) {
                    var restoreTime = function() {
                        audio.currentTime = state.currentTime;
                        audio.removeEventListener(
                            "loadedmetadata",
                            restoreTime
                        );
                    };

                    audio.addEventListener("loadedmetadata", restoreTime);
                }
            } else {
                // Restore the selected track without autoplay.
                Amplitude.playSongAtIndex(songIndex);
                Amplitude.pause();

                var pausedAudio = getAudio();

                if (pausedAudio && state.currentTime > 0) {
                    var setPausedTime = function() {
                        pausedAudio.currentTime = state.currentTime;
                        pausedAudio.removeEventListener(
                            "loadedmetadata",
                            setPausedTime
                        );
                        onTimeUpdate();
                    };

                    pausedAudio.addEventListener(
                        "loadedmetadata",
                        setPausedTime
                    );
                }
            }
        } catch (e) {
            console.warn("VEBRA audio restore failed:", e);
        }
    }

   /* ============================================================
   VEBRA — SMART PLAYER SESSION RESTORE
   First visit: neutral player
   Same tab navigation: restore current track
   Refresh: restore current track and position
   ============================================================ */

(function () {
    "use strict";

    var SESSION_KEY = "vebra_player_session_v1";
    var STATE_KEY = "vebra_audio_state";

    var isExistingSession = false;

    try {
        isExistingSession =
            sessionStorage.getItem(SESSION_KEY) === "1";

        if (isExistingSession) {
            // Existing tab session: preserve playback state.
            window.addEventListener(
                "beforeunload",
                saveAudioState
            );

            restoreAudioState();

        } else {
            // First visit / fresh tab: clear any stale saved track.
            sessionStorage.removeItem(STATE_KEY);

            // Mark this tab as an active VEBRA player session.
            sessionStorage.setItem(SESSION_KEY, "1");

            // Do not restore or automatically select an old track.
        }

    } catch (error) {
        console.warn(
            "VEBRA player session initialization failed:",
            error
        );
    }
})();

    /* --------------------------------------------------
       DURATION PROBING
       -------------------------------------------------- */

    var queue = rows.filter(function(row) {
        return (
            qs(".list-song__duration", row) &&
            row.getAttribute("data-url")
        );
    });

    (function probeNext(index) {
        if (index >= queue.length) return;

        var row = queue[index];
        var output = qs(".list-song__duration", row);
        var probe = new Audio();

        function next() {
            probeNext(index + 1);
        }

        probe.preload = "metadata";

        probe.addEventListener("loadedmetadata", function() {
            output.textContent = formatTime(probe.duration);
            next();
        });

        probe.addEventListener("error", function() {
            output.textContent = "--:--";
            next();
        });

        probe.src = row.getAttribute("data-url");
    })(0);

    /* --------------------------------------------------
       INITIAL UI STATE
       -------------------------------------------------- */

    onPlayState();
    onTimeUpdate();

   
}
    /* ======================================================================
   12 — NATIVE VIDEO
   ====================================================================== */

function initVideo() {
    qsa(".video-frame").forEach(function (frame) {
        var video = qs("video", frame);
        var poster = qs(".video-poster", frame);

        if (!video || video.dataset.vebraVideoInit === "1") return;

        video.dataset.vebraVideoInit = "1";

        var play = function () {
            // Pause other videos.
            qsa("video").forEach(function (other) {
                if (other !== video && !other.paused) {
                    other.pause();

                    var otherFrame = other.closest(".video-frame");
                    if (otherFrame) {
                        otherFrame.classList.remove("is-playing");
                    }
                }
            });

            // Pause music when video starts.
            if (
                typeof Amplitude !== "undefined" &&
                Amplitude.getPlayerState() === "playing"
            ) {
                Amplitude.pause();
            }

            video.play().catch(function (error) {
                console.warn("Video playback failed:", error);
            });

            frame.classList.add("is-playing");
            video.setAttribute("controls", "");
        };

        if (poster) {
            poster.addEventListener("click", play);
        }

        video.addEventListener("play", function () {
            // Keep music and video from playing together.
            if (
                typeof Amplitude !== "undefined" &&
                Amplitude.getPlayerState() === "playing"
            ) {
                Amplitude.pause();
            }

            frame.classList.add("is-playing");
        });

        video.addEventListener("pause", function () {
            if (video.currentTime === 0 || video.ended) {
                frame.classList.remove("is-playing");
            }
        });

        video.addEventListener("ended", function () {
            frame.classList.remove("is-playing");
            video.removeAttribute("controls");
            video.currentTime = 0;
        });
    });
}
    /* ======================================================================
       13 — LIGHTBOX
       `data-lightbox="image|video"`, grouped by `data-gallery`.
       ====================================================================== */

    function initLightbox() {
        var triggers = qsa("[data-lightbox]");
        if (!triggers.length) return;

        var box = buildLightbox();
        var stage = qs(".lightbox__stage", box);
        var media = qs(".lightbox__media", box);
        var captionText = qs(".lightbox__caption-text", box);
        var counter = qs(".lightbox__counter", box);
        var prevBtn = qs(".lightbox__nav--prev", box);
        var nextBtn = qs(".lightbox__nav--next", box);

        var group = [];
        var index = 0;
        var lastFocus = null;

        function collect(trigger) {
            var name = trigger.getAttribute("data-gallery");
            if (!name) return [trigger];
            return triggers.filter(function (t) {
                return t.getAttribute("data-gallery") === name;
            });
        }

        function render() {
            var trigger = group[index];
            if (!trigger) return;

            var type = trigger.getAttribute("data-lightbox") || "image";
            var src = trigger.getAttribute("data-src") || trigger.getAttribute("href");
            var caption = trigger.getAttribute("data-caption") || "";

            media.innerHTML = "";
            box.classList.add("is-loading");

            if (type === "video") {
                var iframe = document.createElement("iframe");
                iframe.src = src;
                iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
                iframe.allowFullscreen = true;
                iframe.title = caption || "Video";
                media.appendChild(iframe);
                box.classList.remove("is-loading");
            } else {
                var img = new Image();
                img.alt = caption;
                img.onload = function () {
                    box.classList.remove("is-loading");
                };
                img.onerror = function () {
                    box.classList.remove("is-loading");
                };
                img.src = src;
                media.appendChild(img);
            }

            captionText.textContent = caption;
            counter.textContent = group.length > 1 ? pad(index + 1) + " / " + pad(group.length) : "";

            var multi = group.length > 1;
            prevBtn.hidden = !multi;
            nextBtn.hidden = !multi;

            if (hasGsap && !prefersReducedMotion) {
                gsap.fromTo(
                    stage,
                    { opacity: 0, scale: 0.96 },
                    { opacity: 1, scale: 1, duration: 0.5, ease: "expo.out" }
                );
            }
        }

        function open(trigger) {
            group = collect(trigger);
            index = group.indexOf(trigger);
            if (index < 0) index = 0;

            lastFocus = document.activeElement;
            box.classList.add("is-open");
            box.setAttribute("aria-hidden", "false");
            lockScroll(true);
            render();

            if (hasGsap && !prefersReducedMotion) {
                gsap.to(box, { opacity: 1, duration: 0.35, ease: "power2.out" });
            } else {
                box.style.opacity = 1;
            }

            qs(".lightbox__close", box).focus();
        }

        function close() {
            var finish = function () {
                box.classList.remove("is-open");
                box.setAttribute("aria-hidden", "true");
                media.innerHTML = "";
                lockScroll(false);
                if (lastFocus) lastFocus.focus();
            };

            if (hasGsap && !prefersReducedMotion) {
                gsap.to(box, { opacity: 0, duration: 0.3, ease: "power2.in", onComplete: finish });
            } else {
                box.style.opacity = 0;
                finish();
            }
        }

        function step(dir) {
            if (group.length < 2) return;
            index = (index + dir + group.length) % group.length;
            render();
        }

        triggers.forEach(function (trigger) {
            trigger.addEventListener("click", function (e) {
                e.preventDefault();
                open(trigger);
            });
        });

        qs(".lightbox__backdrop", box).addEventListener("click", close);
        qs(".lightbox__close", box).addEventListener("click", close);
        prevBtn.addEventListener("click", function () {
            step(-1);
        });
        nextBtn.addEventListener("click", function () {
            step(1);
        });

        document.addEventListener("keydown", function (e) {
            if (!box.classList.contains("is-open")) return;
            if (e.key === "Escape") close();
            if (e.key === "ArrowLeft") step(-1);
            if (e.key === "ArrowRight") step(1);
            // Keep focus inside the dialog.
            if (e.key === "Tab") {
                var focusables = qsa("button:not([hidden])", box);
                var first = focusables[0];
                var last = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });
    }

    function buildLightbox() {
        var existing = qs(".lightbox");
        if (existing) return existing;

        var box = document.createElement("div");
        box.className = "lightbox";
        box.setAttribute("role", "dialog");
        box.setAttribute("aria-modal", "true");
        box.setAttribute("aria-hidden", "true");
        box.innerHTML = [
            '<div class="lightbox__backdrop"></div>',
            '<button type="button" class="btn-circle btn-circle--glass lightbox__close" aria-label="Close">',
            '<i class="ri-close-line"></i></button>',
            '<button type="button" class="btn-circle btn-circle--glass lightbox__nav lightbox__nav--prev" aria-label="Previous">',
            '<i class="ri-arrow-left-line"></i></button>',
            '<button type="button" class="btn-circle btn-circle--glass lightbox__nav lightbox__nav--next" aria-label="Next">',
            '<i class="ri-arrow-right-line"></i></button>',
            '<div class="lightbox__spinner"></div>',
            '<div class="lightbox__stage"><figure class="lightbox__figure">',
            '<div class="lightbox__media"></div>',
            '<figcaption class="lightbox__caption">',
            '<span class="lightbox__caption-text"></span>',
            '<span class="lightbox__counter"></span>',
            "</figcaption></figure></div>",
        ].join("");

        document.body.appendChild(box);
        return box;
    }

    /* ======================================================================
       14 — FILTERABLE GRIDS (FLIP, replaces Isotope)
       ====================================================================== */

    function initFilterGrids() {
        qsa("[data-filter-grid]").forEach(function (grid) {
            var scope = grid.closest("[data-filter-scope]") || grid.parentElement;
            var buttons = qsa(".filter-list__btn", scope);
            if (!buttons.length) return;

            var items = qsa(".filter-grid__item", grid);
            var countLabel = qsa("[data-filter-count]", scope)[0];

            buttons.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    var filter = btn.getAttribute("data-filter") || "*";

                    buttons.forEach(function (b) {
                        b.classList.toggle("is-active", b === btn);
                        b.setAttribute("aria-pressed", String(b === btn));
                    });

                    // FLIP: record positions, apply the filter, animate the delta.
                    var before = items.map(function (item) {
                        return item.getBoundingClientRect();
                    });

                    var visible = 0;
                    items.forEach(function (item) {
                        var cats = (item.getAttribute("data-category") || "").split(/\s+/);
                        var match = filter === "*" || cats.indexOf(filter) !== -1;
                        item.classList.toggle("is-filtered", !match);
                        if (match) visible++;
                    });

                    grid.classList.toggle("is-empty", visible === 0);
                    if (countLabel) {
                        countLabel.textContent = visible + " product" + (visible === 1 ? "" : "s") + " found";
                    }

                    if (!hasGsap || prefersReducedMotion) return;

                    items.forEach(function (item, i) {
                        if (item.classList.contains("is-filtered")) return;

                        var after = item.getBoundingClientRect();
                        var dx = before[i].left - after.left;
                        var dy = before[i].top - after.top;

                        // Newly shown items fade up; moved items slide from the old spot.
                        if (before[i].width === 0) {
                            gsap.fromTo(
                                item,
                                { opacity: 0, scale: 0.92 },
                                { opacity: 1, scale: 1, duration: 0.6, ease: "expo.out" }
                            );
                        } else if (dx || dy) {
                            gsap.fromTo(
                                item,
                                { x: dx, y: dy },
                                { x: 0, y: 0, duration: 0.7, ease: "expo.out" }
                            );
                        }
                    });

                    if (hasGsap) ScrollTrigger.refresh();
                });
            });
        });
    }

    /* ======================================================================
       15 — TABS
       ====================================================================== */

    function initTabs() {
        qsa("[data-tabs]").forEach(function (root) {
            var buttons = qsa(".tabs__btn", root);
            var indicator = qs(".tabs__indicator", root);
            if (!buttons.length) return;

            var panels = qsa("[data-tab-panel]", root.closest("[data-tabs-scope]") || root.parentElement);

            function moveIndicator(btn) {
                if (!indicator) return;
                indicator.style.width = btn.offsetWidth + "px";
                indicator.style.transform = "translateX(" + btn.offsetLeft + "px)";
            }

            function activate(btn) {
                var name = btn.getAttribute("data-tab");

                buttons.forEach(function (b) {
                    b.classList.toggle("is-active", b === btn);
                    b.setAttribute("aria-selected", String(b === btn));
                });

                panels.forEach(function (panel) {
                    var match = panel.getAttribute("data-tab-panel") === name;
                    panel.classList.toggle("is-active", match);
                    if (match && hasGsap && !prefersReducedMotion) {
                        gsap.fromTo(
                            panel,
                            { opacity: 0, y: 18 },
                            { opacity: 1, y: 0, duration: 0.6, ease: "expo.out" }
                        );
                    }
                });

                moveIndicator(btn);
                if (hasGsap) ScrollTrigger.refresh();
            }

            buttons.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    activate(btn);
                });
            });

            var initial = buttons.filter(function (b) {
                return b.classList.contains("is-active");
            })[0] || buttons[0];

            activate(initial);
            window.addEventListener("resize", debounce(function () {
                moveIndicator(
                    buttons.filter(function (b) {
                        return b.classList.contains("is-active");
                    })[0] || buttons[0]
                );
            }, 150));
        });
    }

    /* ======================================================================
       16 — SMALL INTERACTIONS
       ====================================================================== */

    /** 3D tilt on [data-tilt]. */
    function initTilt() {
        if (!hasGsap || isTouch || prefersReducedMotion) return;

        qsa("[data-tilt]").forEach(function (el) {
            var max = parseFloat(el.getAttribute("data-tilt")) || 8;
            gsap.set(el, { transformPerspective: 900, transformStyle: "preserve-3d" });

            var rx = gsap.quickTo(el, "rotationX", { duration: 0.7, ease: "power3" });
            var ry = gsap.quickTo(el, "rotationY", { duration: 0.7, ease: "power3" });

            el.addEventListener("mousemove", function (e) {
                var r = el.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width - 0.5;
                var py = (e.clientY - r.top) / r.height - 0.5;
                ry(px * max * 2);
                rx(-py * max * 2);
            });

            el.addEventListener("mouseleave", function () {
                rx(0);
                ry(0);
            });
        });
    }

    /** Swap an image source on hover — product cards. */
    function initHoverSwap() {
        qsa("[data-hover-src]").forEach(function (img) {
            var original = img.getAttribute("src");
            var swap = img.getAttribute("data-hover-src");
            var scope = img.closest("[data-hover-scope]") || img.parentElement;

            // Preload so the swap doesn't flash.
            var pre = new Image();
            pre.src = swap;

            scope.addEventListener("mouseenter", function () {
                img.setAttribute("src", swap);
            });
            scope.addEventListener("mouseleave", function () {
                img.setAttribute("src", original);
            });
        });
    }

    /**
     * Image preview that follows the cursor over list rows (gigs, members).
     * Rows carry `data-preview-src`; the scope hosts one shared preview node.
     */
    function initRowPreview() {
        if (!hasGsap || isTouch || prefersReducedMotion) return;

        qsa("[data-preview-scope]").forEach(function (scope) {
            var preview = qs(".row-preview", scope);
            var rows = qsa("[data-preview-src]", scope);
            if (!preview || !rows.length) return;

            var img = qs("img", preview);
            var xTo = gsap.quickTo(preview, "x", { duration: 0.8, ease: "power3" });
            var yTo = gsap.quickTo(preview, "y", { duration: 0.8, ease: "power3" });
            var pointerSeen = false;

            scope.addEventListener("mousemove", function (e) {
                var r = scope.getBoundingClientRect();
                pointerSeen = true;
                xTo(e.clientX - r.left);
                yTo(e.clientY - r.top);
            });

            rows.forEach(function (row) {
                row.addEventListener("mouseenter", function () {
                    if (img) img.src = row.getAttribute("data-preview-src");

                    /* A row can be entered by scrolling under a still cursor, so
                       mousemove may never have run and x/y would still be 0 —
                       the preview would flash at the scope's top-left corner.
                       Seed it over the row's right side instead, which is where
                       it would sit had the pointer arrived normally. */
                    if (!pointerSeen) {
                        var sr = scope.getBoundingClientRect();
                        var rr = row.getBoundingClientRect();
                        gsap.set(preview, {
                            x: rr.right - sr.left - rr.width * 0.25,
                            y: rr.top - sr.top + rr.height / 2
                        });
                    }

                    gsap.to(preview, { opacity: 1, scale: 1, duration: 0.45, ease: "expo.out" });
                });
                row.addEventListener("mouseleave", function () {
                    gsap.to(preview, { opacity: 0, scale: 0.9, duration: 0.3, ease: "power2.out" });
                });
            });
        });
    }

    /** Quantity steppers on the product page. */
    function initQuantity() {
        qsa(".qty").forEach(function (widget) {
            var input = qs("input", widget);
            if (!input) return;

            qsa("button", widget).forEach(function (btn) {
                btn.addEventListener("click", function () {
                    var step = btn.getAttribute("data-step") === "down" ? -1 : 1;
                    var min = parseInt(input.getAttribute("min"), 10) || 1;
                    var value = (parseInt(input.value, 10) || min) + step;
                    input.value = Math.max(min, value);
                });
            });
        });
    }

    /**
     * Contact / comment forms ship as front-end stubs — no backend in the
     * template. Validate, show a message, and never actually submit.
     */
    function initForms() {
        qsa("form[data-stub-form]").forEach(function (form) {
            var message = qs(".form-message", form);

            form.addEventListener("submit", function (e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                if (message) {
                    message.textContent =
                        form.getAttribute("data-success-message") ||
                        "Thanks — your message has been received. Connect a backend to enable delivery.";
                    if (hasGsap && !prefersReducedMotion) {
                        gsap.fromTo(
                            message,
                            { opacity: 0, y: 10 },
                            { opacity: 1, y: 0, duration: 0.5, ease: "expo.out" }
                        );
                    }
                }

                form.reset();
            });
        });
    }

    /** Counters that roll up when scrolled into view. */
    function initCounters() {
        if (!hasGsap) return;

        qsa("[data-count-to]").forEach(function (el) {
            var target = parseFloat(el.getAttribute("data-count-to")) || 0;
            var suffix = el.getAttribute("data-count-suffix") || "";

            if (prefersReducedMotion) {
                el.textContent = target + suffix;
                return;
            }

            var obj = { value: 0 };
            gsap.to(obj, {
                value: target,
                duration: 2,
                ease: "power2.out",
                scrollTrigger: { trigger: el, start: "top 90%", once: true },
                onUpdate: function () {
                    el.textContent = Math.round(obj.value) + suffix;
                },
            });
        });
    }

    /* ======================================================================
       17 — BOOTSTRAP
       ====================================================================== */

    function init() {
        if (hasGsap && typeof ScrollTrigger !== "undefined") {
            gsap.registerPlugin(ScrollTrigger);
            if (typeof SplitText !== "undefined") gsap.registerPlugin(SplitText);
        }

        initSmoothScroll();
        initHeader();

        // Splitting before webfonts land produces wrong line breaks, so wait
        // for them. Fall back after a short timeout if the font request hangs.
        if (document.fonts && document.fonts.ready) {
            Promise.race([document.fonts.ready, new Promise(function (r) { setTimeout(r, 2500); })]).then(
                initTextReveal
            );
        } else {
            initTextReveal();
        }

        initReveals();
        initRevealGroups();
        initParallax();
        initMagnetic();
        initCursor();
        initSliders();
        initPlayers();
        initVideo();
        initLightbox();
        initFilterGrids();
        initTabs();
        initTilt();
        initHoverSwap();
        initRowPreview();
        initQuantity();
        initForms();
        initCounters();
        initPreloader();

        // Late-loading images change layout; recalculate trigger positions.
        window.addEventListener("load", function () {
            if (hasGsap) ScrollTrigger.refresh();
        });

        window.addEventListener(
            "resize",
            debounce(function () {
                if (hasGsap) ScrollTrigger.refresh();
            }, 250)
        );
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
    // Reinitialize components after VEBRA soft navigation
window.VEBRA_REINIT_CONTENT = function () {
    initTextReveal();
    initReveals();
    initRevealGroups();
    initSliders();
    initVideo();
    initLightbox();
    initFilterGrids();
    initTabs();
    initHoverSwap();
    initRowPreview();
    initQuantity();
    initForms();
    initCounters();
    refreshScrollTriggers();
};
})();
