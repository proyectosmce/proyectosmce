<?php

function fetchPortfolioProjects(mysqli $conn): array
{
    $projects = [];
    $sql = "SELECT id, titulo, descripcion, imagen, categoria, url_demo, url_repo, cliente, fecha_completado, destacado, orden
            FROM proyectos
            ORDER BY destacado DESC, orden ASC, id DESC";

    $result = $conn->query($sql);
    if (!$result instanceof mysqli_result) {
        return $projects;
    }

    while ($row = $result->fetch_assoc()) {
        $row['categoria'] = trim((string) ($row['categoria'] ?? '')) ?: 'Sin categoria';
        $row['public_url'] = getProjectPublicUrl($row);
        $row['image_url'] = getProjectImageUrl($row);
        $projects[] = $row;
    }

    $result->free();

    return $projects;
}

function fetchProjectDropdownOptions(mysqli $conn): array
{
    $options = [];

    foreach (fetchPortfolioProjects($conn) as $project) {
        $options[] = [
            'id' => (int) ($project['id'] ?? 0),
            'titulo' => (string) ($project['titulo'] ?? ''),
            'categoria' => (string) ($project['categoria'] ?? ''),
        ];
    }

    return $options;
}

function fetchPortfolioCategories(array $projects): array
{
    $categories = [];

    foreach ($projects as $project) {
        $category = trim((string) ($project['categoria'] ?? ''));
        if ($category === '' || isset($categories[$category])) {
            continue;
        }

        $categories[$category] = true;
    }

    return array_keys($categories);
}

function getProjectPublicUrl(array $project): string
{
    $url = trim((string) ($project['url_demo'] ?? ''));
    if ($url !== '') {
        if (preg_match('~^(https?://|/|#)~i', $url) === 1) {
            return $url;
        }

        return app_url($url);
    }

    $customRoutes = [
        'destello de oro 18k' => app_url('destello-oro.php'),
    ];

    $title = trim((string) ($project['titulo'] ?? ''));
    if ($title !== '') {
        $norm = mb_strtolower($title, 'UTF-8');
        if (isset($customRoutes[$norm])) {
            return $customRoutes[$norm];
        }
    }

    return '#';
}

function isExternalProjectUrl(string $url): bool
{
    return preg_match('~^https?://~i', $url) === 1;
}

function getProjectImageUrl(array $project): string
{
    $image = trim((string) ($project['imagen'] ?? ''));
    if ($image === '') {
        return app_url('fondo.jpeg');
    }

    if (preg_match('~^(https?://|/)~i', $image) === 1) {
        return $image;
    }

    $normalized = str_replace('\\', '/', $image);

    if (strpos($normalized, '/') !== false) {
        return projectAssetUrl($normalized);
    }

    $assetsImage = __DIR__ . '/../assets/img/' . $normalized;
    if (is_file($assetsImage)) {
        return projectAssetUrl('assets/img/' . $normalized);
    }

    $rootImage = __DIR__ . '/../' . $normalized;
    if (is_file($rootImage)) {
        return projectAssetUrl($normalized);
    }

    return projectAssetUrl('assets/img/' . $normalized);
}

function projectAssetUrl(string $path): string
{
    $normalized = trim(str_replace('\\', '/', $path), '/');
    $segments = array_map('rawurlencode', explode('/', $normalized));

    return app_url(implode('/', $segments));
}
