<?php


function checkValidity($image) {
    $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($image['type'], $validMimeTypes)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, and GIF are allowed.');
    }

    if ($image['size'] > $maxFileSize) {
        throw new Exception('File size exceeds the maximum limit of 5MB.');
    }

    return true;
}

function saveAvatar($image, $targetDir, $userId) {
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $filename = $userId . '_' . time() . '.' . $extension;
    $targetFile = $targetDir . $filename;
    if (move_uploaded_file($image['tmp_name'], $targetFile)) {
        $relativePath = strstr($targetFile, 'api');
        $relativePath = str_replace('\\', '/', $relativePath);
        $relativePath = preg_replace('~/[^/]+/\.\.~', '', $relativePath);
        return $relativePath;
    } else {
        throw new Exception('Error uploading the avatar.');
    }
}