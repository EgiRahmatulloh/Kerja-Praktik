<?php

namespace App\Events;

use App\Models\FilledLetter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLetterSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $letter;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FilledLetter $letter)
    {
        $this->letter = $letter;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'letter.submitted';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->letter->id,
            'title' => 'Surat Baru Masuk',
            'message' => "Mahasiswa {$this->letter->user->name} mengajukan surat {$this->letter->letterType->nama_jenis}",
            'time' => $this->letter->created_at->diffForHumans(),
            'url' => route('admin.filled-letters.show', $this->letter->id)
        ];
    }
}
