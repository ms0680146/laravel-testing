<?php

namespace Tests\Unit\Mails;

use Tests\TestCase;
use App\Mail\AlertMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_alert_mail_from_s3_fr_json()
    {
        // Arrange
        $user = factory(\App\User::class)->create(['lang' => 'fr']);
        $s3FilePath = 'mails/alert/fr.json';
        $s3File = [
            'subject' => '(S3 fr)Customer Feedback Alert - Action Required',
            'content' => "********(S3 fr)Please do not reply to this e-mail. It was automatically generated because a customer wanted to have a call back. ********<br><br><b>Survey date:</b>{date}<br><b>Survey time: </b>{time}<br><b>Location: </b>{storeId} - {storeName}<br><br>NPS score for Customer's Feedback is {nps}.<br>Please find all the enquiry details attached to this e-mail.<br><br>Please contact the customer within 3 business days and find all the enquiry details in the <a href='{url}'>reporting system.</a>" 
        ];

        // Act
        $alertMail = new AlertMail($user);
        Storage::fake('s3');
        Storage::disk('s3')->put($s3FilePath, json_encode($s3File));

        // Assert
        Storage::disk('s3')->assertExists($s3FilePath);
        $this->assertEquals('mail.AlertTemplate', $alertMail->build()->view);
        $this->assertEquals('(S3 fr)Customer Feedback Alert - Action Required', $alertMail->build()->subject);
    }

    public function test_alert_survey_mail_from_default_en_json()
    {
        // Arrange
        $user = factory(\App\User::class)->create(['lang' => 'test']);
        $s3FilePath = 'reporting/mails/alert/fr.json';

        // Act
        $alertMail = new AlertMail($user);
        Storage::fake('s3');

        // Assert
        Storage::disk('s3')->assertMissing($s3FilePath);
        $this->assertEquals('mail.AlertTemplate', $alertMail->build()->view);
        $this->assertEquals('Customer Feedback Alert - Action Required', $alertMail->build()->subject);
    }
}
