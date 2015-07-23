<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Contracts\Action;

class ActionController extends Controller
{
    /**
     * @var Action\Service
     */
    private $as;

    public function __construct(Action\Service $as) {
        $this->as = $as;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $action = new \App\Action();
        $action->fromJson($request->getContent());

        $user_id = $action->user_id;
        if (!$user_id) {
            $user_id = $request->cookie('tr_guid');
            if (!$user_id) {
                $user_id = $this->as->generateUserId();
            }
            $action->user_id = $user_id;
        }

        $this->as->setRequestData($action, $request);
        $this->as->setDefaults($action);

        try {
            $action->validate();
        } catch (\Exception $e) {
            return $this->error($e);
        }

        $referral_code = $this->as->generateReferralCode($action);
        $action->referral_code = $referral_code;

        try {
            $this->as->storeAction($action);
        } catch (\Exception $e) {
            return $this->error($e);
        }

        $response_data = [
            'user_id' => $user_id,
            'referral_code' => $referral_code,
            'action' => $action->toArray(),
        ];

        $response_json = json_encode($response_data);
        return response($response_json)->header('Content-Type', 'application/json');
    }

    public function error(\Exception $e) {
        $error_data = [
            'message' => $e->getMessage(),
        ];
        return response(json_encode($error_data))->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
