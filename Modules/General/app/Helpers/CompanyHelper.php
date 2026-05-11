<?php

namespace Modules\General\App\Helpers;

use Illuminate\Support\Facades\Auth;

class CompanyHelper
{
    public static function getCompanyAndBranch(): array
    {
        $user = Auth::user();

        return [
            'company_id' => $user->company_id ?? null,
            'branch_id'  => $user->branch_id ?? null,
        ];
    }
}
