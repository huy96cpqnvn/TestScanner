<?php

namespace App\Services\Transactions;

use App\Events\ResetPasswordUserEvent;
use App\Jobs\SendMailCreateUserJob;
use App\Jobs\SendMailResetPasswordJob;
use App\Models\Agent;
use App\Models\Api;
use App\Models\Permission;
use App\Models\RemovePermission;
use App\Models\Role;
use App\Models\RoleApi;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserAccountApi;
use App\Models\UserGroup;
use App\Models\UserAccountRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class UserTransaction extends Transaction
{
    /**
     * @param array $params [
     * user_id,
     * user_group_id,
     * permission_ids,
     * updated_by,
     * ]
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function updateRole($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            // check user exists
            $user = User::find($params['user_id']);
            if ($user) {
                $user_group = UserGroup::where('id', $params['user_group_id'])->where('user_id', $user->id)->first();
                if (!$user_group) {
                    $this->error('Tài khoản người dùng không tồn tại');
                }
                // delete remove_permissions
                $remove_exists = RemovePermission::where('user_group_id', $user_group->id)->first();
                if ($remove_exists) {
                    if (!RemovePermission::where('user_group_id', $user_group->id)->delete()) {
                        $this->error('Có lỗi khi cập nhật quyền tài khoản');
                    }
                }
                // check role_ids exists
                $permissions = $user_group->role->permissions;
                if ($permissions) {
                    // add remove_permissions
                    foreach ($permissions as $permission) {
                        if (!in_array($permission->id, $params['permission_ids'])) {
                            $model = new RemovePermission();
                            $model->user_group_id = $user_group->id;
                            $model->permission_id = $permission->id;
                            $model->created_by = $params['updated_by'];
                            if (!$model->save()) {
                                $this->error('Có lỗi khi cập nhật quyền tài khoản');
                            }
                        }
                    }
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function changePassword($params, $allow_commit = false)
    {

        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            // check user exists
            $user = User::find($params['user_id']);
            if ($user) {
                // check password true
                $password = $user->password;
                if ($user->validatePassword($params['current_password']) == $password) {
                    //Change password
                    $new_password = $params['new_password'];
                    $user->password = $new_password;
                    $user->first_login = User::NOT_FIRST_LOGIN;
                    $user->updated_by = $params['updated_by'];
                    if (!$user->save()) {
                        $this->error('Có lỗi khi đổi mật khẩu');
                    }
                } else {
                    $this->error('Mật khẩu cũ không đúng');
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params [
     * user_id,
     * updated_by
     * ]
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function resetPassword($params, $allow_commit = false)
    {

        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            // check user exists
            $user = User::find($params['user_id']);
            if ($user) {
                //reset password
                $new_password = $user->getRandomPassword();
                $user->password = $new_password;
                $user->first_login = User::FIRST_LOGIN;
                $user->updated_by = $params['updated_by'];
                if (!$user->save()) {
                    $this->error('Có lỗi khi đổi mật khẩu');
                } else {
                    // send mail
                    $job = (new SendMailResetPasswordJob($user, $new_password))->onConnection(config('queue.default'))->onQueue('notifications');
                    dispatch($job);
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function create($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            // Check email exist and username exist
            $email_exist = User::where('email', '=', $params['email'])->first();
            $username_exist = User::where('username', '=', $params['username'])->first();
            // Check user originator
            if ($email_exist === null && $username_exist === null) {
                $new_user = new User();
                $password = $new_user->getRandomPassword();
                $new_user->username = $params['username'];
                $new_user->fullname = $params['fullname'];
                $new_user->password = $password;
                $new_user->email = $params['email'];
                $new_user->mobile = $params['mobile'];
                $new_user->first_login = User::FIRST_LOGIN;
                $new_user->status = $params['status'];
                $new_user->allow_group_id = $params['group_id'];
                $new_user->created_by = $params['created_by'];
                if ($new_user->save()) {
                    $response['user_id'] = $new_user->id;
                    $result = (new UserGroupTransaction)->create([
                        'user_id'=> $new_user->id,
                        'group_id' => $params['group_id'],
                        'created_by' => $params['created_by']
                    ]);
                    $new_user->user_group_id = $result['user_group_id'];
                    if ($new_user->save()) {
                        $role = Role::where('code', Agent::CODE)->find($result['role_id']);
                        if ($role) {
                            $this->removePermissionWhenCreateAgentUser($result['user_group_id']);
                        }

                        $job = (new SendMailCreateUserJob($new_user, $password))->onConnection(config('queue.default'))->onQueue('notifications');
                        dispatch($job);
                    } else {
                        $this->error('Có lỗi khi thêm tài khoản đăng nhập');
                    }
                } else {
                    $this->error('Có lỗi khi thêm tài khoản đăng nhập');
                }
            } else if ($email_exist != null) {
                $this->error('Email đã tồn tại');
            } else if ($username_exist != null) {
                $this->error('Tên đăng nhập này đã tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param integer $params[
     * user_id,
     * fullname,
     * email,
     * mobile,
     * updated_by
     * ]
     * @throws \Exception
     * @return array
     */
    public function update($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
        //Check user id exist
            $user = User::where('id', $params['user_id'])->first();
            if( $user){
                $other_user = User::select('id','email')->whereNotIn('id',[$params['user_id']])->where('email', $params['email'])->first();
                if ($other_user) {
                    $this->error('Email đã tồn tại');
                }
                $user->fullname =  $params['fullname'];
                $user->email = $params['email'];
                $user->mobile = $params['mobile'];
                $user->updated_by = $params['updated_by'];
                if (!$user->save()) {
                    $this->error('Có lỗi khi cập nhật thông tin người dùng');
                }
            }else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param integer $user_id
     * @param string $password
     * @throws \Exception
     * @return array
     */
    public function updatePassword($params, $allow_commit = false){

        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $user = User::find($params['user_id']);
            if ($user) {
                $new_password=$params['new_password'];
                $user->first_login = User::NOT_FIRST_LOGIN;
                $user->password= $new_password;
                $user->save();
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function lock($params, $allow_commit = false){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            //Check user id exist
            $user = User::where('id', '=', $params['user_id'])->first();
            if ($user) {
                if ($user->isActive()) { //Lock user
                    $user->status = User::STATUS_LOCK;
                    $user->updated_by = $params['updated_by'];
                    $user->save();
                } else {
                    $this->error('Người dùng không hợp lệ');
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function active($params, $allow_commit = false){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            //Check user id exist
            $user = User::where('id', '=', $params['user_id'])->first();
            if ($user) {
                if ($user->status == User::STATUS_LOCK) { //Lock user
                    $user->status = User::STATUS_ACTIVE;
                    $user->updated_by = $params['updated_by'];
                    $user->save();
                } else {
                    $this->error('Người dùng không hợp lệ');
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    protected function removePermissionWhenCreateAgentUser($userGroupId)
    {
        $permissionsOfAgent = Permission::where('code', 'like', '/backend/agent/%')->get();

        if ($permissionsOfAgent->isNotEmpty()) {
            $userGroup = UserGroup::find($userGroupId);
            if ($userGroup) {
                $permissions = $userGroup->role->permissions;
                if ($permissions) {
                    // add remove_permissions
                    foreach ($permissions as $permission) {
                        if (!$permissionsOfAgent->find($permission->id)) {
                            $remove = RemovePermission::create([
                                'user_group_id' => $userGroupId,
                                'permission_id' => $permission->id,
                                'created_by' => auth()->user()->id
                            ]);
                            if (!$remove) {
                                $this->error('Có lỗi khi cập nhật quyền tài khoản');
                            }
                        }
                    }
                }
            }
        }
    }
}
