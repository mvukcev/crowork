<?php

namespace App\Observers;

use App\Models\Employer;
use App\Notifications\EmployerAccountApproved;

class EmployerObserver
{
    public function updated(Employer $employer): void
    {
        if ($employer->wasChanged('approved_at') && $employer->getOriginal('approved_at') === null && $employer->approved_at !== null) {
            $employer->loadMissing('user');
            $employer->user?->notify(new EmployerAccountApproved($employer));
        }
    }
}
