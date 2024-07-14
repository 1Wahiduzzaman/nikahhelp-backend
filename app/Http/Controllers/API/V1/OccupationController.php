<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OccupationRequest;
use App\Models\Occupation;
use Symfony\Component\HttpFoundation\Response as FResponse;

class OccupationController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $data['occupations'] = Occupation::pluck('name', 'id');

            //        $occupations = Occupation::latest()->get();
            return $this->sendSuccessResponse($data, 'Occupation List', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(OccupationRequest $request)
    {
        try {
            $data['occupation'] = Occupation::create($request->all());

            return $this->sendSuccessResponse($data, 'Occupation created successfully', [], FResponse::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data['occupation'] = Occupation::findOrFail($id);

            return $this->sendSuccessResponse($data, 'Occupation List', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(OccupationRequest $request, $id)
    {
        try {
            $occupation = Occupation::findOrFail($id);
            $occupation->update($request->all());
            $data['occupation'] = $occupation;

            return $this->sendSuccessResponse($data, 'Occupation List', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data['occupation'] = Occupation::destroy($id);

            return $this->sendSuccessResponse($data, 'Occupation List', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }

        return response(['data' => null], 204);
    }
}
