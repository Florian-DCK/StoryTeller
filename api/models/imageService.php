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
    // Log des entrées pour déboguer
    error_log("saveAvatar appelé avec : targetDir=$targetDir, userId=$userId, image=" . print_r($image, true));

    // Vérifier si le fichier est valide
    if (!isset($image['tmp_name']) || !is_uploaded_file($image['tmp_name'])) {
        throw new Exception("Fichier non uploadé ou invalide : " . ($image['error'] ?? 'Erreur inconnue'));
    }

    // Vérifier l'extension
    $extension = strtolower(pathinfo($image['name'] ?? '', PATHINFO_EXTENSION));
    if (empty($extension) || !in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
        throw new Exception("Extension de fichier non valide : $extension");
    }

    // Normaliser le dossier cible (ajouter un slash final si absent)
    $targetDir = rtrim($targetDir, '/') . '/';
    
    // Vérifier si le dossier existe et est inscriptible
    if (!is_dir($targetDir)) {
        error_log("Dossier $targetDir n'existe pas, tentative de création");
        if (!mkdir($targetDir, 0755, true)) {
            throw new Exception("Impossible de créer le dossier : $targetDir");
        }
    }
    if (!is_writable($targetDir)) {
        throw new Exception("Dossier non inscriptible : $targetDir");
    }

    // Construire le nom du fichier
    $filename = $userId . '_' . time() . '.' . $extension;
    $targetFile = $targetDir . $filename;
    error_log("Tentative de déplacement vers : $targetFile");

    // Déplacer le fichier
    if (!move_uploaded_file($image['tmp_name'], $targetFile)) {
        throw new Exception("Échec du déplacement du fichier vers $targetFile");
    }

    error_log("Fichier déplacé avec succès : $targetFile");
    return $targetFile;
}