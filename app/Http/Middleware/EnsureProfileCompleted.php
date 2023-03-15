<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Models\CandidateInformation;
use App\Repositories\CandidateRepository;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureProfileCompleted
{
    /**
     * @var \App\Repositories\CandidateRepository
     */
    public $repository;

    public function __construct(CandidateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = $request->user();
            $candidate = $this->repository->findOneBy([
                'user_id' => $user->id
            ]);
            if (!$candidate->is_publish){
                return $this->errorResponse();
            }
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage());
        }
        return $next($request);
    }

    public function errorResponse($message = "Candidate profile not completed!"): JsonResponse
    {
        return response()->json([
            'status' => 'FAIL',
            'status_code' => HttpStatusCode::VALIDATION_ERROR,
            'message' => $message,
            'error' => ['details' => $message]
        ], HttpStatusCode::VALIDATION_ERROR);
    }


}
