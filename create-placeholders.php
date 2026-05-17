<?php
// Generate placeholder JPEGs for homepage

$images = [
    'hero-dashboard-preview-1200x900.jpg' => [1200, 900],
    'employer-workflow-1200x800.jpg' => [1200, 800],
    'candidate-opportunity-1200x800.jpg' => [1200, 800],
    'insights-modern-work-1200x700.jpg' => [1200, 700],
];

foreach ($images as $filename => [$w, $h]) {
    $img = imagecreatetruecolor($w, $h);
    $color = imagecolorallocate($img, 15, 23, 42); // Dark slate background
    imagefill($img, 0, 0, $color);
    
    $path = __DIR__ . '/public/assets/pages/home/' . $filename;
    @mkdir(dirname($path), 0755, true);
    
    imagejpeg($img, $path, 85);
    imagedestroy($img);
    echo "Created $filename\n";
}

echo "\nPlaceholder images created successfully.\n";
