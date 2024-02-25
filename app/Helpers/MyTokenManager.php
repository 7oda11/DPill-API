<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use illuminate\support\Str;

class MyTokenManager
{
    public static function CreateToken($id)
    {
        $tokenstr = str::random(40);
        $encryptToken = Hash::make($tokenstr);
        DB::insert('insert into users_tokens (user_id,tokens)values(?,?)', [$id, $encryptToken]);
        $tokenId = DB::getPdo()->lastInsertId();
        return "$tokenId|$tokenstr";
    }
    public static function currentUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }
        if (!str_contains($token, '|')) {
            return null;
        }
        [$tokenId, $tokenStr] = explode('|', $token, 2);
        $result = DB::select('select * from users_tokens where id=?', [$tokenId]);
        if ($result) {
            $tokenData = $result[0];
            if (Hash::check($tokenStr, $tokenData->tokens)) {
                $resut2 = DB::select('select * from users where id=?', [$tokenData->user_id]);
                if ($resut2) {
                    $userData = $resut2[0];
                    return $userData;
                }
            }
        }
    }
    public static function removeUserTokens(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }
        if (!str_contains($token, '|')) {
            return null;
        }
        [$tokenId, $tokenStr] = explode('|', $token, 2);
        DB::delete('delete from users_tokens where id =?', [$tokenId]);
    }
}
