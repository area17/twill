<?php

namespace A17\CmsToolkit\Helpers;

use Laracasts\Flash\FlashNotifier as BaseFlashNotifier;
use Laracasts\Flash\SessionStore;

class FlashNotifier extends BaseFlashNotifier
{
    protected $session;

    public function __construct(SessionStore $session)
    {
        $this->session = $session;
    }

    public function message($message, $level = 'info', $allowClose = true)
    {
        $this->session->flash('flash_notification.message', $message);
        $this->session->flash('flash_notification.level', $level);
        $this->session->flash('flash_notification.close', $allowClose);

        return $this;
    }
}
