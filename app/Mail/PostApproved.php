<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class PostApproved extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $post;
    public $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $post)
    {

        $this->user = $user;
        $this->post = $post;
        $this->url = env('APP_URL_UI') . '/viewpost/' . $this->post->slug;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('Post Approved')
            ->markdown('mail.post-approved');
    }
}
