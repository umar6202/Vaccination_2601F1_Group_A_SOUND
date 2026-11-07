-- Seed data for VEBRA project
USE vebra;

-- 1. Insert default users (passwords hashed with PASSWORD_BCRYPT)
-- 'admin123' -> $2y$10$eO1fD4c0dI2H3bY8L7gI7.OqKx0mDk6L9fH3j4k5l6m7n8o9p0q1r (we compute actual hash via PHP)
-- We will insert or update via PHP seed script.

-- 2. Ensure band artists exist
INSERT IGNORE INTO artists (name, bio, image) VALUES
('Mara Vance', 'Founding member, Vocals & Synths. Mara writes the first line of every VEBRA song on a battered Juno-60.', 'assets/images/team-1.jpg'),
('Elias Ward', 'Guitars. Angular chords and wall of sound tone shaping.', 'assets/images/team-2.jpg'),
('Nico Ferreira', 'Bass. Driving basslines that anchor VEBRA rhythm sections.', 'assets/images/team-3.jpg'),
('Junie Okafor', 'Drums & Percussion. Dynamic polyrhythms and visceral live energy.', 'assets/images/team-4.jpg'),
('Theo Lindqvist', 'Modular & Keys. Analog textures, soundscapes and synth wizardry.', 'assets/images/team-5.jpg'),
('Ada Reyes', 'Live Strings. Cinematic violin and viola arrangements on tour.', 'assets/images/team-6.jpg');

-- 3. Update existing songs with proper track titles if they have generic names
UPDATE songs SET title = 'Nightform', artwork = 'assets/images/album-1.jpg', release_year = 2026 WHERE id = 1 AND title = 'VEBRA Track 1';
UPDATE songs SET title = 'Halogen Kids', artwork = 'assets/images/album-2.jpg', release_year = 2026 WHERE id = 2 AND title = 'VEBRA Track 2';
UPDATE songs SET title = 'Amber Room', artwork = 'assets/images/album-3.jpg', release_year = 2026 WHERE id = 3 AND title = 'VEBRA Track 3';
UPDATE songs SET title = 'Static Bloom', artwork = 'assets/images/album-4.jpg', release_year = 2026 WHERE id = 4 AND title = 'VEBRA Track 4';
UPDATE songs SET title = 'Sirens, Slowly', artwork = 'assets/images/album-5.jpg', release_year = 2026 WHERE id = 5 AND title = 'VEBRA Track 5';

-- Update artist_id of songs to Mara Vance if it was demo artist
SET @mara_id = (SELECT id FROM artists WHERE name = 'Mara Vance' LIMIT 1);
IF @mara_id IS NOT NULL THEN
    UPDATE songs SET artist_id = @mara_id WHERE artist_id = 1 OR artist_id IS NULL;
    UPDATE videos SET artist_id = @mara_id WHERE artist_id = 1 OR artist_id IS NULL;
END IF;
