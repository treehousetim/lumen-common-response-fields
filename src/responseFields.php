<?php namespace treehousetim\responseFields;

trait responseFields
{
    protected function crf_getCommonFields( $message, $errorMessage, int $code )
    {
        return [
            'success_msg' => $message,
            'error_msg' => $errorMessage,
            'httpCode' => $code,
            'request_utc' => Carbon::now()->toDateTimeString()
        ];
    }
    //------------------------------------------------------------------------
    protected function successResponse( \Illuminate\Database\Eloquent\Model $obj, string $message = 'Success', int $code = 200 ) : JsonResponse
    {
        $data = $this->crf_getCommonFields( $message, null, $code ) + $obj->toArray();
        return $response = new JsonResponse( $data, $code );
    }
    //------------------------------------------------------------------------
    protected function successList( \Illuminate\Database\Eloquent\Collection $obj, $namespace = 'record', string $message = 'Success', int $code = 200 ) : JsonResponse
    {
        $data = $this->crf_getCommonFields( $message, null, $code );
        $data[$namespace] = [];
        foreach( $obj as $item )
        {
            $data[$namespace][] = $item;
        }
        return $response = new JsonResponse( $data, $code );
    }
    //------------------------------------------------------------------------
    protected function failResponse( \Illuminate\Database\Eloquent\Model $obj, string $message = 'failed', int $code = 500 ) : JsonResponse
    {
        $data = $this->crf_getCommonFields( null, $message, $code ) + $obj->toArray();
        return $response = new JsonResponse( $data );
    }
    //------------------------------------------------------------------------
    protected function hardFailResponse( string $message = 'failed', int $code = 500 )
    {
        $data = $this->crf_getCommonFields( null, $message, $code );
        return $response = new JsonResponse( $data, $code );
    }
    //------------------------------------------------------------------------
    protected function notFoundResponse( string $message = 'not found', $code = 404 )
    {
        $data = $this->crf_getCommonFields( null, $message, $code );
        return $response = new JsonResponse( $data );
    }
}