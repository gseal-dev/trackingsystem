<?php

namespace App\Modules\Authentication\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'userID' => $this->userID,
            'username' => $this->username,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
            'roleID' => $this->roleID,
            'departmentID' => $this->departmentID,
            'phoneNo' => $this->phoneNo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}