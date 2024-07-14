<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class SubscriptionReportResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $subscriptionhistory = [];

        if (! empty($this->subscription_expire_at)) {
            $subscriptionhistory = DB::table('subscriptions')->where('team_id', '=', $this->id)->get();
        }

        $result = [
            'id' => $this->id ?? null,
            'team_id' => $this->team_id ?? null,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'member_count' => $this->member_count ?? null,
            'subscription_id' => $this->subscription_id ?? null,
            'subscription_expire_at' => $this->subscription_expire_at ?? null,
            'status' => $this->status ?? null,
            'password' => $this->password ?? null,
            'logo' => $this->logo ?? null,
            'subscriptionhistory' => $subscriptionhistory ?? null,
            'created_by' => $this->user->full_name ?? null,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];

        return $result;

    }
}
