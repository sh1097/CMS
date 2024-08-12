<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserSendMail extends Mailable
{
    use Queueable, SerializesModels;
    public  $user;
    public  $subject;
    public  $arrayData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $subject, $arrayData)
    {
        $this->user      = $user;
        $this->subject   = $subject;
        $this->arrayData = $arrayData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data  = $this->arrayData;
        
        $mail['name']              = $data['name'];
        $mail['query']             = $data['message']; 
        $mail['subject']           = $data['subject']; 
        return $this->subject($this->subject)->view('thanku-email',$mail);
    }
}
