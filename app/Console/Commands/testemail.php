<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Illuminate\Console\Command;

class testemail extends Command
{
    protected $signature = 'test:email';
    protected $description = 'Send a test email using dynamic SMTP settings';

    public function handle()
    {
        $emailConfig = DB::table('email_configures')->where('status', 1)->orderBy('id', 'desc')->first();

        if (!$emailConfig) {
            $this->error('❌ No active email configuration found.');
            return;
        }
        config([
            'mail.mailers.smtp.host' => $emailConfig->host,
            'mail.mailers.smtp.port' => $emailConfig->port,
            'mail.mailers.smtp.username' => $emailConfig->email,
            'mail.mailers.smtp.password' => $emailConfig->password,
            'mail.mailers.smtp.encryption' => $emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : null),
            'mail.from.address' => $emailConfig->email,
            'mail.from.name' => $emailConfig->mail_from_name,
        ]);

        // \Log::info(  config('mail.mailers.smtp'));

        Mail::raw('✅ Test email from Laravel dynamic SMTP config.', function ($msg) {
            $msg->to('mahfujur15-14276@diu.edu.bd') // <- CHANGE THIS TO YOUR EMAIL
                ->subject('Test Email from Laravel Command');
        });

        $this->info('✅ Test email sent successfully!');
    }
}
