<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Traits\TableTrait;
use App\Http\Resources\UserResource;
use App\Models\Airport;
use App\Models\FundCompany;
use App\Models\FundDistributor;
use App\Models\FundDistributorStaff;
use App\Models\Group;
use App\Models\Investor;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserRequest;
use App\Models\UserToken;
use App\Rules\IntegerArray;
use App\Services\Transactions\UserGroupTransaction;
use App\Services\Transactions\UserRequestTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Transactions\UserTransaction;
use App\Services\Transactions\UserTokenTransaction;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;


/**
 * @group  AppApi
 */
class UserController extends AppApiController
{
    use TableTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login'] ]);
    }

    /**
     * User login
     *
     * Đăng nhập hệ thống
     *
     * @header {String} token={{TOKEN}}
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam params.username string required Username login. Example: admin
     * @bodyParam params.password string required Password login. Example: 123456
     * 
     * @response {
     *      "status": true,
     *      "statusCode": 200,
     *      "message": "Success",
     *      "responseCode": "00",
     *      "response": {
     *          "token": "eyJpdiI6ImpwdDArV2UydlVjNnUvK2JyWjNMYWc9PSIsInZhbHVlIjoicGJJZzFuWG1DTXpoVVVlRGRoTThpT1ZTa0JCRDg2cCtmZUdIOGxrcDcwR3R5eEQ1c2MvNm5xcENwbWpNOXdYOCIsIm1hYyI6IjJkMDYzYTk2ZGJhMWJhMzc1ZmRmYzEwMDllODJkMThiMDZhZWMxOGQ4MGRkYzAwMjY5NGRjODZiM2I4NWMzODQifQ=="
     *      } 
     * }
     * 
     * @response 403 {
     *      "status": true,
     *      "statusCode": 403,
     *      "message": "Tên truy cập hoặc mật khẩu không đúng",
     *      "responseCode": "01",
     *      "response": {
     *          "token": "eyJpdiI6ImpwdDArV2UydlVjNnUvK2JyWjNMYWc9PSIsInZhbHVlIjoicGJJZzFuWG1DTXpoVVVlRGRoTThpT1ZTa0JCRDg2cCtmZUdIOGxrcDcwR3R5eEQ1c2MvNm5xcENwbWpNOXdYOCIsIm1hYyI6IjJkMDYzYTk2ZGJhMWJhMzc1ZmRmYzEwMDllODJkMThiMDZhZWMxOGQ4MGRkYzAwMjY5NGRjODZiM2I4NWMzODQifQ=="
     *      } 
     * }
     */
    public function login(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.username' => 'required|string|max:255',
            'params.password' => 'required|string|max:20'
        ]);
        $params = $inputs['params'];
        $user = User::where('username', $params['username'])->first();        
        if ($user) {            
            if ($user->validatePassword($params['password'])) {                
                if ($user->isActive()) {
                    if ($user->user_group_default->role->code == 'AIRPORT' && $user->user_group_default->group->ref->status == Airport::STATUS_ACTIVE) {
                        // $user->roles;
                        if (!UserToken::isExistsTokenForUser($user->id, $token)) {
                            $result = (new UserTokenTransaction)->makeToken(['user_id' => $user->id], true);
                            $token = UserToken::encryptHashToken($result['response']['token']);
                        }                    
                        return $this->responseSuccess([
                            'token' => $token,                      
                        ]);
                    } else {
                        $this->error('Tài khoản đăng nhập không hợp lệ', '01', Response::HTTP_FORBIDDEN);
                    }
                } else {
                    $this->error('Tài khoản đăng nhập đang bị khóa', '01', Response::HTTP_FORBIDDEN);
                }
            } else {
                $this->error('Tên truy cập hoặc mật khẩu không đúng', '01', Response::HTTP_FORBIDDEN);
            }
        } else {
            $this->error('Tên truy cập hoặc mật khẩu không đúng', '01', Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Profile
     *
     * Lấy thông tin tài khoản đã đăng nhập theo token
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1  
     * @bodyParam token string required User Token.   
     * @response {
     * "status": false,
     * "code"": 403,
     * "messages": "Access denied",
     * "response": null
     * }
     */
    public function profile(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->where('status', User::STATUS_ACTIVE)->first();
        if ($user) {
            $user_group = $user->getUserGroupById(userGroup()->id);
            if ($user_group && $user_group->status == UserGroup::STATUS_ACTIVE) {
                return $this->responseSuccess([
                    'user' => [
                        'id' => $user->id,
                        'user_group_name' => trans($user_group->group->name),                        
                        'user_group_id' => $user_group->id,
                        'ref_id' => $user_group->group->ref_id,
                        'ref_code' => $user_group->group->ref_type != '' ? $user_group->group->ref->code : '',
                        'role_code' => $user_group->role->code,
                        'role_name' => trans($user_group->role->name),
                        'username' => $user->username,
                        'fullname' => $user->fullname,
                        'email' => $user->email,
                        'mobile' => $user->mobile,
                        // 'self_permissions' => $user_group->getSelfPermissions(),
                        'all_permissions' => $user_group->getAllPermissions(),
                        'session_data' => auth()->user()->user_token_data,
                    ],
                    'menus' => $user_group->getMenus(),
                    'permission_groups' => $user_group->getPermissionGroups()
                ]);
            } else {
                $this->error('Người dùng không tồn tại hoặc bị khóa', '01', Response::HTTP_UNAUTHORIZED);
            }            
        } else {
            $this->error('Người dùng không tồn tại hoặc bị khóa', '01', Response::HTTP_UNAUTHORIZED);
        }
    }
}
