<?php

namespace Modules\EmailCampaigns\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Modules\EmailCampaigns\Http\Models\Email;
use \Platform\Controllers\Core;

class SendTestEmail implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    protected $user;
    protected $mailto;
    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $mailto, Email $email)
    {
        $this->user = $user;
        $this->mailto = $mailto;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $data = [];

      $variant = 1;
      $view = 'public.emails::' . Core\Secure::staticHash($this->email->user_id) . '.' . Core\Secure::staticHash($this->email->email_campaign_id, true) . '.' . $this->email->local_domain . '.' . $variant . '.index';

      $html = \Modules\EmailCampaigns\Http\Controllers\FunctionsController::parseEmail($this->mailto, $view);
      $subject = \Modules\EmailCampaigns\Http\Controllers\FunctionsController::parseString($this->mailto, $this->email->subject);

      $mail_from = ($this->email->emailCampaign->mail_from == '') ? $this->user->email : $this->email->emailCampaign->mail_from;
      $mail_from_name = ($this->email->emailCampaign->mail_from_name == '') ? $this->user->name : $this->email->emailCampaign->mail_from_name;

      $mailto = $this->mailto;

      $response = \Mailgun::raw($html, function ($message) use ($subject, $mail_from, $mail_from_name, $mailto) {
        $message
          ->subject($subject)
          ->from($mail_from, $mail_from_name)
          ->replyTo($mail_from, $mail_from_name)
          ->to($mailto)
          ->trackClicks(false)
          ->trackOpens(false);
      });

    }
}
