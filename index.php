<?php

$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$files = scandir($currentDir);
$rootDir = htmlspecialchars($_SERVER['PHP_SELF']);

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tillexplorer</title>
    <style>
        body{font-family:Arial,sans-serif; margin:20px; background-color:#121212; color:#e0e0e0}
        .container{padding:10px; background:#282828; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1)}
        h2, h3{text-align:center; color:#9b4afb;}
        .file-list{list-style-type:none; padding:0; margin:0}
        .file-item{margin-bottom:8px}
        .file-link{display:block; background-color:#383A40; padding:10px 15px; color:#e0e0e0; border-radius:4px; text-decoration:none}
        .dir{color:#89CFF0}
        .file{color:#c677ff}
        .file-link:hover{background-color:#9b4afb}
        .path-container{display:flex; align-items:center; margin-bottom:15px}
        .back-button{color:#e0e0e0; border:none; border-radius:4px; padding:10px; margin-right:10px; text-decoration:none; font-weight:bold}
        .back-button:hover{background-color:#9b4afb; cursor:pointer}
        .current-path{flex-grow:1; padding:10px; background-color:#1E1E1E; border-radius:4px; font-family:monospace}
    </style>
</head>
<body>
    <div class="container">
        <div class="path-container">
            <a href="' . $rootDir . '" class="back-button"> 🛆 </a>
            <a href="' . $rootDir . '?dir=' . htmlspecialchars(dirname($currentDir)) . '" class="back-button">• •</a>
            <div class="current-path">' . getcwd() . '/' . htmlspecialchars($currentDir) . '</div>
        </div>
        <ul class="file-list">';
if (mb_substr($currentDir, 0, 1) != '/' && is_dir($currentDir)) {
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != 'index.php') {
            $filePath = $currentDir == '.' ? $file : $currentDir . '/' . $file;

            if (is_dir($filePath)) {
                echo '<li class="file-item"><a class="file-link dir" href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?dir=' . htmlspecialchars($filePath) . '">📁 ' . htmlspecialchars($file) . '</a></li>';
            } elseif (is_file($filePath)) {
                $emoji = '📄';

                if (getimagesize($filePath) !== false) {
                    $emoji = '🖼️';
                }
                elseif (0 === strpos(mime_content_type($filePath), 'video/')) {
                    $emoji = '🎬';
                }

                echo '<li class="file-item"><a class="file-link file" href="' . htmlspecialchars($filePath) . '"> ' . $emoji . ' ' . htmlspecialchars($file) . '</a></li>';
            }
        }
    }
}
else {
    echo '<h2>Directory not found! (404)</h2><h3>Dont modify the url text</h3>';
}
echo '        </ul>
    </div>
</body>
</html>';
