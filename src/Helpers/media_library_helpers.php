<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('s3Endpoint')) {
    /**
     * @param string $disk
     * @return string
     */
    function s3Endpoint($disk = 'libraries')
    {
        // if a custom s3 endpoint is configured explicitly, return it
        $customEndpoint = config("filesystems.disks.{$disk}.endpoint");

        if ($customEndpoint) {
            return $customEndpoint;
        }

        $scheme = config("filesystems.disks.{$disk}.use_https") ? 'https://' : '';
        return $scheme . config("filesystems.disks.{$disk}.bucket") . '.' . Storage::disk($disk)->getAdapter()->getClient()->getEndpoint()->getHost();
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

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('cyrillicToLatin')) {
    /**
     * @param string $str
     * @return bool|string
     */
    function cyrillicToLatin($str)
    {
      $tr = [
        // Russian
        "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d",
        "Е" => "e", "Ё" => "yo", "Ж" => "zh", "З" => "z", "И" => "i",
        "Й" => "j", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n",
        "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t",
        "У" => "u", "Ф" => "f", "Х" => "kh", "Ц" => "ts", "Ч" => "ch",
        "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "y", "Ь" => "",
        "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b",
        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo",
        "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k",
        "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p",
        "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f",
        "х" => "kh", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch",
        "ъ" => "", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu",
        "я" => "ya",
        // Ukrainian
        'Ґ' => 'G', 'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'ґ' => 'g',
        'є' => 'ie', 'і' => 'i', 'ї' => 'i', 'і́' => 'i',
        // Kazakh
        'Ә' => 'A', 'Ғ' => 'G', 'Қ' => 'Q', 'Ң' => 'N', 'Ө' => 'O',
        'Ұ' => 'U', 'Ү' => 'U', 'Һ' => 'H', 'І' => 'I', 'ә' => 'a',
        'ғ' => 'g', 'қ' => 'q', 'ң' => 'n', 'ө' => 'o', 'ұ' => 'u',
        'ү' => 'u', 'һ' => 'h', 'і' => 'i',
      ];

      return strtr($str, $tr);
    }
}

if (!function_exists('replaceAccents')) {
    /**
     * @param string $str
     * @return bool|string
     */
    function replaceAccents($str)
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT', cyrillicToLatin($str));
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
