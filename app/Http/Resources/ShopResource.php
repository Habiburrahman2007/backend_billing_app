<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'name'         => $this->name,
            'address_line1'=> $this->address_line1,
            'address_line2'=> $this->address_line2,
            'phone_number' => $this->phone_number,
            'upi_id'       => $this->upi_id,
            'footer_text'  => $this->footer_text,
            'logo_url'     => $this->logo_path
                ? Storage::disk('public')->url($this->logo_path)
                : null,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
