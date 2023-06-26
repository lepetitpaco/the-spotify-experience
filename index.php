<?php include('../login_redirect.php'); ?>
<?php

// PHP code to get user playlists using Spotify API

// Set up the API credentials
// These should be stored in a separate file and not pushed to GitHub
include('../env/api_credentials.php');


// Set up the API request to get the user's playlists
$user_id = isset($_GET['id']) ? $_GET['id'] : $default_user;
$api_url = "https://api.spotify.com/v1/users/" . $user_id . "/playlists";

$auth_options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
        'content' => http_build_query(
            array(
                'grant_type' => 'client_credentials'
            )
        )
    )
);

$context = stream_context_create($auth_options);
$response = file_get_contents('https://accounts.spotify.com/api/token', false, $context);

$access_token = json_decode($response)->access_token;

$api_headers = array(
    "Authorization: Bearer " . $access_token
);


// Get the user's playlists with pages of 50 playlists
$playlists_infos = array();
$next_url = $api_url . "?limit=50";
while ($next_url) {
    $api_response = file_get_contents($next_url, false, stream_context_create(
        array(
            'http' => array(
                'method' => 'GET',
                'header' => implode("\r\n", $api_headers)
            )
        )
    )
    );
    $playlists = json_decode($api_response, true);
    $playlists_infos = array_merge($playlists_infos, $playlists['items']);
    $next_url = $playlists['next'];
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Playlists</title>
    <style>
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            grid-gap: 1rem;
            justify-items: center;
            margin: 0 auto;
            max-width: 1000px;
        }

        .grid-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 200px;
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.5s ease-in-out;
            padding: 10px;
            z-index: 1;
            border-radius: 15px;
            margin: 0 auto;
        }
  


        .grid-item a {
            position: relative;
            transition: all 0.5s ease-in-out;
            transform: scale(1);
            border-radius: 15px;
            background-color: rgb(0, 255, 0, .5);
        }

        .grid-item:hover {
            z-index: 9999;
        }

        .grid-item:hover a {
            background-color: rgb(0, 255, 0, 1);
            transform: scale(1.2);
            z-index: 9999;
        }

        .grid-item img {
            width: 100%;
            height: auto;
            border-radius: 15px 15px 0 0;
        }

        .grid-item.show {
            opacity: 1;
            transform: translateY(0);
        }

        a {
            color: green;
        }

        a:hover {
            color: green;
            text-decoration: none;
        }
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
<script>
    $(window).on('load', function () {
        var grid = document.querySelector('.grid');
        var msnry = new Masonry(grid, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-item',
        });
        // grid.style.display = 'flex';
        // grid.style.flexWrap = 'wrap';
        // grid.style.justifyContent = 'center';
        // grid.style.alignItems = 'center';

        var gridItems = document.querySelectorAll('.grid-item');
        gridItems.forEach(function (item) {
            item.classList.add('show');
        });
    });
</script>
</body>

</html>