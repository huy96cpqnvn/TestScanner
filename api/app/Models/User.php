<?php

namespace App\Models;

class User extends GroupModel
{
    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 2;

    const FIRST_LOGIN = 1;
    const NOT_FIRST_LOGIN = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'allow_group_id', 'user_group_id', 'username', 'password', 'fullname', 'email', 'mobie', 'first_login', 'status', 'create_by', 'update_by'
    ];

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $this->_encrypt($value);
    }

    /**
     * Get the user tokens for the user.
     */
    public function user_tokens()
    {
        return $this->hasMany('App\Models\UserToken', 'user_id');
    }

    /**
     * Get the user requests for the user.
     */
    public function user_requests()
    {
        return $this->hasMany('App\Models\UserRequest', 'user_id');
    }

    /**
     * Get the user tokens for the user.
     */
    public function user_groups()
    {
        return $this->hasMany('App\Models\UserGroup', 'user_id');
    }

    /**
     * Get the user tokens for the user.
     */
    public function user_group_default()
    {
        return $this->belongsTo('App\Models\UserGroup', 'user_group_id');
    }

    /**
     * Get the user tokens for the user.
     */
    public function getUserGroupById($user_group_id)
    {
        $user_group = UserGroup::where('id', $user_group_id)
            ->where('user_id', $this->id)
            ->where('status', UserGroup::STATUS_ACTIVE)
            ->first();
        return $user_group;
    }

    public static function getListStatus() {
        return array(
            User::STATUS_ACTIVE => trans('status.user.active'),
            User::STATUS_LOCK => trans('status.user.lock'),
        );
    }

    /**
     * Return true if user active
     * @return boolean
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isFirstLogin()
    {
        return $this->first_login == self::FIRST_LOGIN;
    }

    /**
     * Check password current user
     * @return boolean
     */
    public function validatePassword($password)
    {
        return $this->_encrypt($password) === $this->password;
    }

    /**
     * Function encrypt password
     */
    private function _encrypt($value)
    {
        return md5($value);
    }

    public function getRandomPassword($length = 6) {
        return substr(md5(rand(1,1000000)),0, $length);
    }

    public function setCurrentUserGroup($user_group_id)
    {
        $this->current_user_group_id = $user_group_id;
        $this->current_user_group = UserGroup::find($user_group_id);
    }

    public function setUserToken($user_token)
    {
        $this->user_token_id = $user_token->id;
        $this->user_token_data = $user_token->data;
    }

    // use in policy
    public function currentUserGroup() : UserGroup
    {
        return $this->current_user_group;
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'agent_users');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_groups');
    }

    public function pivot_agent_user()
    {
        return $this->hasOne(AgentUser::class);
    }
}
