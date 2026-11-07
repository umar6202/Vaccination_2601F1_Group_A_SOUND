/**
 * ECHORA — pre-render flags
 *
 * Runs in <head>, before first paint, so entrance animations never flash
 * their finished state. It is a blocking script by design: deferring it
 * would let unstyled content render first, which is the exact problem the
 * `is-loading` flag exists to prevent.
 *
 * Adds nothing when the visitor asks for reduced motion, so the animated
 * paths stay switched off for them.
 */
(function () {
    "use strict";

    var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!reduce) {
        document.documentElement.classList.add("js-anim", "is-loading");
    }
})();
