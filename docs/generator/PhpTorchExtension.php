<?php

namespace A17\Docs;

use A17\PhpTorch\Highlight;
use A17\Twill\Commands\GenerateDocsCommand;
use Illuminate\Support\Str;
use Torchlight\Block;
use Torchlight\Commonmark\V2\TorchlightExtension;

/**
 * @todo: Move to phptorch
 */
class PhpTorchExtension extends TorchlightExtension
{
    protected function makeTorchlightBlock($node)
    {
        return Block::make()
            ->language($this->getRealLanguage($node))
            ->theme($this->getTheme($node))
            ->code($this->getContent($node));
    }

    protected function getLanguage($node)
    {
        return parent::getLanguage($node);
    }

    protected function getRealLanguage($node)
    {
        if (Str::startsWith($this->getLanguage($node), 'phptorch')) {
            $code = $this->getLiteralContent($node);
            $containsCode = Str::contains($code, '##CODE##');

            if ($containsCode) {
                $data = json_decode(Str::before($code, '##CODE##'), flags: JSON_THROW_ON_ERROR);
            } else {
                $data = json_decode($code, flags: JSON_THROW_ON_ERROR);
            }

            if ($data->language ?? false) {
                $language = $data->language;
            } elseif ($data->file ?? false) {
                if (Str::endsWith($data->file, '.blade.php')) {
                    $language = 'blade';
                } else {
                    $language = Str::afterLast($data->file, '.');
                }
            } else {
                $language = 'php';
            }

            return $language;
        }

        return $this->getLanguage($node);
    }

    protected function getContent($node)
    {
        $code = $this->getLiteralContent($node);

        if (Str::startsWith($this->getLanguage($node), 'phptorch')) {
            $containsCode = Str::contains($code, '##CODE##');

            if ($containsCode) {
                $data = json_decode(Str::before($code, '##CODE##'), flags: JSON_THROW_ON_ERROR);

                $code = Str::after($code, '##CODE##');

                $highlighter = Highlight::fromCode(trim($code));
            } else {
                $data = json_decode($code, flags: JSON_THROW_ON_ERROR);

                if (Str::startsWith($data->file, '/')) {
                    $path = $data->file;
                } else {
                    if (Str::startsWith($data->file, '../') && $currentFile = GenerateDocsCommand::$currentFile) {
                        $path = Str::beforeLast($currentFile, '/');
                        $path = $path . '/' . $data->file;
                    } elseif (Str::startsWith($data->file, './') && $currentFile = GenerateDocsCommand::$currentFile) {
                        $path = Str::beforeLast($currentFile, '/');
                        $path = $path . '/' . $data->file;
                    } else {
                        $path = $_SERVER['PWD'] . '/' . $data->file;
                    }
                }

                if ($data->simple ?? false) {
                    return file_get_contents($path);
                }

                $highlighter = Highlight::new($path);
            }

            foreach ($data as $key => $value) {
                if ($key === 'file' || $key === 'language') {
                    continue;
                }

                if (method_exists($highlighter, $key)) {
                    if (is_object($value)) {
                        $highlighter->{$key}(...(array)$value);
                    } elseif (is_array($value) && is_object($value[0])) {
                        $highlighter->{$key}(...(array)$value[0]);
                    } else {
                        $highlighter->{$key}($value);
                    }
                }
            }

            return (string)$highlighter;
        }

        return parent::getContent($node);
    }
}
