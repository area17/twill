<?php

namespace A17\Twill\Models\Contracts;

interface TwillLinkableModel
{
    /**
     * This method should return the full url. By default this will be based on the
     * controller slug or urlWithoutSlug. But it can be overwritten in order to
     * fully customize the link.
     *
     * This link is used by for example `TwillUtil::parseInternalLinks`
     */
    public function getFullUrl(): string;
}
