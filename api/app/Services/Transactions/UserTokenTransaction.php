<?php

namespace App\Services\Transactions;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserToken;
use Carbon\Carbon;

class UserTokenTransaction extends Transaction
{
    /**
     * Create user token
     * 
     * @param array $params
     * @param integer $params.user_id
     * @param boolean $allow_commit
     * @throws \Exception
     * @return array
     */
    public function create($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            $user = User::where('id', $params['user_id'])->where('status', User::STATUS_ACTIVE)->first();
            if (!$user) {
                $this->error('Tài khoản đăng nhập không tồn tại hoặc đang bị khóa');
            }
            $user_token = new UserToken();
            $token = $user_token->generateToken();
            $user_token->user_id = $user->id;
            $user_token->user_group_id = $user->user_group_id;
            $user_token->hash_token = $token;
            $user_token->expired_at = $user_token->getExpiredAt(time());
            if ($user_token->save()) {
                $response['token'] = $token;
            } else {
                $this->error('Có lỗi khi sinh token');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * Delete all user_token by user_id
     * 
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     */
    public function deleteByUser($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            $rows = UserToken::where('user_id', $params['user_id']);
            if ($rows->count() && !$rows->delete()) {
                $this->error('Có lỗi khi xóa token');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * Make token by user_id
     * 
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     */
    public function makeToken($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {            
            $this->deleteByUser($params);
            $response = $this->create($params);
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * switchAccount token
     * 
     * @param array $params[
     * user_token_id,
     * user_group_id,
     * updated_by,
     * ]
     * @param boolean $allow_commit
     * @throws \Exception
     * @return array
     */
    public function switchGroup($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            $model = UserToken::where('id', $params['user_token_id'])
                ->where('expired_at', '>=', Carbon::now()->toDateTimeString())
                ->first();
            if ($model) {
                $user_group = UserGroup::where('id', $params['user_group_id'])
                    ->where('user_id', $model->user_id)
                    ->where('status', UserGroup::STATUS_ACTIVE)
                    ->first();
                if (!$user_group) {
                    $this->error('Tài khoản đăng nhập không tồn tại hoặc đang bị khóa');
                }
                $model->user_group_id = $user_group->id;
                if (!$model->save()) {
                    $this->error('Có lỗi chuyên đổi tài khoản');
                }
            } else {
                $this->error('Tài khoản đăng nhập không tồn tại hoặc đang bị khóa');
            }            
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * switchAccount token
     * 
     * @param array $params[
     * user_token_id,
     * data,
     * updated_by,
     * ]
     * @param boolean $allow_commit
     * @throws \Exception
     * @return array
     */
    public function updateData($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            $model = UserToken::where('id', $params['user_token_id'])
                ->where('expired_at', '>=', Carbon::now()->toDateTimeString())
                ->first();
            if ($model) {
                $model->data = $params['data'];
                if (!$model->save()) {
                    $this->error('Có lỗi khi cập nhật tài khoản');
                }
            } else {
                $this->error('Tài khoản đăng nhập không tồn tại hoặc đang bị khóa');
            }            
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
