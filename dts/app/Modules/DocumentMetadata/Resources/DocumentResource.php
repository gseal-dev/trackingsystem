<?php

namespace App\Modules\DocumentMetadata\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'documentId' => $this->documentId,
            'documentNo' => $this->documentNo,
            'title' => $this->title,
            'description' => $this->description,
            'documentType' => $this->documentType,
            'ownerID' => $this->ownerID,
            'currentStatus' => $this->currentStatus,
            'filePath' => $this->filePath,
            'googleDriveId' => $this->googleDriveId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'owner' => $this->whenLoaded('owner'),
        ];
    }
}