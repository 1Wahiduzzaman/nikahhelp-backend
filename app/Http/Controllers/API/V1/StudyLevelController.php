<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudyLevelRequest;
use App\Models\StudyLevel;
use Symfony\Component\HttpFoundation\Response as FResponse;

class StudyLevelController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $studylevels = StudyLevel::orderBy('name')->get();
            $data['studylevels'] = $studylevels;
            return $this->sendSuccessResponse($data, 'Successfully retrieved', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param \App\Http\Requests\StudyLevelRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StudyLevelRequest $request)
    {
        try {
            try{
                $studylevel = StudyLevel::create($request->all());
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['studylevels'] = $studylevel;
            return $this->sendSuccessResponse($data, 'Successfully created', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            try{
                $studylevel = StudyLevel::findOrFail($id);
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['studylevels'] = $studylevel;
            return $this->sendSuccessResponse($data, 'Successfully retrieved', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function update(StudyLevelRequest $request, $id)
    {
        try {
            try{
                $studylevel = StudyLevel::findOrFail($id);
                $studylevel->update($request->all());
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['studylevels'] = $studylevel;
            return $this->sendSuccessResponse($data, 'Successfully updated', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        try {
            try{
                StudyLevel::destroy($id);
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['studylevels'] = array();
            return $this->sendSuccessResponse($data, 'Successfully deleted', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }
}
