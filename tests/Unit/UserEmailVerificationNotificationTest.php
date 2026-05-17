<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Tests\TestCase;

class UserEmailVerificationNotificationTest extends TestCase
{
    public function test_local_testing_mail_failure_does_not_throw(): void
    {
        Log::spy();

        $user = new class extends User
        {
            public function notify($instance)
            {
                throw new RuntimeException('SMTP connection failed');
            }
        };

        $user->id = 123;
        $user->role = User::ROLE_WORKER;

        $user->sendEmailVerificationNotification();

        Log::shouldHaveReceived('warning')->once();
        $this->assertTrue(true);
    }
}
