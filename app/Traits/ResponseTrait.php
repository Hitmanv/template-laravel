<?php

/**
 * User: hitman
 * Date: 2019/9/12
 * Time: 3:07 PM
 */

namespace App\Traits;

use Illuminate\Support\Arr;

trait ResponseTrait
{
    protected function responseItem($data)
    {
        $resp = [
            'code' => 0,
            'errmsg' => '',
            'data' => $data
        ];

        return response()->json($resp);
    }

    protected function error($msg, $code = 10000, $data = null)
    {
        $resp = [
            'code' => $code,
            'errmsg' => $msg,
            'data' => $data
        ];

        return response()->json($resp);
    }

    public function responseList($listItem)
    {
        $data = [];
        $data['code'] = 0;
        $data['errmsg'] = '';
        $data['data'] = [
            'items' => $listItem
        ];

        return response()->json($data);
    }

    public function responseListWithPaginator($listItem, $extra = null)
    {
        $data['code'] = 0;
        $data['errmsg'] = '';
        $data['data']['items'] = $listItem->getCollection();
        $data['links'] = [
            'first' => $listItem->toArray()['first_page_url'] ?: null,
            'last' => $listItem->toArray()['last_page_url'] ?: null,
            'prev' => $listItem->toArray()['prev_page_url'] ?: null,
            'next' => $listItem->toArray()['next_page_url'] ?: null,
        ];
        $data['meta'] = Arr::except($listItem->toArray(), [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
        $data['extra'] = $extra;

        return response()->json($data);
    }
}
