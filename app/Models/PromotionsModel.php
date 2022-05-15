<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\WalletModel;

class PromotionsModel extends Model
{
    use HasFactory;

    protected $table = 'promotions';

    function getAllData()
    {
        $data = DB::table($this->table)
            ->select('promotion_codes.id', 'promotion_codes.code', 'promotions.start_date', 'promotions.end_date', 'promotion_codes.amount', 'promotions.quota', 'promotion_codes.who_usaged')
            ->leftJoin('promotion_codes', "promotions.id", '=', 'promotion_codes.promotion_id')
            ->get();
        return $data;
    }

    function getDataById($id)
    {
        $data = DB::table($this->table)
            ->select('promotion_codes.id', 'promotion_codes.code', 'promotions.start_date', 'promotions.end_date', 'promotion_codes.amount', 'promotions.quota', 'promotion_codes.who_usaged')
            ->leftJoin('promotion_codes', "promotions.id", '=', 'promotion_codes.promotion_id')
            ->where('promotion_codes.id', '=', $id)
            ->get();
        return $data;
    }

    function getPromotionCodes($startDate, $endDate, $amount, $quota)
    {
        $data = DB::table($this->table)
            ->select('promotions.id', 'promotion_codes.code', 'promotions.start_date', 'promotions.end_date', 'promotion_codes.amount', 'promotions.quota')
            ->leftJoin('promotion_codes', "promotions.id", '=', 'promotion_codes.promotion_id')
            ->where('who_usaged', '=', 0)
            ->where('start_date', '=', $startDate)
            ->where('end_date', '=', $endDate)
            ->where('amount', '=', $amount)
            ->where('quota', '=', $quota)
            ->get();
        return $data;
    }

    function checkPromotionCode($code)
    {
        $data = DB::table($this->table)
            ->select('promotion_codes.id', 'promotion_codes.amount')
            ->leftJoin('promotion_codes', "promotions.id", '=', 'promotion_codes.promotion_id')
            ->where('start_date', '<', date('Y-m-d H:i:s'))
            ->where('end_date', '>', date('Y-m-d H:i:s'))
            ->where('promotion_codes.code', '=', $code)
            ->where('promotion_codes.who_usaged', '=', 0)
            ->get();
        return $data;
    }

    function setUserPromotionCode($userId, $promotionCodeId, $balance, $amount)
    {
        $wallet = new WalletModel();

        DB::beginTransaction();
        try {
            $wallet->setWalletBalance($userId, $balance, $amount);
            DB::table('promotion_codes')->where('id', $promotionCodeId)->update(['who_usaged' => $userId]);
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return false;
        }


    }
}
