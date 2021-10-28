<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;

use App\Http\Requests\ReligionRequest;
use App\Models\Religion;
use Symfony\Component\HttpFoundation\Response as FResponse;

class ReligionController extends Controller
{
    public function index()
    {
        try {
            try{
                $religion = Religion::where('status', 1)->orderBy('name')->get();
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['religions'] = $religion;
            return $this->sendSuccessResponse($data, 'Successfully retrieved', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function store(ReligionRequest $request)
    {
        try {
            try{
                $religion = Religion::create($request->all());
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['religion'] = $religion;
            return $this->sendSuccessResponse($data, 'Successfully created', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            try{
                $religion = Religion::findOrFail($id);
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['religion'] = $religion;
            return $this->sendSuccessResponse($data, 'Successfully retrieved', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function update(ReligionRequest $request, $id)
    {
        try {
            try{
                $religion = Religion::findOrFail($id);
                $religion->update($request->all());
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['religion'] = $religion;
            return $this->sendSuccessResponse($data, 'Successfully updated', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        try {
            try{
                Religion::destroy($id);
            }
            catch (\Illuminate\Database\QueryException $ex){
                return $this->sendErrorResponse($ex->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
            }
            $data['religion'] = array();
            return $this->sendSuccessResponse($data, 'Successfully deleted', [], FResponse::HTTP_OK);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], FResponse::HTTP_BAD_REQUEST);
        }
    }
}
