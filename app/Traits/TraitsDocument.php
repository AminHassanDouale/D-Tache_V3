<?php

namespace App\Traits;

use App\Models\Document;
use Livewire\Features\SupportRedirects\HandlesRedirects;
use Mary\Traits\Toast;

trait TraitsDocument
{
    use Toast, ForcesLogin, HandlesRedirects;

    // You should place authorization here
    public function toggleLike(Document $document): void
    {
        $this->forcesLogin("toggleLike,$document->id");

        if (!$document->department_id) {
            $document->department_id = $this->user()->department_id;
        }

        $this->user()->likes()->toggle($document);

        $document->save();

        $this->success('Wishlist updated', position: 'toast-bottom toast-end', css: 'bg-green-500 text-base-100');
    }
}
