<?php

namespace A17\CmsToolkit\Presenters\Admin;

trait PaginatorNextPreviousButtonRendererTrait
{
    public function getPreviousButton($text = '&laquo;')
    {
        if ($this->paginator->currentPage() <= 1) {
            return '<li><span class="disabled previous">' . $text . '</span></li>';
        }

        $url = $this->paginator->url(
            $this->paginator->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text, 'prev');
    }

    public function getNextButton($text = '&raquo;')
    {
        if (!$this->paginator->hasMorePages()) {
            return '<li><span class="disabled next">' . $text . '</span></li>';
        }

        $url = $this->paginator->url($this->paginator->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text, 'next');
    }
}
