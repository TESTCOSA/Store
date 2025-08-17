<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens; // Import HasApiTokens
use Exception;
use Filament\Actions\Exports\Models\Export;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use http\Env\Response;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasRoles, Notifiable; // Add HasApiTokens trait

    protected $table = 'ws_users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username', 'email', 'access_level', 'enabled', 'password', 'user_hash'
    ];
    public $timestamps = false;

    /**
     * Return the username (or any identifier) for Filament authentication
     *
     * @return string
     */
    public function getFilamentName(): string
    {
        return $this->userDetails->full_name_en;
    }

    public function getRememberToken(): ?string
    {
        return $this->user_hash; // Use `user_hash` instead of `remember_token`
    }

    public function setRememberToken($value): void
    {
        $this->user_hash = $value; // Set `user_hash` as the remember token
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'app') {
            if (Auth()->user()?->hasAnyRole('super_admin', 'Inspector', 'admin', 'panel_user', 'Supervisor')) {
                return true;
            } else {
                return abort(403, 'Error: Not authorized');
            }
        }
        dd('Panel ID mismatch', $panel->getId());
        return false;
    }

    public function userDetails()
    {
        return $this->hasOne(UserDetails::class, 'user_id', 'user_id');
    }


    public function leaveOrders()
    {
        return $this->hasMany(LeaveOrders::class, 'emp_id');
    }
}
