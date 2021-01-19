<?php


namespace App\Http\Controllers\App;

use App\Exceptions\ApiException;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;
use App\Rules\TicketCodeRule;
use App\Services\Tickets\TicketService;
use Carbon\Carbon;
use App\Http\Resources\TicketDetailResource;
/**
 * @group AppApi
 */
class TicketController extends AppApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Ticket check
     *
     * Kiểm tra mã vé
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.ticket_code string required Ticket code. Example: SU0A7C1C3B9L
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé không hợp lệ",
     *     "responseCode": "01",
     *     "response": null
     * }
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé chưa được kích hoạt",
     *     "responseCode": "02",
     *     "response": null
     * }
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé đã hết hạn sử dụng",
     *     "responseCode": "03",
     *     "response": null
     * }
     *
     * @response {
     *     "status": true,
     *     "statusCode": 200,
     *     "message": "",
     *     "responseCode": "00",
     *     "response": null
     * }
     */
    public function check(Request $request){
        $inputs = $this->validate($request, [
            'params.ticket_code' => ['required', new TicketCodeRule]
        ]);
        $ticket = Ticket::where('code', $inputs['params']['ticket_code'])->first();
        if($ticket) {
            if ($ticket->status == Ticket::STATUS_ACTIVE) {
                if (Carbon::now()->lessThan(Carbon::createFromFormat('d/m/Y H:i:s', $ticket->limited_at))) {
                    if ((new TicketService($ticket))->check(Carbon::now())) {
                        return $this->responseSuccess([]);
                    } else {
                        $this->error('Vé không hợp lệ hoặc chưa được sử dụng do thời gian kích hoạt chưa đủ 3 giờ', '01');
                    }
                } else {
                    $this->error('Vé đã hết hạn sử dụng', '03');
                }
            } elseif ($ticket->status == Ticket::STATUS_USED) {
                $this->error('Vé đã được sử dụng', '03');
            } else {
                $this->error('Vé chưa được kích hoạt', '02');
            }
        } else {
            $this->error('Vé không tồn tại', '01');
        }
    }

    /**
     * Ticket use
     *
     * Sử dụng vé
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.ticket_code string required Ticket code. Example: SU0A7C1C3B9L
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé không hợp lệ",
     *     "responseCode": "01",
     *     "response": null
     * }
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé chưa được kích hoạt",
     *     "responseCode": "02",
     *     "response": null
     * }
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé đã hết hạn sử dụng",
     *     "responseCode": "03",
     *     "response": null
     * }
     *
     * @response {
     *     "status": true,
     *     "statusCode": 200,
     *     "message": "",
     *     "responseCode": "00",
     *     "response": null
     * }
     */
    public function use(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.ticket_code' => ['required', new TicketCodeRule]
        ]);
        $ticket = Ticket::where('code', $inputs['params']['ticket_code'])->first();
        if($ticket) {
            if ($ticket->status == Ticket::STATUS_ACTIVE) {
                if (Carbon::now()->lessThan(Carbon::createFromFormat('d/m/Y H:i:s', $ticket->limited_at))) {
                    $actual_airport_id = auth()->user()->user_group_default->ref_id;
                    if ((new TicketService($ticket))->use(Carbon::now(), $actual_airport_id)) {
                        return $this->responseSuccess([]);
                    } else {
                        $this->error('Vé không hợp lệ hoặc chưa được sử dụng do thời gian kích hoạt chưa đủ 3 giờ', '01');
                    }
                } else {
                    $this->error('Vé đã hết hạn sử dụng', '03');
                }
            } elseif ($ticket->status == Ticket::STATUS_USED) {
                $this->error('Vé đã được sử dụng', '03');
            } else {
                $this->error('Vé chưa được kích hoạt', '02');
            }
        } else {
            $this->error('Vé không tồn tại', '01');
        }
    }

    /**
     * Ticket detail
     *
     * Chi tiết vé
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.ticket_code string required Ticket code. Example: SU0A7C1C3B9L
     *
     * @response 400 {
     *     "status": false,
     *     "statusCode": 400,
     *     "message": "Vé không hợp lệ",
     *     "responseCode": "01",
     *     "response": null
     * }
     */

    public function detail (Request $request)
    {
        $inputs = $this->validate($request, [
            'params.ticket_code' => ['required', new TicketCodeRule]
        ]);
        $params = $inputs['params'];
        $ticket = Ticket::where('code', $params['ticket_code'])
            ->where('status', '!=', Ticket::STATUS_LOCK)
            ->first();
        if ($ticket) {
            $data = new TicketDetailResource($ticket);
            return $this->responseSuccess($data);
        } else {
            $this->error('Vé không tồn tại', '01');
        }
    }
}
