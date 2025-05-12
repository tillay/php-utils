<?php
$file = 'notebook.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($file, $_POST['content']);
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['timestamp' => time()]);
        exit;
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['check_modified'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'timestamp' => filemtime($file),
        'content' => file_exists($file) ? file_get_contents($file) : ''
    ]);
    exit;
}

$savedContent = file_exists($file) ? file_get_contents($file) : '';
$lastModified = file_exists($file) ? filemtime($file) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TilNote</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background-color: #121212; color: #e0e0e0; }
        h1 { text-align: center; color: #8511ff; }
        textarea { width: 100%; height: 400px; padding: 10px; margin: 0; border: 1px solid #444; border-radius: 4px; font-size: 16px; box-sizing: border-box; background-color: #383A40; color: #e0e0e0; resize: vertical; }
    </style>
</head>
<body>
<h1>TilNote</h1>
<div>
    <textarea id="content" placeholder="Type here..."><?php echo htmlspecialchars($savedContent); ?></textarea>
</div>
<script>
    const textarea = document.getElementById('content');
    let lastSavedContent = textarea.value;
    let fileTimestamp = <?php echo $lastModified; ?>;

    textarea.addEventListener('input', () => {
        if (textarea.value !== lastSavedContent) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = () => {
                lastSavedContent = textarea.value;
                fileTimestamp = JSON.parse(xhr.responseText).timestamp;
            };
            xhr.send('content=' + encodeURIComponent(textarea.value));
        }
    });

    setInterval(() => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.href + '?check_modified=1', true);
        xhr.onload = () => {
            const response = JSON.parse(xhr.responseText);
            if (response.content !== textarea.value) {
                textarea.value = response.content;
                lastSavedContent = response.content;
            }
            fileTimestamp = response.timestamp;
        };
        xhr.send();
    }, 3000);
</script>
</body>
</html>
