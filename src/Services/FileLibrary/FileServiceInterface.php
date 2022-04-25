<?php

namespace A17\Twill\Services\FileLibrary;

interface FileServiceInterface
{
    /**
     * @return string
     */
    public function getUrl(string $id);
}
