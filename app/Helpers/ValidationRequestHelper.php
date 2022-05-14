<?php

namespace App\Helpers;

use App\Models\Salesman;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidationRequestHelper {

    /**
     * Generate salesman validation error response
     * @param array $input
     * @param array $validationErrors
     * @param bool $expectsJson
     * @return void
     */
    public static function generateFailedValidationResponse(array $input, array $validationErrors, bool $expectsJson) {
        $httpCode = 0;
        $errors = [];

        foreach ($validationErrors as $failedAttribute => $failedReason) {
            $reason = key($failedReason);

            //
            if (array_key_exists($failedAttribute, $input) && is_array($input[$failedAttribute])) {
                $input[$failedAttribute] = $input[$failedAttribute][0];
            }

            //this is used only for In case
            $originalFailedAttribute = '';

            //when validating arrays, get input field and array key
            if (str_contains($failedAttribute, '.')) {
                $originalFailedAttribute = $failedAttribute;
                $failedAttribute = explode('.', $failedAttribute);
            }

            switch ($reason) {
                case 'In':
                    if (is_array($failedAttribute)) {
                        $value = $input[$failedAttribute[0]][(int)$failedAttribute[1]] ?? '';
                        $failedAttribute = $failedAttribute[0];
                    } else {
                        $value = $input[$failedAttribute] ?? '';
                    }
                    $allowedList = implode(',', $validationErrors[$originalFailedAttribute]['In']);
                    $errors[] = [
                        'code'    => Salesman::RESPONSE_CODE_BAD_FORMAT,
                        'message' => __('response.input_bad_format',
                            [
                                'field'       => $failedAttribute,
                                'input_value' => $value,
                                'good_type'   => $allowedList
                            ]
                        )
                    ];
                    $httpCode = 409;
                    break;

                case 'Unique':
                    $errors[] = [
                        'code'    => Salesman::RESPONSE_CODE_EXISTS,
                        'message' => __('response.salesman_exists',
                            [
                                'field'       => $failedAttribute,
                                'input_value' => $input[$failedAttribute]
                            ]
                        )
                    ];
                    $httpCode = 409;
                    break;

                case 'Max':
                case 'Size':
                case 'Digits':
                    $errors[] = [
                        'code'    => Salesman::RESPONSE_CODE_OUT_OF_RANGE,
                        'message' => __('response.input_out_of_range',
                            [
                                'field'       => $failedAttribute,
                                'input_value' => $input[$failedAttribute],
                                'good_range'  => $failedReason[$reason][0]
                            ]
                        )
                    ];
                    $httpCode = 416;
                    break;

                default:
                    //if it is an array, the posted value is inside input as array
                    if (is_array($failedAttribute)) {
                        $value = $input[$failedAttribute[0]][(int)$failedAttribute[1]] ?? '';
                        $failedAttribute = $failedAttribute[0];
                    } else {
                        $value = $input[$failedAttribute] ?? '';
                    }
                    $errors[] = [
                        'code'    => Salesman::RESPONSE_CODE_BAD_FORMAT,
                        'message' => __('response.input_bad_format',
                            [
                                'field'       => $failedAttribute,
                                'input_value' => $value,
                                'good_type'   => $reason
                            ]
                        )
                    ];
                    $httpCode = 400;
                    break;
            }
        }

        if ($expectsJson) {
            $response['errors'] = $errors;
            throw new HttpResponseException(
                response()->json($response, $httpCode)
            );
        }
    }
}
