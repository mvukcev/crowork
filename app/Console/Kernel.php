        $schedule->command('cleanup:inactive-users')->daily();
        $schedule->command('cleanup:deletion-requests')->daily();