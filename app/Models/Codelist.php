<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Codelist extends Model {

    /**
     * Generate codelist array
     * @return array
     */
    public static function getCodelist(): array {
        $allowedTitlesBefore = Salesman::TITLES_BEFORE;
        $allowedTitlesAfter = Salesman::TITLES_AFTER;
        $newTitlesBefore = $newTitlesAfter = [];
        $appendTitlesBefore = $appendTitlesAfter = [];

        //for titles before and titles after, gather the name values from translation file
        foreach ($allowedTitlesBefore as $titleBefore) {
            $titleBeforeTranslationKey = strtolower($titleBefore);
            $titleBeforeTranslationKey = str_replace('.', '', $titleBeforeTranslationKey);
            $newTitlesBefore[$titleBefore] = __('codelist.title_before_' . $titleBeforeTranslationKey);
        }

        foreach ($allowedTitlesAfter as $titleAfter) {
            $titleAfterTranslationKey = strtolower($titleAfter);
            $titleAfterTranslationKey = str_replace('.', '', $titleAfterTranslationKey);
            $newTitlesAfter[$titleAfter] = __('codelist.title_after_' . $titleAfterTranslationKey);
        }

        //generate codelist for titles
        foreach ($newTitlesBefore as $originalTitle => $translatedTitle) {
            $appendTitlesBefore[] = [
                'code' => $originalTitle,
                'name' => $translatedTitle
            ];
        }

        foreach ($newTitlesAfter as $originalTitle => $translatedTitle) {
            $appendTitlesAfter[] = [
                'code' => $originalTitle,
                'name' => $translatedTitle
            ];
        }

        $fullCodeList = [
            'marital_statuses' => [
                'code' => 'single',
                'name' => [
                    'm'       => __('codelist.marital_status_single_m'),
                    'f'       => __('codelist.marital_status_single_f'),
                    'general' => __('codelist.marital_status_single_general')
                ]
            ],
            'genders'          => [
                [
                    'code' => 'm',
                    'name' => __('codelist.gender_m')
                ],
                [
                    'code' => 'f',
                    'name' => __('codelist.gender_f')
                ]
            ],
        ];

        //append titles to the codelist
        $fullCodeList['titles_before'] = $appendTitlesBefore;
        $fullCodeList['titles_after'] = $appendTitlesAfter;

        return $fullCodeList;
    }
}
