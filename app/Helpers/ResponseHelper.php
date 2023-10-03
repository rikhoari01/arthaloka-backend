<?php

function response_json($code, $status, $result)
{
  return response()->json([
    'code' => $code,
    'status' => $status,
    'result' => $result
  ], $code);
}