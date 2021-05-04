<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = auth()->user()->email;
        $name = auth()->user()->name. ' '. auth()->user()->lastname;
        return $this->from('bsms.support@ideaserv.com.ph', 'BSMS Support Team')
            ->subject('Report A Problem')
            ->view('responder');
    }
}
