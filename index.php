<?php
// This code retrieves the user's playlists from the Spotify API and stores them in an array.
// It also handles errors if the access token cannot be retrieved or if there are no playlists found.
// Redirect if not logged in
include('../login_redirect.php');

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
require __DIR__ . '/vendor/autoload.php';
require __DIR__.'/../../config/config.php';
$dotenv = Dotenv\Dotenv::createImmutable(getEnvFilePath());
$dotenv->load();

// Set up the API credentials
$spotify_client_id = $_ENV['SPOTIFY_CLIENT_ID'];
$spotify_client_secret = $_ENV['SPOTIFY_CLIENT_SECRET'];
$spotify_default_user = $_ENV['SPOTIFY_DEFAULT_USER'];

// Set up the API request to get the user's playlists
$user_id = $_GET['id'] ?? $spotify_default_user;
$api_url = "https://api.spotify.com/v1/users/" . $user_id . "/playlists";

// Set up authentication options
$auth_options = [
    'http' => [
        'method' => 'POST',
        'header' => [
            'Authorization: Basic ' . base64_encode($spotify_client_id . ':' . $spotify_client_secret),
            'Content-Type: application/x-www-form-urlencoded'
        ],
        'content' => http_build_query([
            'grant_type' => 'client_credentials'
        ])
    ]
];

// Create stream context for authentication
$context = stream_context_create($auth_options);

// Get access token from Spotify API
$response = file_get_contents('https://accounts.spotify.com/api/token', false, $context);

// Check if the response is not null before decoding it
if ($response !== null) {
    $access_token = json_decode($response)->access_token;
} else {
    // Handle the error if the response is null
    echo "Error: Unable to retrieve access token.";
    exit();
}

$api_headers = [
    "Authorization: Bearer " . $access_token,
    "Content-Type: application/json"
];

// Get the user's playlists with pages of 50 playlists
$playlists_infos = [];
$next_url = $api_url . "?limit=50";
while ($next_url) {
    $api_response = file_get_contents(
        $next_url,
        false,
        stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $api_headers)
            ]
        ])
    );
    $playlists = json_decode($api_response, true);
    $playlists_infos = array_merge($playlists_infos, $playlists['items']);
    $next_url = $playlists['next'];
}

// Handle case where no playlists are found
if (empty($playlists_infos)) {
    echo "No playlists found.";
    exit();
}

// Get user's display name
$user_id = $_GET['id'] ?? $spotify_default_user;
$api_url = "https://api.spotify.com/v1/users/" . $user_id;

$user_response = file_get_contents(
    $api_url,
    false,
    stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $api_headers)
        ]
    ])
);

$user_info = json_decode($user_response, true);
$user_display_name = $user_info['display_name'] ?? $user_info['id']; // set user's display name to either the display name or the user ID
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title><?php echo $user_display_name ?>'s Playlists</title>

    <style>
        .grid {display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 1rem; margin: 0 auto; max-width: 1000px;}
        .grid-item {display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 200px; opacity: 0; transform: translateY(50px); transition: all 0.5s ease-in-out; padding: 10px; z-index: 1; border-radius: 15px; margin: 0 auto;}
        .grid-item a {position: relative; transition: all 0.5s ease-in-out; transform: scale(1); border-radius: 15px; background-color: rgb(0, 255, 0, .5);}
        .grid-item:hover {z-index: 9999;}
        .grid-item:hover a {background-color: rgb(0, 255, 0, 1); transform: scale(1.2); z-index: 9999;}
        .grid-item img {width: 100%; height: auto; border-radius: 15px 15px 0 0;}
        .grid-item.show {opacity: 1; transform: translateY(0);}
        a {color: green;}
        a:hover {color: green; text-decoration: none;}
    </style>
</head>

<body>
    <div class="grid justify-content-center">
        <?php if (!empty($playlists_infos)): ?>
            <?php foreach ($playlists_infos as $index => $playlist): ?>
                <div class="grid-item">
                    <a href="<?php echo htmlspecialchars($playlist['external_urls']['spotify'], ENT_QUOTES, 'UTF-8'); ?>"
                        target="_blank">
                        <?php if (empty($playlist['images'])): ?>
                            <img src="https://static.vecteezy.com/system/resources/previews/006/541/760/original/spotify-logo-on-black-background-free-vector.jpg"
                                alt="<?php echo htmlspecialchars($playlist['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($playlist['images'][0]['url'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?php echo htmlspecialchars($playlist['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>

                        <span>
                            <?php echo htmlspecialchars($playlist['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                        <p>
                            <?php echo htmlspecialchars($playlist['tracks']['total'], ENT_QUOTES, 'UTF-8'); ?> songs
                        </p>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No playlists found.</p>
        <?php endif; ?>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>
<!-- This block is responsible for Masonry layout of the playlists -->
<script>
    $(window).on('load', function () {
        var grid = document.querySelector('.grid');
        var msnry = new Masonry(grid, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-item',
        });
        var gridItems = document.querySelectorAll('.grid-item');
        gridItems.forEach(function (item) {
            item.classList.add('show');
        });
    });
</script>

</body>

</html>
