<?php

namespace App\Filament\Resources\DocumentResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;

class DocumentViewer extends Widget
{
    protected static string $view = 'filament.resources.document-resource.widgets.document-viewer';

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function getFileUrl()
    {
        if (!$this->record->doc_permit) return null;

        $path = 'public/' . $this->record->doc_permit;
        return Storage::exists($path) ? Storage::url($this->record->doc_permit) : null;
    }
}
