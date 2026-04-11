<?php

namespace App\Modules\Core\Shared\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Modules\Core\Shared\Services\LoadDataService;
use App\Modules\Core\Shared\Traits\HasResponse;
use http\Env\Request;
use Illuminate\Support\Facades\Session;

class MetaDataApiController extends Controller
{
    use HasResponse;

    public function __construct(protected LoadDataService $loadService)
    {}
    public function loadMetaData(){

        try {
            $data = $this->loadService->loadAllData();

            return $this->success(
                'Metadata loaded successfully',
                $data,
                200
            );

        } catch (\Exception $e) {
            return $this->error(
                'Failed to load metadata',
                ['exception' => $e->getMessage()],
                500
            );
        }

    }

    public function keepSessionAlive(): \Illuminate\Http\JsonResponse
    {
        // Regenerate the CSRF token to keep it fresh
        Session::regenerateToken();
        return response()->json([
            'status' => 'refreshed',
            'csrf_token' => csrf_token(),
        ]);
    }
}
