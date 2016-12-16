<?php

namespace A17\CmsToolkit\Presenters\Admin;

use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Pagination\BootstrapThreePresenter;
use Illuminate\Pagination\UrlWindow;
use Input;

class PaginatorPresenter extends BootstrapThreePresenter
{
    use PaginatorNextPreviousButtonRendererTrait;

    public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
        $this->paginator->appends(Input::except('page'));
        $this->window = is_null($window) ? UrlWindow::make($paginator) : $window->get();
    }

    public function render()
    {
        if ($this->hasPages()) {
            return sprintf(
                '<ul class="pagination">%s %s %s</ul>',
                $this->getNextButton(),
                $this->getPreviousButton(),
                $this->getLinks()
            );
        }

        return '';
    }

    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {
        $class = '';
        if ($rel == 'prev') {
            $class = "class='previous'";
        } elseif ($rel == 'next') {
            $class = "class='next'";
        }

        $rel = is_null($rel) ? '' : ' rel="' . $rel . '"';

        return '<li><a href="' . $url . '"' . $rel . ' ' . $class . '>' . $page . '</a></li>';
    }

    protected function getDisabledTextWrapper($text)
    {
        return '<li class="disabled"><span>' . $text . '</span></li>';
    }

    protected function getActivePageWrapper($text)
    {
        return '<li class="on"><span>' . $text . '</span></li>';
    }
}
