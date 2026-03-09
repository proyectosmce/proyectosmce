<?php

function image_helper_supported_extensions(): array
{
    return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
}

function image_helper_can_optimize(string $extension): bool
{
    $extension = strtolower($extension);

    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return false;
    }

    if (!function_exists('imagecreatetruecolor') || !function_exists('getimagesize')) {
        return false;
    }

    $required = [
        'jpg' => 'imagecreatefromjpeg',
        'jpeg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',
        'webp' => 'imagecreatefromwebp',
    ];

    return isset($required[$extension]) && function_exists($required[$extension]);
}

function image_helper_resize_and_save(string $sourcePath, string $extension, string $destinationPath, array $options = []): bool
{
    $imageInfo = @getimagesize($sourcePath);
    if ($imageInfo === false) {
        return false;
    }

    [$width, $height] = $imageInfo;
    if ($width <= 0 || $height <= 0) {
        return false;
    }

    $maxWidth = max(1, (int) ($options['max_width'] ?? $width));
    $maxHeight = max(1, (int) ($options['max_height'] ?? $height));
    $jpegQuality = max(60, min(92, (int) ($options['jpeg_quality'] ?? 82)));
    $webpQuality = max(60, min(92, (int) ($options['webp_quality'] ?? 80)));
    $pngCompression = max(0, min(9, (int) ($options['png_compression'] ?? 6)));

    $scale = min($maxWidth / $width, $maxHeight / $height, 1);
    $targetWidth = max(1, (int) round($width * $scale));
    $targetHeight = max(1, (int) round($height * $scale));

    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $sourceImage = @imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $sourceImage = @imagecreatefrompng($sourcePath);
            break;
        case 'webp':
            $sourceImage = @imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    $destinationImage = imagecreatetruecolor($targetWidth, $targetHeight);
    if (!$destinationImage) {
        imagedestroy($sourceImage);
        return false;
    }

    if (in_array($extension, ['png', 'webp'], true)) {
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);
        $transparent = imagecolorallocatealpha($destinationImage, 0, 0, 0, 127);
        imagefilledrectangle($destinationImage, 0, 0, $targetWidth, $targetHeight, $transparent);
    }

    $resampled = imagecopyresampled(
        $destinationImage,
        $sourceImage,
        0,
        0,
        0,
        0,
        $targetWidth,
        $targetHeight,
        $width,
        $height
    );

    if (!$resampled) {
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);
        return false;
    }

    $saved = false;
    if ($extension === 'jpg' || $extension === 'jpeg') {
        $saved = imagejpeg($destinationImage, $destinationPath, $jpegQuality);
    } elseif ($extension === 'png') {
        $saved = imagepng($destinationImage, $destinationPath, $pngCompression);
    } elseif ($extension === 'webp') {
        $saved = imagewebp($destinationImage, $destinationPath, $webpQuality);
    }

    imagedestroy($sourceImage);
    imagedestroy($destinationImage);

    return (bool) $saved;
}

function image_helper_store_upload(array $file, string $destinationDirectory, string $filenamePrefix, array $options = []): array
{
    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($errorCode !== UPLOAD_ERR_OK) {
        return [
            'ok' => false,
            'error' => 'No se pudo procesar la imagen subida.',
        ];
    }

    $originalName = (string) ($file['name'] ?? '');
    $tmpPath = (string) ($file['tmp_name'] ?? '');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, image_helper_supported_extensions(), true)) {
        return [
            'ok' => false,
            'error' => 'La imagen debe estar en formato JPG, PNG, GIF o WEBP.',
        ];
    }

    if (@getimagesize($tmpPath) === false) {
        return [
            'ok' => false,
            'error' => 'El archivo subido no es una imagen valida.',
        ];
    }

    if (!is_dir($destinationDirectory) && !mkdir($destinationDirectory, 0777, true) && !is_dir($destinationDirectory)) {
        return [
            'ok' => false,
            'error' => 'No se pudo preparar la carpeta de imagenes.',
        ];
    }

    $filename = uniqid($filenamePrefix, true) . '.' . $extension;
    $destinationPath = rtrim($destinationDirectory, '/\\') . DIRECTORY_SEPARATOR . $filename;
    $optimized = false;

    if (image_helper_can_optimize($extension)) {
        $optimized = image_helper_resize_and_save($tmpPath, $extension, $destinationPath, $options);
    }

    if (!$optimized && !move_uploaded_file($tmpPath, $destinationPath)) {
        return [
            'ok' => false,
            'error' => 'No se pudo guardar la imagen en el servidor.',
        ];
    }

    return [
        'ok' => true,
        'filename' => $filename,
        'path' => $destinationPath,
        'optimized' => $optimized,
    ];
}
