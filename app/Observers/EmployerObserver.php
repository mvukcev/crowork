<?php

namespace App\Observers;

use App\Models\Employer;
use App\Notifications\EmployerAccountApproved;
use App\Notifications\EmployerAccountRejected;

class EmployerObserver
{
    public function updated(Employer $employer): void
    {
        if (! $employer->wasChanged('approved_at')) {
            return;
        }

        $employer->loadMissing('user');

        if ($employer->getOriginal('approved_at') === null && $employer->approved_at !== null) {
            $employer->user?->notify(new EmployerAccountApproved($employer));
            return;
        }

        if ($employer->getOriginal('approved_at') !== null && $employer->approved_at === null) {
            $employer->user?->notify(new EmployerAccountRejected($employer));
        }
    }
}
