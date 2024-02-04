<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ApiPortfolioController extends Controller
{
    public function getPortfolio(Request $request)
    {
        $data = Portfolio::where('provider_id', $request->provider_id)->paginate(4);
        return response()->json($data, 200);
    }

    public function adminDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('users.id', 'users.email', 'users.created_at', 'providers.company_name', 'providers.district', 'users.role', 'providers.id as provider_id')->leftJoin('providers', 'providers.user_id', '=', 'users.id')
                ->where('role', 'provider')
                ->where('district', 'LIKE', '%' . $request->district . '%')
                ->where('providers.deleted_at', null)
                ->latest();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '
                    <td>
                        <div class="d-flex flex-column">
                            <a href="' . route('portfolio.index') . '?provider_id=' . $data->provider_id . '" class="btn btn-warning my-2">Edit</a>
                        </div>
                    </td>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
