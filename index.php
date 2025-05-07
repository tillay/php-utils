<?php
$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$files = scandir($currentDir);
$rootDir = htmlspecialchars($_SERVER['PHP_SELF']);

function upload_file($file, $subdir) {
    $tmpFilePath = $file['tmp_name'];
    $targetFileName = basename($file['name']);
    if (substr($subdir, -1) != '/') $subdir .= '/';
    return move_uploaded_file($tmpFilePath, $subdir . $targetFileName);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    upload_file($_FILES['fileToUpload'], $currentDir);
    header("Location: " . $rootDir . "?dir=" . urlencode($currentDir) . "&uploaded=1");
    exit;
}

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tillexplorer</title>
    <style>
         body{font-family:Arial,sans-serif; margin:20px; background-color:#121212; color:#e0e0e0}
        .container{padding:10px; background:#282828; border-radius:8px;}
        .file-list{list-style-type:none; padding:0; margin:0}
        .file-item{margin-bottom:8px}
        .file-link{display:block; background-color:#383A40; padding:10px 15px; color:#e0e0e0; border-radius:4px; text-decoration:none}
        .dir{color:#89CFF0}
        .file{color:#c677ff}
        .path-container{display:flex; align-items:center; margin-bottom:15px}
         button, .back-button{color:#e0e0e0; background-color:rgba(0,0,0,0); border:none; border-radius:4px; padding:10px; text-decoration:none;}
         button:hover, .back-button:hover, .file-link:hover{background-color:rgba(174,117,229,0.3);}
        .current-path{flex-grow:1; padding:10px; background-color:#1E1E1E; border-radius:4px; font-family:monospace}
        .hidden-file-input{display:none}
    </style>
    <script>
        function triggerFileInput() {document.getElementById("fileToUpload").click();}
        function submitForm() {document.getElementById("uploadForm").submit();}
    </script>
</head>
<body>
    <div class="container">
        <div class="path-container">
            <a href="' . $rootDir . '" class="back-button"> 🛆 </a>
            <a href="' . $rootDir . '?dir=' . htmlspecialchars(dirname($currentDir)) . '" class="back-button">• •</a>
            <div class="current-path">' . getcwd() . '/' . htmlspecialchars($currentDir) . '</div>
            <button class="upload-button" onclick="triggerFileInput()">Upload</button>
            <form id="uploadForm" method="post" enctype="multipart/form-data" style="display:none;">
                <input type="file" name="fileToUpload" id="fileToUpload" class="hidden-file-input" onchange="submitForm()">
            </form>
        </div>
        
    <ul class="file-list">';

if (mb_substr($currentDir, 0, 1) != '/' && is_dir($currentDir)) {
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != 'index.php') {
            $filePath = $currentDir == '.' ? $file : $currentDir . '/' . $file;
            if (is_dir($filePath)) {
                echo '<li class="file-item"><a class="file-link dir" href="' . $rootDir . '?dir=' . htmlspecialchars($filePath) . '">📁 ' . htmlspecialchars($file) . '</a></li>';
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
    header("Location: $rootDir");
    exit;
}
echo '</ul>
    </div>
</body>
</html>';
