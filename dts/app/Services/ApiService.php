<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Http;

// class ApiService
// {
//     private $baseUrl;
//     private $token;

//     public function __construct()
//     {
//         $this->baseUrl = config('app.url') . '/api';
//     }

//     public function setToken($token)
//     {
//         $this->token = $token;
//         return $this;
//     }

//     public function login($email, $password)
//     {
//         $response = Http::post($this->baseUrl . '/auth/login', [
//             'email' => $email,
//             'password' => $password
//         ]);

//         return $response->json();
//     }

//     public function register($userData)
//     {
//         $response = Http::post($this->baseUrl . '/auth/register', $userData);

//         return $response->json();
//     }

//     public function logout()
//     {
//         $response = Http::withToken($this->token)
//                        ->post($this->baseUrl . '/auth/logout');

//         return $response->json();
//     }

//     public function getUser()
//     {
//         $response = Http::withToken($this->token)
//                        ->get($this->baseUrl . '/auth/user');

//         return $response->json();
//     }

//     public function uploadDocument($documentData)
//     {
//         $response = Http::withToken($this->token)
//                        ->attach(
//                            'file', 
//                            $documentData['file_content'], 
//                            $documentData['file_name']
//                        )
//                        ->post($this->baseUrl . '/documents', [
//                            'title' => $documentData['title'],
//                            'description' => $documentData['description'],
//                            'documentType' => $documentData['documentType']
//                        ]);

//         return $response->json();
//     }

//     public function getDocuments()
//     {
//         $response = Http::withToken($this->token)
//                        ->get($this->baseUrl . '/documents');

//         return $response->json();
//     }

//     public function getDocument($id)
//     {
//         $response = Http::withToken($this->token)
//                        ->get($this->baseUrl . '/documents/' . $id);

//         return $response->json();
//     }
// }