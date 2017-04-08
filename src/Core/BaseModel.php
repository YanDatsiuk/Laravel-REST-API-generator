<?php

namespace TMPHP\RestApiGenerators\Core;

use TMPHP\RestApiGenerators\Contracts\Validable as ValidableContract;
use TMPHP\RestApiGenerators\Helpers\Validable;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Model realization base logic of models:
 * interactions with DB, containing validation rules from model
 *
 * Class BaseModel
 *
 * @package App\Core
 */
abstract class BaseModel extends Model implements ValidableContract
{
    use Validable;
}