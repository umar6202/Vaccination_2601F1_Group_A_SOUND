<?php
require_once __DIR__ . '/includes/db.php';

// 1. Ensure artists exist
$artists = [
    ['Mara Vance', 'Founding member, Vocals & Synths. Mara writes the first line of every VEBRA song on a battered Juno-60.', 'assets/images/team-1.jpg'],
    ['Elias Ward', 'Guitars. Angular chords and wall of sound tone shaping.', 'assets/images/team-2.jpg'],
    ['Nico Ferreira', 'Bass. Driving basslines that anchor VEBRA rhythm sections.', 'assets/images/team-3.jpg'],
    ['Junie Okafor', 'Drums & Percussion. Dynamic polyrhythms and visceral live energy.', 'assets/images/team-4.jpg'],
    ['Theo Lindqvist', 'Modular & Keys. Analog textures, soundscapes and synth wizardry.', 'assets/images/team-5.jpg'],
    ['Ada Reyes', 'Live Strings. Cinematic violin and viola arrangements on tour.', 'assets/images/team-6.jpg']
];

foreach ($artists as $art) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM artists WHERE name = ?");
    mysqli_stmt_bind_param($stmt, "s", $art[0]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) === 0) {
        $ins = mysqli_prepare($conn, "INSERT INTO artists (name, bio, image) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($ins, "sss", $art[0], $art[1], $art[2]);
        mysqli_stmt_execute($ins);
        echo "Inserted artist: " . $art[0] . "\n";
    }
}

// Get Mara Vance ID
$maraId = 1;
$res = mysqli_query($conn, "SELECT id FROM artists WHERE name = 'Mara Vance' LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    $maraId = (int)$row['id'];
}

// 2. Update song titles and artworks
$songUpdates = [
    1 => ['Nightform', $maraId, 'English', 2026, 'assets/images/album-1.jpg', 'assets/musics/1.mp3'],
    2 => ['Halogen Kids', $maraId, 'English', 2026, 'assets/images/album-2.jpg', 'assets/musics/2.mp3'],
    3 => ['Amber Room', $maraId, 'English', 2026, 'assets/images/album-3.jpg', 'assets/musics/3.mp3'],
    4 => ['Static Bloom', $maraId, 'English', 2026, 'assets/images/album-4.jpg', 'assets/musics/4.mp3'],
    5 => ['Sirens, Slowly', $maraId, 'English', 2026, 'assets/images/album-5.jpg', 'assets/musics/5.mp3'],
];

foreach ($songUpdates as $id => $s) {
    $stmt = mysqli_prepare($conn, "UPDATE songs SET title = ?, artist_id = ?, language = ?, release_year = ?, artwork = ?, audio_path = ?, status = 'approved' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sisissi", $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $id);
    mysqli_stmt_execute($stmt);
}
echo "Updated songs.\n";

// Update video
$stmt = mysqli_prepare($conn, "UPDATE videos SET title = 'Nightform — Official Video', artist_id = ?, language = 'English', release_year = 2026, thumbnail = 'assets/images/video-cover-1.jpg', video_path = 'assets/videos/1.mp4', status = 'approved' WHERE id = 1");
mysqli_stmt_bind_param($stmt, "i", $maraId);
mysqli_stmt_execute($stmt);
echo "Updated video.\n";

// 3. Create Admin and Demo User
$users = [
    [
        'username' => 'admin',
        'name' => 'VEBRA Administrator',
        'address' => 'VEBRA HQ, 10 Sound Boulevard, Brooklyn, NY',
        'phone' => '+1-555-0199',
        'email' => 'admin@vebra.com',
        'password' => 'admin123',
        'role' => 'admin'
    ],
    [
        'username' => 'demouser',
        'name' => 'Demo Listener',
        'address' => '42 Music Avenue, New York, NY',
        'phone' => '+1-555-0188',
        'email' => 'user@vebra.com',
        'password' => 'user123',
        'role' => 'user'
    ],
    [
        'username' => 'alex_m',
        'name' => 'Alex Miller',
        'address' => '18 Echo Street, Boston, MA',
        'phone' => '+1-555-0177',
        'email' => 'alex@example.com',
        'password' => 'user123',
        'role' => 'user'
    ]
];

foreach ($users as $u) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $u['username'], $u['email']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) === 0) {
        $hash = password_hash($u['password'], PASSWORD_BCRYPT);
        $ins = mysqli_prepare($conn, "INSERT INTO users (username, name, address, phone, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($ins, "sssssss", $u['username'], $u['name'], $u['address'], $u['phone'], $u['email'], $hash, $u['role']);
        mysqli_stmt_execute($ins);
        echo "Created user: " . $u['username'] . " (" . $u['role'] . ")\n";
    }
}

// 4. Sample reviews for songs
$demoUserId = 2;
$alexId = 3;
$res = mysqli_query($conn, "SELECT id FROM users WHERE username = 'demouser' LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) $demoUserId = (int)$row['id'];
$res = mysqli_query($conn, "SELECT id FROM users WHERE username = 'alex_m' LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) $alexId = (int)$row['id'];

$reviews = [
    [$demoUserId, 1, 5, 'Incredible atmosphere. The modular organ and vocals cut through with intense emotional clarity.'],
    [$alexId, 1, 5, 'Nightform is easily one of the best tracks released this year. Masterpiece production.'],
    [$demoUserId, 2, 4, 'High energy track, the synth lines and drumming drive the momentum forward.'],
    [$alexId, 3, 5, 'Raw, intimate, and stunningly mixed. Love the warmth on the tape recording.']
];

foreach ($reviews as $rev) {
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO reviews (user_id, song_id, rating, review) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiis", $rev[0], $rev[1], $rev[2], $rev[3]);
    mysqli_stmt_execute($stmt);
}
echo "Sample reviews seeded.\n";
echo "Done!\n";
