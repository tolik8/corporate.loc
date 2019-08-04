<?php

namespace Corp;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function articles()
    {
        return $this->hasMany('Corp\Article');
    }

    public function roles()
    {
        return $this->belongsToMany('Corp\Role', 'role_user');
    }

    // 'string'  array('View_Admin', 'ADD_ARTICLES')
    public function canDo($permission, $require = false)
    {
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $permName = $this->canDo($permName);
                if ($permName && !$require) {
                    return true;
                } else if (!$permName && $require) {
                    return false;
                }
            }
            return $require;
        } else {
            foreach ($this->roles as $role) {
                foreach ($role->perms as $perm) {
                    if (Str::is($permission, $perm->name)) {
                        return true;
                    }
                }
            }
        }
    }

    // string  ['role1', 'role2']
    public function hasRole($name, $require = false)
    {
        if (is_array($name)) {
            foreach ($name as $roleName) {
                $hasRole = $this->hasRole($roleName);

                if ($hasRole && !$require) {
                    return true;
                } elseif (!$hasRole && $require) {
                    return false;
                }
            }
            return $require;
        } else {
            foreach ($this->roles as $role) {
                if ($role->name == $name) {
                    return true;
                }
            }
        }

        return false;
    }

}
