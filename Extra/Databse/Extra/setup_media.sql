START TRANSACTION;

-- Reuse or create the artist
INSERT INTO artists (name, bio, image)
SELECT 'VEBRA Demo Artist', 'Demo artist for the existing VEBRA media library.', ''
WHERE NOT EXISTS (
    SELECT 1 FROM artists WHERE name = 'VEBRA Demo Artist'
);

SET @artist_id = (
    SELECT id FROM artists
    WHERE name = 'VEBRA Demo Artist'
    LIMIT 1
);

SET @genre_id = (
    SELECT id FROM genres
    WHERE name = 'Electronic'
    LIMIT 1
);

-- Five existing MP3 files
INSERT INTO songs
(title, artist_id, genre_id, language, release_year, artwork, audio_path, status)
SELECT 'VEBRA Track 1', @artist_id, @genre_id, 'English', 2026,
       'assets/images/album-1.jpg', 'assets/musics/1.mp3', 'approved'
WHERE NOT EXISTS (SELECT 1 FROM songs WHERE title = 'VEBRA Track 1');

INSERT INTO songs
(title, artist_id, genre_id, language, release_year, artwork, audio_path, status)
SELECT 'VEBRA Track 2', @artist_id, @genre_id, 'English', 2026,
       'assets/images/album-2.jpg', 'assets/musics/2.mp3', 'approved'
WHERE NOT EXISTS (SELECT 1 FROM songs WHERE title = 'VEBRA Track 2');

INSERT INTO songs
(title, artist_id, genre_id, language, release_year, artwork, audio_path, status)
SELECT 'VEBRA Track 3', @artist_id, @genre_id, 'English', 2026,
       'assets/images/album-3.jpg', 'assets/musics/3.mp3', 'approved'
WHERE NOT EXISTS (SELECT 1 FROM songs WHERE title = 'VEBRA Track 3');

INSERT INTO songs
(title, artist_id, genre_id, language, release_year, artwork, audio_path, status)
SELECT 'VEBRA Track 4', @artist_id, @genre_id, 'English', 2026,
       'assets/images/album-4.jpg', 'assets/musics/4.mp3', 'approved'
WHERE NOT EXISTS (SELECT 1 FROM songs WHERE title = 'VEBRA Track 4');

INSERT INTO songs
(title, artist_id, genre_id, language, release_year, artwork, audio_path, status)
SELECT 'VEBRA Track 5', @artist_id, @genre_id, 'English', 2026,
       'assets/images/album-5.jpg', 'assets/musics/5.mp3', 'approved'
WHERE NOT EXISTS (SELECT 1 FROM songs WHERE title = 'VEBRA Track 5');

-- One existing MP4 video
INSERT INTO videos
(title, artist_id, genre_id, language, release_year, thumbnail, video_path, video_url, status)
SELECT 'VEBRA Video 1', @artist_id, @genre_id, 'English', 2026,
       'assets/images/video-cover-1.jpg', 'assets/videos/1.mp4', '', 'approved'
WHERE NOT EXISTS (SELECT 1 FROM videos WHERE title = 'VEBRA Video 1');

COMMIT;
