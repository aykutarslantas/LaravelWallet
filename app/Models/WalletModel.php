<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletModel extends Model
{
    use HasFactory;

    public function getWallet($userId)
    {
        $data = DB::table('wallet')->select('id', 'balance', 'updated_at')->where('user_id', '=', $userId)->get();
        return $data;
    }

    public function setWalletBalance($userId, $balance, $amount)
    {
        $data = DB::table('wallet')->where('user_id', '=', $userId)
            ->update(['balance' => ($balance + $amount)]);
    }
}
