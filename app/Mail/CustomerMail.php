<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CustomerMail extends Mailable
{
	use Queueable, SerializesModels;

	public $data;

	/**
	 * Create a new message instance.
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			subject: $this->data['subject'] ?? 'Customer Mail',
			from: $this->data['from'] ?? 'abdulrahmansaber120@gmail.com',
		);
	}


	public function content(): Content
	{
		return new Content(
			view: 'emails.customer_mail',
			with: [
				'body_content' => $this->data['message'],
				'type' => $this->data['email_type'],
				'subject' => $this->data['subject'],
				'from' => $this->data['from'],
				'file' => $this->data['file'],
			],
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		$attachments = [];

		if (!empty($this->data['files'])) {
			foreach ($this->data['files'] as $file) {
				$attachments[] = Attachment::fromPath($file['path'])
					->as($file['name']);
			}
		}

		return $attachments;
	}
}
