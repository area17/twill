<?php

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;

if (!function_exists('s3Endpoint')) {
    /**
     * @param string $disk
     * @return string
     */
    function s3Endpoint($disk = 'libraries')
    {
        $diskInstance = Storage::disk($disk);

        $adapter = $diskInstance->getAdapter();

        if (is_callable([$adapter, 'getClient'])) {
            $s3Client = $adapter->getClient();
        } else {
            $s3Client = new S3Client($diskInstance->getConfig());
        }

        $s3PostObject = new PostObjectV4($s3Client, config("filesystems.disks.{$disk}.bucket"), []);

        return $s3PostObject->getFormAttributes()['action'];
    }
}

if (!function_exists('azureEndpoint')) {
    /**
     * @param string $disk
     * @return string
     */
    function azureEndpoint($disk = 'libraries')
    {
        $scheme = config("filesystems.disks.{$disk}.use_https") ? 'https://' : '';
        return $scheme . config("filesystems.disks.{$disk}.name") . '.blob.' . config("filesystems.disks.{$disk}.endpoint-suffix") . '/' . config("filesystems.disks.{$disk}.container");
    }
}

if (!function_exists('bytesToHuman')) {
    /**
     * @param float $bytes
     * @return string
     */
    function bytesToHuman($bytes)
    {
        $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb'];

        for ($i = 0; $bytes > 1024; ++$i) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('replaceAccents')) {
    /**
     * @param string $str
     * @return bool|string
     */
    function replaceAccents($str)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, 'ASCII', 'UTF-8');
        }
        return iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    }
}

if (!function_exists('sanitizeFilename')) {
    /**
     * @param string $filename
     * @return string
     */
    function sanitizeFilename($filename)
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
