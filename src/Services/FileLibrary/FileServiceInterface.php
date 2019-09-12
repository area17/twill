<?php

namespace A17\Twill\Services\FileLibrary;

interface FileServiceInterface
{
    /**
     * @param string $id
     * @return string
     */
    public function getUrl($id);
}
