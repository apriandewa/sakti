<?php

namespace App\Services\Ekinerja;

/**
 * Exception khusus untuk seluruh kegagalan komunikasi dengan API BKN
 * (timeout, 401/403 token invalid, 5xx down, dsb).
 */
class BknApiException extends \RuntimeException
{
    //
}
