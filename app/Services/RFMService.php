<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RFMService {
   public static function rfm($subQuery, $rfmPrms) {
    // RFM分析
        // 1.購買IDごとにまとめ
        $subQuery = $subQuery->groupBy('id')
        ->selectRaw('id, customer_id, customer_name,
                    Sum(subtotal) as totalPerPurchase, created_at');

        // 2.会員ごとにRFMを取得
        $subQuery = DB::table($subQuery)
        ->groupBy('customer_id')
        ->selectRaw('customer_id, customer_name, MAX(created_at) as recentDate,
                    DATEDIFF(now(), max(created_at)) as recency,
                    COUNT(customer_id) as frequency,
                    SUM(totalPerPurchase) as monetary');

        // dd($subQuery);

        // 3.RFMランクを定義(コード外)
        // 4.会員ごとにRFMランクを計算

        // 仮のパラメータ
        // $rfmPrms = [
        //     14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000
        // ];

        $subQuery = DB::table($subQuery)
        ->selectRaw('customer_id, customer_name,
        recentDate, recency, frequency, monetary,
        case
            when recency < ? then 5
            when recency < ? then 4
            when recency < ? then 3
            when recency < ? then 2
            else 1 end as r,
        case
            when ? <= frequency then 5
            when ? <= frequency then 4
            when ? <= frequency then 3
            when ? <= frequency then 2
            else 1 end as f,
        case
            when ? <= monetary then 5
            when ? <= monetary then 4
            when ? <= monetary then 3
            when ? <= monetary then 2
            else 1 end as m', $rfmPrms);

        // dd($subQuery);

        Log::debug($subQuery->get());

        // 5.ランクごとの数を計算
        $totals = DB::table($subQuery)->count();

        $rCount = DB::query()
                ->fromSub($subQuery, 'scored')
                ->rightJoin('ranks', 'ranks.rank', '=', 'scored.r')
                ->groupBy('ranks.rank')
                ->selectRaw('ranks.rank as r, COUNT(scored.r) as cnt')
                ->orderBy('r', 'desc')
                ->pluck('cnt');

        $fCount = DB::query()
                ->fromSub($subQuery, 'scored')
                ->rightJoin('ranks', 'ranks.rank', '=', 'scored.f')
                ->groupBy('ranks.rank')
                ->selectRaw('ranks.rank as f, COUNT(scored.f) as cnt')
                ->orderBy('f', 'desc')
                ->pluck('cnt');

        $mCount = DB::query()
                ->fromSub($subQuery, 'scored')
                ->rightJoin('ranks', 'ranks.rank', '=', 'scored.m')
                ->groupBy('ranks.rank')
                ->selectRaw('ranks.rank as m, COUNT(scored.m) as cnt')
                ->orderBy('m', 'desc')
                ->pluck('cnt');

        // $rCount = DB::table($subQuery)
        // ->rightJoin('ranks', 'ranks.rank', '=', 'r')
        // ->groupBy('rank')
        // ->selectRaw('rank as r, count(r)')
        // ->orderBy('r', 'desc')
        // ->pluck('count(r)');

        Log::debug($rCount);

        // $fCount = DB::table($subQuery)
        // ->rightJoin('ranks', 'ranks.rank', '=', 'f')
        // ->groupBy('rank')
        // ->selectRaw('rank as f, count(f)')
        // ->orderBy('f', 'desc')
        // ->pluck('count(f)');

        // $mCount = DB::table($subQuery)
        // ->rightJoin('ranks', 'ranks.rank', '=', 'm')
        // ->groupBy('rank')
        // ->selectRaw('rank as m, count(m)')
        // ->orderBy('m', 'desc')
        // ->pluck('count(m)');

        // Vue側に渡す空の配列
        $eachCount = [];
        // 初期値
        $rank = 5;

        for($i = 0; $i < 5; $i++) {
            array_push($eachCount, [
                'rank' => $rank,
                'r' => $rCount[$i] ?? 0,
                'f' => $fCount[$i] ?? 0,
                'm' => $mCount[$i] ?? 0,
            ]);
            $rank--;
        }

        // dd($total, $eachCount, $rCount, $fCount, $mCount);

        // 6.R/Fで2次元表示
        $data = DB::query()
            ->fromSub($subQuery, 'scored') // ★サブクエリに別名を付ける
            ->rightJoin('ranks', 'ranks.rank', '=', 'scored.r') // ★scored.r をJOIN条件に
            ->groupBy('ranks.rank')
            ->selectRaw('
                CONCAT("r_", ranks.rank) as rRank,
                COUNT(CASE WHEN scored.f = 5 THEN 1 END) as f_5,
                COUNT(CASE WHEN scored.f = 4 THEN 1 END) as f_4,
                COUNT(CASE WHEN scored.f = 3 THEN 1 END) as f_3,
                COUNT(CASE WHEN scored.f = 2 THEN 1 END) as f_2,
                COUNT(CASE WHEN scored.f = 1 THEN 1 END) as f_1
            ')
            ->orderBy('ranks.rank', 'desc') // ★rRank文字列より数値で並べるのが安全
            ->get();

        // dd($data);

        return [$data, $totals, $eachCount];
   }
}
