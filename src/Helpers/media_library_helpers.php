<?php

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;

if (!function_exists('s3Endpoint')) {
    function s3Endpoint(string $disk = 'libraries'): string
    {
        $diskInstance = Storage::disk($disk);

        $adapter = $diskInstance->getAdapter();

        if (is_callable([$adapter, 'getClient'])) {
            $s3Client = $adapter->getClient();
        } else {
            $s3Client = new S3Client($diskInstance->getConfig());
        }

        $s3PostObject = new PostObjectV4($s3Client, config(sprintf('filesystems.disks.%s.bucket', $disk)), []);

        return $s3PostObject->getFormAttributes()['action'];
    }
}

if (!function_exists('azureEndpoint')) {
    function azureEndpoint(string $disk = 'libraries'): string
    {
        $scheme = config(sprintf('filesystems.disks.%s.use_https', $disk)) ? 'https://' : '';
        return $scheme . config(sprintf('filesystems.disks.%s.name', $disk)) . '.blob.' . config(sprintf('filesystems.disks.%s.endpoint-suffix', $disk)) . '/' . config(sprintf('filesystems.disks.%s.container', $disk));
    }
}

if (!function_exists('bytesToHuman')) {
    function bytesToHuman(float $bytes): string
    {
        $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb'];

        for ($i = 0; $bytes > 1024; ++$i) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('replaceAccents')) {
    function replaceAccents(string $str): bool|string
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    }
}

if (!function_exists('sanitizeFilename')) {
    function sanitizeFilename(string $filename): string
    {
        $sanitizedFilename = replaceAccents($filename);

        $invalid = array(
            ' ' => '-',
            '%20' => '-',
            '_' => '-',
        );

        $sanitizedFilename = str_replace(array_keys($invalid), array_values($invalid), $sanitizedFilename);

        $sanitizedFilename = preg_replace('#[^A-Za-z0-9-\. ]#', '', $sanitizedFilename); // Remove all non-alphanumeric except .
        $sanitizedFilename = preg_replace('#\.(?=.*\.)#', '', $sanitizedFilename); // Remove all but last .
        $sanitizedFilename = preg_replace('#-+#', '-', $sanitizedFilename); // Replace any more than one - in a row
        $sanitizedFilename = str_replace('-.', '.', $sanitizedFilename); // Remove last - if at the end
        $sanitizedFilename = strtolower($sanitizedFilename); // Lowercase

        return $sanitizedFilename;
    }
}
