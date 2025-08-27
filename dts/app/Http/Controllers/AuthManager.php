<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class AuthManager extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            // Redirect to document registration page instead of home
            return redirect()->route('docuReg')->with('success', 'Welcome! Please upload your document.');
        }

        return redirect(route('login'))->with('error', 'Invalid credentials');
    }

    public function registrationPost(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'firstName' => 'required',
            'lastName' => 'required',
            'roleID' => 'required',
            'departmentID' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'firstName' => $request->firstName,
            'middleName' => $request->middleName,
            'lastName' => $request->lastName,
            'roleID' => $request->roleID,
            'departmentID' => $request->departmentID,
            'phoneNo' => $request->phoneNo,
        ]);

        if (!$user) {
            return redirect(route('registration'))->with('error', 'Registration failed');
        }

        return redirect(route('login'))->with('success', 'Registration successful! Please login.');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect(route('login'));
    }


    public function documentRegistration()
    {
        return view('documentreg');
    }

    public function documentRegistrationPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'documentType' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Get file content directly from uploaded file
            $fileContent = file_get_contents($file->getPathname());
            $mimeType = $file->getMimeType();
            
            // Upload to Google Drive - FIXED METHOD NAME
            $googleDriveId = $this->uploadToGoogleDriveFromContent($fileContent, $fileName, $mimeType);
            
            // Generate document number
            $documentNo = 'DOC-' . date('Y') . '-' . str_pad(Document::count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Create document record
            $document = Document::create([
                'documentNo' => $documentNo,
                'title' => $request->title,
                'description' => $request->description,
                'documentType' => $request->documentType,
                'ownerID' => Auth::user()->userID,
                'currentStatus' => 1,
                'filePath' => $fileName,
                'googleDriveId' => $googleDriveId
            ]);

            return redirect()->back()->with('success', 'Document uploaded successfully! Document ID: ' . $document->documentId);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    private function uploadToGoogleDriveFromContent($fileContent, $fileName, $mimeType)
    {
        try {
            $client = new \Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->addScope(\Google_Service_Drive::DRIVE_FILE);
            $client->setAccessType('offline');
            
            // Use refresh token to get access token
            $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
            $accessToken = $client->getAccessToken();
            $client->setAccessToken($accessToken);

            $service = new \Google_Service_Drive($client);

            $fileMetadata = new \Google_Service_Drive_DriveFile([
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

    private function getStoredAccessToken()
    {
        // Store your access token in database or session
        // For now, return a placeholder
        return session('google_access_token');
    }

    private function storeAccessToken($token)
    {
        session(['google_access_token' => $token]);
    }
}