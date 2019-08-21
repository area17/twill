<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('s3Endpoint')) {
    /**
     * @param string $disk
     * @return string
     */
    function s3Endpoint($disk = 'libraries')
    {
        $scheme = config("filesystems.disks.{$disk}.use_https") ? 'https://' : '';

        return $scheme . config("filesystems.disks.{$disk}.bucket") . '.' . Storage::disk($disk)->getAdapter()->getClient()->getEndpoint()->getHost();
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

        for ($i = 0; $bytes > 1024; $i++) {
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

        $sanitizedFilename = preg_replace('/[^A-Za-z0-9-\. ]/', '', $sanitizedFilename); // Remove all non-alphanumeric except .
        $sanitizedFilename = preg_replace('/\.(?=.*\.)/', '', $sanitizedFilename); // Remove all but last .
        $sanitizedFilename = preg_replace('/-+/', '-', $sanitizedFilename); // Replace any more than one - in a row
        $sanitizedFilename = str_replace('-.', '.', $sanitizedFilename); // Remove last - if at the end
        $sanitizedFilename = strtolower($sanitizedFilename); // Lowercase

        return $sanitizedFilename;
    }
}
