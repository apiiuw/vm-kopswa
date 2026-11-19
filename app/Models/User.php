<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'category', 'vendor_id', 'client_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all contract (vendor) IDs accessible by this client user.
     * Supports multi-contract per client while preserving single vendor_id fallback.
     *
     * @return array<int>
     */
    public function accessibleContractIds(): array
    {
        if ($this->client_id) {
            $ids = Vendor::where('client_id', $this->client_id)->pluck('id')->all();
            if ($this->vendor_id && ! in_array($this->vendor_id, $ids, true)) {
                $ids[] = $this->vendor_id;
            }

            return $ids;
        }

        return $this->vendor_id ? [$this->vendor_id] : [];
    }
}
