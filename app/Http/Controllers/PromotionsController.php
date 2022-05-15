<?php

namespace App\Http\Controllers;

use App\Models\PromotionsModel;
use App\Models\User;
use App\Models\WalletModel;
use Illuminate\Http\Request;

class PromotionsController extends Controller
{
    protected $data_model;

    public function __construct()
    {
        $this->data_model = new PromotionsModel();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->id) && !is_numeric($request->id)) {
            http_response_code(400);
            exit();
        }

        $user = new User();
        $wallet = new WalletModel();

        $response = array();
        try {
            $response["success"] = true;
            $datas = $request->id ? $this->data_model->getDataById($request->id) : $this->data_model->getAllData();
        } catch (\Exception $exception) {
            $response["success"] = false;
            http_response_code(206);
            print_r(json_encode($response));
            exit();
        }

        $response["data"] = array();
        foreach ($datas as $data) {
            $data = (array)$data;
            $userid = $data["who_usaged"];
            unset($data["who_usaged"]);
            $userData = json_decode($user->getUser($userid), true);
            $walletData = json_decode($wallet->getWallet($userid), true);
            foreach ($walletData as $value) {
                $userData[0]["wallet"] = $value;
            }
            $data["users"] = $userData;
            array_push($response["data"], $data);
        }
        http_response_code(201);
        print_r(json_encode($response, JSON_PRETTY_PRINT));
        exit();
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getPromotionCode(Request $request)
    {
        if (!isset($request->start_date) || !isset($request->end_date) || !isset($request->amount) || !isset($request->quota)) {
            $response["success"] = false;
            $response["result"] = "Missing parameters";
            print_r(json_encode($response));
        }

        $data = json_decode($this->data_model->getPromotionCodes($request->start_date, $request->end_date, $request->amount, $request->quota), true);
        print_r(json_encode($data));
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function assignPromotion(Request $request)
    {
        if (!isset($request->user_id) || !isset($request->code)) {
            $response["success"] = false;
            $response["result"] = "Missing parameters";
            print_r(json_encode($response));
            exit();
        }

        $user = new User();
        if ($user->getUser($request->user_id)->count()) {
            $codeID = $this->data_model->checkPromotionCode($request->code);
            if ($codeID->count() != 0) {
                $codeID = json_decode($codeID);
                foreach ($codeID as $code) {
                    $codeID = $code->id;
                    $amount = $code->amount;
                }

                $wallet = new WalletModel();
                $data = json_decode($wallet->getWallet($request->user_id));
                foreach ($data as $item) {
                    $balance = $item->balance;
                }
                if ($this->data_model->setUserPromotionCode($request->user_id, $codeID, $balance, $amount)) {
                    $response = ["success" => true];
                } else {
                    $response = ["success" => false];
                }
                print_r(json_encode($response, JSON_PRETTY_PRINT));
            } else {
                // code doesn't exist TODO: response
            }
        } else {
            // user doesn't exist TODO: response
        }
    }
}
