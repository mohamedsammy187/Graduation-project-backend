<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

// ------------------------------
// 1. getTitle()
/*
if (!function_exists('getTitle')) {
    function getTitle($default = 'Default')
    {
        // Use Laravel view shared variable
        return view()->shared('pageTitle') ?? $default;
    }
}
*/
// ------------------------------
if (!function_exists('getTitle')) {
    function getTitle($default = 'Default')
    {
        // Use Laravel view shared variable
        return view('pageTitle') ?? $default;
    }
}


// ------------------------------
// 2. redirectHome()
// ------------------------------
if (!function_exists('redirectHome')) {
    function redirectHome($msg, $url = null, $seconds = 3)
    {
        // Determine redirect URL
        if ($url === null) {
            $url = url('/');
            $link = 'Home Page';
        } else {
            $url = url()->previous() ?? url('/');
            $link = 'Previous Page';
        }

        // Flash message to session
        session()->flash('redirect_message', $msg);
        session()->flash('redirect_info', "You will be redirected to $link after $seconds seconds");

        // Return redirect response with delay using meta refresh (Blade handles delay)
        return response()
            ->view('redirect', ['url' => $url, 'seconds' => $seconds])
            ->header('Refresh', "$seconds;url=$url");
    }
}


// ------------------------------
// 3. checkItem()
// ------------------------------
if (!function_exists('checkItem')) {
    function checkItem($select, $from, $value)
    {
        return DB::table($from)
            ->where($select, $value)
            ->count();
    }
}


// ------------------------------
// 4. countItems()
// ------------------------------
if (!function_exists('countItems')) {
    function countItems($item, $table)
    {
        return DB::table($table)->count($item);
    }
}


// ------------------------------
// 5. getLatest()
// ------------------------------
if (!function_exists('getLatest')) {
    function getLatest($select, $table, $order, $limit = 5)
    {
        return DB::table($table)
            ->select(DB::raw($select))
            ->orderBy($order, 'DESC')
            ->limit($limit)
            ->get();
    }
}
