<?php

namespace App\Modules\DocumentMetadata\Services;

use App\Modules\DocumentMetadata\Models\Document;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class DocumentService
{
    public function store($data, $file, $ownerID)
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = file_get_contents($file->getPathname());
            $mimeType = $file->getMimeType();
            
            // Upload to Google Drive
            $googleDriveId = $this->uploadToGoogleDrive($fileContent, $fileName, $mimeType);
            
            // Generate document number
            $documentNo = 'DOC-' . date('Y') . '-' . str_pad(Document::count() + 1, 4, '0', STR_PAD_LEFT);
            
            $document = Document::create([
                'documentNo' => $documentNo,
                'title' => $data['title'],
                'description' => $data['description'],
                'documentType' => $data['documentType'],
                'ownerID' => $ownerID,
                'currentStatus' => 1,
                'filePath' => $fileName,
                'googleDriveId' => $googleDriveId
            ]);

            return [
                'success' => true,
                'message' => 'Document uploaded successfully! Document ID: ' . $document->documentId,
                'document' => $document
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ];
        }
    }

    private function uploadToGoogleDrive($fileContent, $fileName, $mimeType)
    {
        try {
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->addScope(Google_Service_Drive::DRIVE_FILE);
            $client->setAccessType('offline');
            
            $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
            $accessToken = $client->getAccessToken();
            $client->setAccessToken($accessToken);

            $service = new Google_Service_Drive($client);

            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')]
            ]);

            $file = $service->files->create($fileMetadata, [
                'data' => $fileContent,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart'
            ]);

            return $file->id;
        } catch (\Exception $e) {
            throw new \Exception('Google Drive upload failed: ' . $e->getMessage());
        }
    }
}