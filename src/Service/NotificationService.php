<?php

// src/Service/NotificationService.php

namespace App\Service;

use App\Entity\User;
use App\Entity\Covoiturage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyUser(User $user, Covoiturage $covoiturage): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('New Covoiturage Available')
            ->html($this->renderEmailBody($covoiturage));

        $this->mailer->send($email);
    }

    private function renderEmailBody(Covoiturage $covoiturage): string
    {
        return sprintf(
            'A new covoiturage is available: %s to %s on %s at %s',
            $covoiturage->getLieudepart(),
            $covoiturage->getLieuarrivee(),
            $covoiturage->getDatedepart()->format('Y-m-d'),
            $covoiturage->getHeuredepart()->format('H:i')
        );
    }
    public function notifyAdmin(Covoiturage $covoiturage): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to('admin@example.com')
            ->subject('New Covoiturage Created')
            ->html($this->renderEmailBody($covoiturage));

        $this->mailer->send($email);
    }




    public function notifyCancellation(User $user, Covoiturage $covoiturage): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Covoiturage Cancelled')
            ->html($this->renderEmailBody($covoiturage));

        $this->mailer->send($email);

    }

    public function senCancellationConfirmation(User $user, Covoiturage $covoiturage): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Covoiturage Cancellation Confirmation')
            ->html($this->renderEmailBody($covoiturage));

        $this->mailer->send($email);

    }

}