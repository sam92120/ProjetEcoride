<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class AccueilListener
{
    #[AsEventListener(event: 'date_depart')]
    public function onDateDepart($event): void
    {
        // ...
    }
}
