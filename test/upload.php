<?php
ini_set('max_file_uploads', '50');

$target_dir = __DIR__ . "/uploads/";
$type = isset($_POST['type']) ? $_POST['type'] : "";


if ($type == "delete") {
    $delete_path = $_POST['path'];
    @unlink($target_dir . $delete_path);
    echo json_encode(true);
    exit;
}

if ($type == "getfile") {
    $directoryPath = $target_dir . $_POST['path'];

    $image = [];
    $media = [];
    // Check if the directory exists
    if (is_dir($directoryPath)) {
        // Open the directory
        if ($dh = opendir($directoryPath)) {
            // Create a new finfo object
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            // Loop through the files in the directory
            while (($file = readdir($dh)) !== false) {
                $filePath = $directoryPath . DIRECTORY_SEPARATOR . $file;

                // Skip '.' and '..' and directories
                if ($file !== '.' && $file !== '..' && !is_dir($filePath)) {
                    // Get the MIME type of the file
                    $mimeType = $finfo->file($filePath);

                    if (strpos($mimeType, 'image/') === 0) {
                        $image[] = basename($filePath);
                    } else if (strpos($mimeType, 'video/') === 0) {
                        $media[] = basename($filePath);
                    }
                }
            }
            closedir($dh);
        }
    }
    echo json_encode([
        "image" => $image,
        "media" => $media
    ]);
    exit;
}

$sub_dir = $_POST['sub_dir'];

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (!is_dir($target_dir . $sub_dir)) {
    mkdir($target_dir . $sub_dir, 0777, true);
}

$target_dir = $target_dir . $sub_dir . '/';

$image = [];
$media = [];
$error = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    $total = count($_FILES['files']['name']);

    for ($i = 0; $i < $total; $i++) {
        $micro_time = microtime(true);
        $target_file = $target_dir . $micro_time . '.' . basename($_FILES["files"]["name"][$i]);
        $file_name = $micro_time . '.' . basename($_FILES["files"]["name"][$i]);

        // Check if file is an image or video
        $mimeType = mime_content_type($_FILES["files"]["tmp_name"][$i]);
        $check = getimagesize($_FILES["files"]["tmp_name"][$i]);

        if ((strpos($mimeType, 'image/') === 0 || strpos($mimeType, 'video/') === 0)) {
            if (move_uploaded_file($_FILES["files"]["tmp_name"][$i], $target_file)) {
                if (strpos($mimeType, 'image/') === 0) {
                    $image[] = $file_name;
                } elseif (strpos($mimeType, 'video/') === 0) {
                    $media[] = $file_name;
                }
            } else {
                $error[] = "Sorry, there was an error uploading your file: " . htmlspecialchars(basename($_FILES["files"]["name"][$i]));
            }
        } else {
            $error[] = "File is not an image or video: " . htmlspecialchars(basename($_FILES["files"]["name"][$i]));
        }
    }
} else {
    $error[] = "No files uploaded.";
}

echo json_encode([
    "image" => $image,
    "media" => $media,
    "error" => $error
]);
return exit;
