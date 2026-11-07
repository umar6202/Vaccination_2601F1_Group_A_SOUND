<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

function vebra_store_products(): array
{
    global $conn;
    $rows = [];
    $res = mysqli_query($conn, 'SELECT * FROM store_products WHERE is_active = 1 ORDER BY sort_order, id');
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
        }
    }

    $products = [];
    foreach ($rows as $row) {
        $sizes = array_values(array_filter(array_map('trim', explode(',', (string)$row['sizes']))));
        $products[$row['product_key']] = [
            'id' => $row['product_key'],
            'name' => $row['title'],
            'title' => $row['title'],
            'subtitle' => $row['subtitle'],
            'sku' => $row['sku'],
            'category' => $row['category'],
            'category_key' => $row['category_key'],
            'price' => $row['price'],
            'old_price' => $row['old_price'],
            'rating' => (int)$row['rating'],
            'reviews_count' => (int)$row['reviews_count'],
            'shipping' => $row['shipping'],
            'desc' => $row['description'],
            'image' => $row['image1'],
            'image1' => $row['image1'],
            'image2' => $row['image2'],
            'image3' => $row['image3'],
            'badge' => $row['badge'],
            'badge_style' => $row['badge_style'],
            'sizes' => $sizes,
            'has_sizes' => count($sizes) > 0,
        ];
    }

    return $products;
}

function vebra_store_csrf_token(): string
{
    if (empty($_SESSION['store_csrf_token'])) {
        $_SESSION['store_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['store_csrf_token'];
}

function vebra_store_valid_csrf($token): bool
{
    return is_string($token)
        && isset($_SESSION['store_csrf_token'])
        && hash_equals($_SESSION['store_csrf_token'], $token);
}

function vebra_store_cart(): array
{
    global $conn;
    $cart = [];
    if (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "SELECT product_key, quantity, size FROM cart_items WHERE user_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($res)) {
                $key = vebra_store_cart_key($row['product_key'], $row['size'] ?? '');
                $cart[$key] = ['quantity' => (int)$row['quantity']];
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        if (!isset($_SESSION['store_cart']) || !is_array($_SESSION['store_cart'])) {
            $_SESSION['store_cart'] = [];
        }
        $cart = $_SESSION['store_cart'];
    }

    return $cart;
}

function vebra_store_cart_count(): int
{
    $count = 0;
    foreach (vebra_store_cart() as $item) {
        $count += max(0, (int)($item['quantity'] ?? 0));
    }

    return $count;
}

function vebra_store_cart_key(string $productId, string $size = ''): string
{
    return $productId . ':' . $size;
}

function vebra_store_cart_add(string $productId, int $quantity, string $size = ''): bool
{
    global $conn;
    if ($quantity < 1 || $quantity > 20) {
        return false;
    }

    if (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
        $cleanSize = trim($size);

        // Check if item already in cart
        $stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_key = ? AND (size = ? OR (size IS NULL AND ? = '')) LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isss", $userId, $productId, $cleanSize, $cleanSize);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            mysqli_stmt_close($stmt);

            if ($row) {
                $newQty = min(20, (int)$row['quantity'] + $quantity);
                $upStmt = mysqli_prepare($conn, "UPDATE cart_items SET quantity = ? WHERE id = ?");
                if ($upStmt) {
                    mysqli_stmt_bind_param($upStmt, "ii", $newQty, $row['id']);
                    mysqli_stmt_execute($upStmt);
                    mysqli_stmt_close($upStmt);
                }
            } else {
                $inStmt = mysqli_prepare($conn, "INSERT INTO cart_items (user_id, product_key, quantity, size) VALUES (?, ?, ?, ?)");
                if ($inStmt) {
                    mysqli_stmt_bind_param($inStmt, "isis", $userId, $productId, $quantity, $cleanSize);
                    mysqli_stmt_execute($inStmt);
                    mysqli_stmt_close($inStmt);
                }
            }
            return true;
        }
    } else {
        if (!isset($_SESSION['store_cart']) || !is_array($_SESSION['store_cart'])) {
            $_SESSION['store_cart'] = [];
        }
        $key = vebra_store_cart_key($productId, $size);
        $cur = (int)($_SESSION['store_cart'][$key]['quantity'] ?? 0);
        $_SESSION['store_cart'][$key] = ['quantity' => min(20, $cur + $quantity)];
        return true;
    }

    return false;
}

function vebra_store_cart_update(string $cartKey, int $quantity): bool
{
    global $conn;
    $parts = explode(':', (string)$cartKey, 2);
    $productId = $parts[0] ?? '';
    $size = trim($parts[1] ?? '');
    $quantity = max(1, min(20, $quantity));

    if (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_key = ? AND (size = ? OR (size IS NULL AND ? = ''))");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iisss", $quantity, $userId, $productId, $size, $size);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }
    } else {
        if (!isset($_SESSION['store_cart']) || !is_array($_SESSION['store_cart'])) {
            $_SESSION['store_cart'] = [];
        }
        if (isset($_SESSION['store_cart'][$cartKey])) {
            $_SESSION['store_cart'][$cartKey]['quantity'] = $quantity;
            return true;
        }
    }

    return false;
}

function vebra_store_cart_remove(string $cartKey): bool
{
    global $conn;
    $parts = explode(':', (string)$cartKey, 2);
    $productId = $parts[0] ?? '';
    $size = trim($parts[1] ?? '');

    if (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM cart_items WHERE user_id = ? AND product_key = ? AND (size = ? OR (size IS NULL AND ? = ''))");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isss", $userId, $productId, $size, $size);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }
    } else {
        if (isset($_SESSION['store_cart'][$cartKey])) {
            unset($_SESSION['store_cart'][$cartKey]);
            return true;
        }
    }

    return false;
}

function vebra_store_cart_clear(): void
{
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM cart_items WHERE user_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    $_SESSION['store_cart'] = [];
}

function vebra_store_cart_items(): array
{
    $products = vebra_store_products();
    $items = [];

    foreach (vebra_store_cart() as $key => $cartItem) {
        $parts = explode(':', (string)$key, 2);
        $productId = $parts[0] ?? '';
        $size = $parts[1] ?? '';
        if (!isset($products[$productId])) {
            continue;
        }

        $product = $products[$productId];
        if (($product['sizes'] && !in_array($size, $product['sizes'], true))
            || (!$product['sizes'] && $size !== '')) {
            continue;
        }

        $quantity = (int)($cartItem['quantity'] ?? 0);
        if ($quantity < 1 || $quantity > 20) {
            continue;
        }

        $product['id'] = $productId;
        $product['size'] = $size;
        $product['quantity'] = $quantity;
        $product['unit_cents'] = (int)round((float)$product['price'] * 100);
        $product['line_cents'] = $product['unit_cents'] * $quantity;
        $product['key'] = vebra_store_cart_key($productId, $size);
        $items[] = $product;
    }

    return $items;
}

function vebra_store_format_money(int $cents): string
{
    return '$' . number_format($cents / 100, 2);
}
