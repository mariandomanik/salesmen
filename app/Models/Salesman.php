<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salesman extends Model {
    use HasFactory;
    use \App\Traits\TraitUuid;

    //do not allow mass assign for these columns
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'display_name',
        'titles_before',
        'titles_after'
    ];

    //JSON Response Codes
    public const RESPONSE_CODE_NOT_FOUND = 'PERSON_NOT_FOUND';
    public const RESPONSE_CODE_FORBIDDEN = 'FORBIDDEN';
    public const RESPONSE_CODE_BAD_REQUEST = 'BAD_REQUEST';
    public const RESPONSE_CODE_EXISTS = 'PERSON_ALREADY_EXISTS';
    public const RESPONSE_CODE_BAD_FORMAT = 'INPUT_DATA_BAD_FORMAT';
    public const RESPONSE_CODE_OUT_OF_RANGE = 'INPUT_DATA_OUT_OF_RANGE';

    //allowed titles before
    public const TITLES_BEFORE = [
        'Bc.',
        'Mgr.',
        'Ing.',
        'JUDr.',
        'MVDr.',
        'MUDr.',
        'PaedDr.',
        'prof.',
        'doc.',
        'dipl.',
        'MDDr.',
        'Dr.',
        'Mgr.art.',
        'ThLic.',
        'PhDr.',
        'PhMr.',
        'RNDr.',
        'ThDr.',
        'RSDr.',
        'arch.',
        'PharmDr.'
    ];

    //allowed titles after
    public const TITLES_AFTER = [
        'CSc.',
        'DrSc.',
        'PhD.',
        'ArtD.',
        'DiS',
        'DiS.art',
        'FEBO',
        'MPH',
        'BSBA',
        'MBA',
        'DBA',
        'MHA',
        'FCCA',
        'MSc.',
        'FEBU',
        'LL.M'
    ];

    public const GENDERS = [
        'm',
        'f'
    ];

    public const MARITAL_STATUSES = [
        'single',
        'married',
        'divorced',
        'widowed'
    ];

    /**
     * Return comma-separated list of titles after
     * @return string
     */
    public static function getTitlesAfterList(): string {
        return implode(',', self::TITLES_AFTER);
    }

    /**
     * Return comma-separated list of titles before
     * @return string
     */
    public static function getTitlesBeforeList(): string {
        return implode(',', self::TITLES_BEFORE);
    }


    /**
     * Return comma-separated list of genders
     * @return string
     */
    public static function getGendersList(): string {
        return implode(',', self::GENDERS);
    }

    /**
     * Return comma-separated list of marital statuses
     * @return string
     */
    public static function getMaritalStatusesList(): string {
        return implode(',', self::MARITAL_STATUSES);
    }

    /**
     * Generate display_name attribute in format Titles_Before First_Name Last_Name, Titles_After
     * Example Ing. John Rambo, Phd.
     * @return Attribute Eloquent attribute display_name
     */
    protected function displayName(): Attribute {
        return Attribute::make(
            get: static function ($value, $attributes) {
                $displayName = '';

                if ($attributes['titles_before'] !== '') {
                    $displayName .= "{$attributes['titles_before']} ";
                }

                $displayName .= "{$attributes['first_name']} {$attributes['last_name']}";

                if ($attributes['titles_after'] !== '') {
                    $displayName .= ", {$attributes['titles_after']}";
                }

                return $displayName;
            }
        );
    }

    /**
     * Transform titles_before string into array
     * @return Attribute
     */
    protected function titlesBefore(): Attribute {
        return Attribute::make(
            get: static fn($value, $attributes) => explode(',', $attributes['titles_before'])
        );
    }

    /**
     * Transform titles_after string into array
     * @return Attribute
     */
    protected function titlesAfter(): Attribute {
        return Attribute::make(
            get: static fn($value, $attributes) => explode(',', $attributes['titles_after'])
        );
    }
}
