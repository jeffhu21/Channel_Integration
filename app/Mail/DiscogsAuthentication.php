<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DiscogsAuthentication extends Mailable
{
    use Queueable, SerializesModels;

    public $id;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        //
        //$this->id = $id;
        $this->url = env('APP_SERVER').'/api/DiscogsOauth/'.$id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('jeffreyhu97@gmail.com')->markdown('mail.discogs-authentication');

        //return $this->from('jeffreyhu97@gmail.com','Discogs Channel')->view('emails.Discogs.authenticate');
    }
}
