<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class AlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $content;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filePath = "mails/alert/" . $this->user->lang . '.json';
        if (Storage::disk('s3')->exists($filePath)) {
            $result = json_decode(Storage::disk('s3')->get($filePath), true);
            Log::info('Get mail template from s3.');
        } else {
            $result = [
                'subject' => 'Customer Feedback Alert - Action Required',
                'content' => "
                    ******** Please do not reply to this e-mail. It was automatically generated because a customer wanted to have a call back. ********<br><br>

                    <b>Survey date:</b>{date}<br>
                    <b>Survey time: </b>{time}<br>
                    <b>Location: </b>{storeId} - {storeName}<br><br>
            
                    NPS score for Customer's Feedback is {nps}.<br>
                    Please find all the enquiry details attached to this e-mail.<br><br>
            
                    Please contact the customer within 3 business days and find all the enquiry details in the <a href='{url}'>reporting system.</a>"
            ];
            Log::info('Get mail template from local.');
        }

        $this->content = $result['content'];
        return $this->view('mail.AlertTemplate')->subject($result['subject']);
    }
}