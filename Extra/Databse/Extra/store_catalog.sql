CREATE TABLE IF NOT EXISTS store_products (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_key VARCHAR(80) NOT NULL,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(200) NOT NULL,
    sku VARCHAR(80) NOT NULL,
    category VARCHAR(120) NOT NULL,
    category_key VARCHAR(40) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
    reviews_count INT UNSIGNED NOT NULL DEFAULT 0,
    shipping VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image1 VARCHAR(255) NOT NULL,
    image2 VARCHAR(255) NOT NULL,
    image3 VARCHAR(255) NOT NULL,
    badge VARCHAR(60) DEFAULT NULL,
    badge_style VARCHAR(30) NOT NULL DEFAULT '',
    sizes VARCHAR(100) NOT NULL DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_store_products_product_key (product_key),
    UNIQUE KEY uq_store_products_sku (sku),
    KEY idx_store_products_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO store_products (
    product_key, title, subtitle, sku, category, category_key, price, old_price,
    rating, reviews_count, shipping, description, image1, image2, image3,
    badge, badge_style, sizes, sort_order, is_active
) VALUES
(
    'nightform-vinyl', 'Nightform — Double Vinyl', 'Vinyl · Limited to 1,000',
    'VEB-NF-2LP-001', 'Vinyl · Albums', 'vinyl', 38.00, 46.00, 5, 42,
    'Free over $75 · Ships in 3 days',
    'The 2026 album on 180g double vinyl, cut at 45rpm from the original tape transfers. Foil-blocked gatefold sleeve, twelve-page insert of Catskills session photographs and a lossless download card tucked behind side D.',
    'assets/images/product-1.jpg', 'assets/images/product-2.jpg', 'assets/images/product-3.jpg',
    'Limited', 'pill--accent', '', 1, 1
),
(
    'halogen-single', 'Halogen — 12" Single', 'Vinyl · Limited Pressing',
    'VEB-HLG-12-002', 'Vinyl · Singles', 'vinyl', 22.00, 28.00, 4, 18,
    'Free over $75 · Ships in 3 days',
    'Limited edition 12-inch single featuring the extended club mix of "Halogen" on side A, and an unreleased ambient instrumental rework on side B. Cut at 45rpm for supreme punch and clarity.',
    'assets/images/product-2.jpg', 'assets/images/product-3.jpg', 'assets/images/Product-4.jpg',
    'Sale', '', '', 2, 1
),
(
    'tour-tee', 'Tour Tee 2026', 'Apparel · Heavyweight Organic Cotton',
    'VEB-TEE-2026-BLK', 'Apparel · T-Shirts', 'apparel', 34.00, NULL, 5, 64,
    'Ships worldwide in 2-4 business days',
    'Custom heavyweight 240gsm combed organic cotton t-shirt. Features typographic Nightform chest embroidery and high-density screenprinted 2026 tour routing across the reverse.',
    'assets/images/product-3.jpg', 'assets/images/Product-4.jpg', 'assets/images/Product-5.jpg',
    'New', 'pill--accent', 'S,M,L,XL', 3, 1
),
(
    'work-jacket', 'Rivington Work Jacket', 'Apparel · Limited Edition Cut',
    'VEB-JKT-RIV-001', 'Apparel · Outerwear', 'apparel', 120.00, NULL, 4, 11,
    'Free worldwide priority courier',
    'Durable 12oz duck canvas jacket lined with warm quilted flannel. Custom bronze hardware, interior stash pocket for cassette tapes or audio interfaces, and tonal VEBRA collar embroidery.',
    'assets/images/Product-4.jpg', 'assets/images/Product-5.jpg', 'assets/images/Product-6.jpg',
    '2 left', '', 'S,M,L,XL', 4, 1
),
(
    'enamel-pins', 'Nightform Enamel Pin Set', 'Accessories · 3-Piece Set',
    'VEB-PIN-SET-001', 'Accessories · Pins', 'accessories', 14.00, 18.00, 4, 27,
    'Ships in padded recyclable mailer',
    'Hard enamel pin set depicting the waveform sigil, analogue tape reel, and classic VEBRA wordmark with dual butterfly clasps on commemorative card backing.',
    'assets/images/Product-5.jpg', 'assets/images/Product-6.jpg', 'assets/images/product-1.jpg',
    'Sale', '', '', 5, 1
),
(
    'screen-print', 'Screen Print — Hand Numbered', 'Artwork · Limited Edition of 300',
    'VEB-PRT-SET-2026', 'Artwork · Prints', 'accessories', 45.00, NULL, 5, 9,
    'Shipped in heavy duty cardboard tube',
    'Hand-pulled 4-color silkscreen print on 300gsm Somerset archival cotton paper. Measures 18" x 24", signed and hand-numbered in pencil by the band.',
    'assets/images/Product-6.jpg', 'assets/images/product-1.jpg', 'assets/images/product-2.jpg',
    'Signed', 'pill--accent', '', 6, 1
),
(
    'deluxe-box', 'Nightform Deluxe Box Set', 'Collector Edition · 500 Copies Made',
    'VEB-BOX-DLX-001', 'Vinyl · Box Sets', 'vinyl', 89.00, 110.00, 5, 58,
    'Free tracked express worldwide',
    'The ultimate collector package: Double 180g colored vinyl, 48-page hardcover photo book from Catskills recording sessions, numbered art print, enamel pin, and download voucher.',
    'assets/images/product-1.jpg', 'assets/images/product-2.jpg', 'assets/images/product-3.jpg',
    'Bundle · Limited', 'pill--accent', '', 0, 1
);
