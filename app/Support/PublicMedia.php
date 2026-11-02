<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PublicMedia
{
    public const BLOG_MAX_WIDTH = 1200;

    public const BLOG_MAX_HEIGHT = 2000;

    public const BLOG_JPEG_QUALITY = 85;

    /** @var list<string> */
    protected static array $imageSubdirs = [
        'technologies',
        'blogs',
        'blogs/content',
        'projects',
        'projects/content',
        'campaigns',
        'campaigns/content',
        'campaigns/templates',
    ];

    /**
     * Save an upload under public/images/{subdir}/ (web-accessible without storage:link).
     *
     * @param  array{max_width?: int, max_height?: int, quality?: int}  $resize
     */
    public static function storeImage(UploadedFile $file, string $subdir, array $resize = []): string
    {
        if (! $file->isValid()) {
            throw new \RuntimeException($file->getErrorMessage() ?: 'Invalid upload.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $allowed = ['svg', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
        if (! in_array($extension, $allowed, true)) {
            $extension = 'jpg';
        }

        $subdir = trim($subdir, '/');
        $destDir = public_path('images/' . $subdir);
        if (! is_dir($destDir) && ! mkdir($destDir, 0755, true) && ! is_dir($destDir)) {
            throw new \RuntimeException('Could not create directory: images/' . $subdir);
        }

        $filename = Str::uuid()->toString() . '.' . $extension;
        $absolutePath = $destDir . DIRECTORY_SEPARATOR . $filename;
        $file->move($destDir, $filename);

        if ($resize !== [] && $extension !== 'svg' && $extension !== 'gif') {
            self::resizeImageInPlace($absolutePath, $extension, $resize);
        }

        if (! in_array($extension, ['svg', 'gif', 'webp'], true) && function_exists('imagewebp')) {
            $webpName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
            $webpPath = $destDir . DIRECTORY_SEPARATOR . $webpName;
            $image = self::loadGdImage($absolutePath, $extension);
            if ($image) {
                if ($extension === 'png') {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                $saved = imagewebp($image, $webpPath, (int) ($resize['quality'] ?? 82));
                imagedestroy($image);
                if ($saved && is_file($webpPath)) {
                    @unlink($absolutePath);
                    $filename = $webpName;
                }
            }
        }

        return 'images/' . $subdir . '/' . $filename;
    }

    /**
     * Copy a local file into public/images/{subdir}/{filename} without renaming or resizing.
     * Used for email-template assets so HTML relative paths can be rewritten 1:1.
     */
    public static function storeFilesystemFile(string $sourcePath, string $subdir, string $filename): string
    {
        if (! is_file($sourcePath)) {
            throw new \RuntimeException('Archivo no encontrado: '.$filename);
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION) ?: '');
        $allowed = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        if (! in_array($extension, $allowed, true)) {
            throw new \RuntimeException('Extensión no permitida: '.($extension ?: 'desconocida'));
        }

        $safe = preg_replace('/[^A-Za-z0-9._-]/', '', str_replace(' ', '-', basename($filename))) ?: '';
        if ($safe === '' || str_starts_with($safe, '.')) {
            throw new \RuntimeException('Nombre de archivo no válido.');
        }

        $subdir = trim(str_replace('\\', '/', $subdir), '/');
        $subdir = preg_replace('#\.\.+#', '', $subdir) ?: 'campaigns/templates';
        $destDir = public_path('images/'.$subdir);
        if (! is_dir($destDir) && ! mkdir($destDir, 0755, true) && ! is_dir($destDir)) {
            throw new \RuntimeException('No se pudo crear images/'.$subdir);
        }

        $dest = $destDir.DIRECTORY_SEPARATOR.$safe;
        if (! @copy($sourcePath, $dest) || ! is_file($dest)) {
            throw new \RuntimeException('No se pudo copiar '.$safe);
        }

        return 'images/'.$subdir.'/'.$safe;
    }

    /**
     * Save a non-image file under public/{subdir}/ (e.g. videos/campaigns).
     *
     * @param  list<string>  $allowedExtensions
     */
    public static function storePublicFile(UploadedFile $file, string $subdir, array $allowedExtensions): string
    {
        if (! $file->isValid()) {
            throw new \RuntimeException($file->getErrorMessage() ?: 'Invalid upload.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: '');
        $allowedExtensions = array_map('strtolower', $allowedExtensions);
        if ($extension === '' || ! in_array($extension, $allowedExtensions, true)) {
            throw new \RuntimeException('Extensión no permitida: ' . ($extension ?: 'desconocida'));
        }

        $subdir = trim($subdir, '/');
        $destDir = public_path($subdir);
        if (! is_dir($destDir) && ! mkdir($destDir, 0755, true) && ! is_dir($destDir)) {
            throw new \RuntimeException('Could not create directory: ' . $subdir);
        }

        $filename = Str::uuid()->toString() . '.' . $extension;
        $file->move($destDir, $filename);

        return $subdir . '/' . $filename;
    }

    /** @return array{max_width: int, max_height: int, quality: int} */
    public static function blogImageResizeOptions(): array
    {
        return [
            'max_width' => self::BLOG_MAX_WIDTH,
            'max_height' => self::BLOG_MAX_HEIGHT,
            'quality' => self::BLOG_JPEG_QUALITY,
        ];
    }

    /**
     * @param  array{max_width?: int, max_height?: int, quality?: int}  $resize
     */
    protected static function resizeImageInPlace(string $path, string $extension, array $resize): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $maxWidth = $resize['max_width'] ?? self::BLOG_MAX_WIDTH;
        $maxHeight = $resize['max_height'] ?? self::BLOG_MAX_HEIGHT;
        $quality = $resize['quality'] ?? self::BLOG_JPEG_QUALITY;

        $info = @getimagesize($path);
        if ($info === false) {
            return;
        }

        [$width, $height] = $info;
        $scale = min($maxWidth / $width, $maxHeight / $height, 1.0);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $source = self::loadGdImage($path, $extension);
        if (! $source) {
            return;
        }

        if ($scale >= 1.0 && $width === $targetWidth && $height === $targetHeight) {
            self::saveGdImage($source, $path, $extension, $quality);
            imagedestroy($source);

            return;
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($extension === 'png' || $extension === 'webp') {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        self::saveGdImage($canvas, $path, $extension, $quality);
        imagedestroy($canvas);
    }

    protected static function loadGdImage(string $path, string $extension): ?\GdImage
    {
        return match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path) ?: null,
            'png' => @imagecreatefrompng($path) ?: null,
            'webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            default => null,
        };
    }

    protected static function saveGdImage(\GdImage $image, string $path, string $extension, int $quality): void
    {
        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $path, $quality),
            'png' => imagepng($image, $path, 8),
            'webp' => function_exists('imagewebp') ? imagewebp($image, $path, $quality) : imagejpeg($image, str_replace('.webp', '.jpg', $path), $quality),
            default => imagejpeg($image, $path, $quality),
        };
    }

    /**
     * Resolve a DB media path to a browser URL, or null if file is missing.
     */
    public static function url(?string $path, ?int $version = null): ?string
    {
        $webPath = self::resolveWebPath($path);
        if (! $webPath) {
            return null;
        }

        $url = Branding::assetUrl($webPath);

        if ($version !== null) {
            return $url . '?v=' . $version;
        }

        return $url;
    }

    /**
     * Web path under public/ that exists on disk, if any.
     */
    public static function resolveWebPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $basename = basename($path);
        $candidates = [];

        if (str_starts_with($path, 'images/')) {
            $candidates[] = $path;
        } else {
            $dir = dirname($path);
            if ($dir !== '.' && $dir !== '') {
                $candidates[] = 'images/' . $dir . '/' . $basename;
            }

            foreach (self::$imageSubdirs as $subdir) {
                $candidates[] = 'images/' . $subdir . '/' . $basename;
            }

            $candidates[] = 'storage/' . $path;
        }

        foreach (array_unique($candidates) as $webPath) {
            if (self::webFileExists($webPath)) {
                return $webPath;
            }
        }

        // Hostinger: file only under storage/app/public — copy into public/images so it is web-visible.
        $storageRelative = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;
        $storageFile = storage_path('app/public/' . $storageRelative);
        if (is_file($storageFile)) {
            $destRel = str_starts_with($storageRelative, 'images/')
                ? $storageRelative
                : 'images/' . ltrim($storageRelative, '/');
            $destFull = public_path($destRel);
            $destDir = dirname($destFull);
            if (! is_dir($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            if (@copy($storageFile, $destFull) && is_file($destFull)) {
                return $destRel;
            }
        }

        return null;
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'images/')) {
            $full = public_path($path);
            if (is_file($full)) {
                @unlink($full);
            }

            return;
        }

        $storagePublic = storage_path('app/public/' . $path);
        if (is_file($storagePublic)) {
            @unlink($storagePublic);
        }

        $publicStorage = public_path('storage/' . $path);
        if (is_file($publicStorage)) {
            @unlink($publicStorage);
        }
    }

    protected static function webFileExists(string $webPath): bool
    {
        $webPath = ltrim($webPath, '/');

        if (str_starts_with($webPath, 'storage/')) {
            $relative = substr($webPath, strlen('storage/'));

            return is_file(public_path('storage/' . $relative));
        }

        return is_file(public_path($webPath));
    }
}
