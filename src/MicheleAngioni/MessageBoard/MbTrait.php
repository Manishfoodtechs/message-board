<?php namespace MicheleAngioni\MessageBoard;

use Helpers;
use MicheleAngioni\MessageBoard\Models\Role;

trait MbTrait {


    public function mbBans()
    {
        return $this->hasMany('\MicheleAngioni\MessageBoard\Models\Ban', 'user_id')->orderBy('updated_at', 'desc');
    }

    public function mbPosts()
    {
        return $this->hasMany('\MicheleAngioni\MessageBoard\Models\Post');
    }

    public function mbLastView()
    {
        return $this->hasOne('\MicheleAngioni\MessageBoard\Models\View');
    }

    public function mbRoles()
    {
        return $this->belongsToMany('\MicheleAngioni\MessageBoard\Models\Role', 'tb_messboard_user_role', 'user_id')->withTimestamps();
    }

    /**
     * Return the User last view datetime.
     *
     * @return string
     */
    public function getLastViewDatetime()
    {
        return $this->mbLastView->datetime;
    }

    /**
     * Check if the user is actually banned.
     *
     * @return bool
     */
    public function isBanned()
    {
        if(count($this->mbBans) > 0) {
            if($this->mbBans->first()->until >= Helpers::getDate()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the user has a Role by its name.
     *
     * @param string $name
     * @return bool
     */
    public function hasMbRole($name)
    {
        foreach ($this->mbRoles as $role) {
            if ($role->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a Permission by its name.
     *
     * @param string $permission
     * @return bool
     */
    public function canMb($permission)
    {
        foreach ($this->mbRoles as $role) {
            foreach ($role->permissions as $perm) {
                if ($perm->name == $permission) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Attach input Role to the user.
     *
     * @param Role|array $role
     * @return void
     */
    public function attachMbRole($role)
    {
        if( is_object($role)) {
            $role = $role->getKey();
        }

        if( is_array($role)) {
            $role = $role['id'];
        }

        $this->mbRoles()->attach( $role );
    }

    /**
     * Detach input Role from the user.
     *
     * @param Role $role
     * @return void
     */
    public function detachMbRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->mbRoles()->detach($role);
    }

}
