<?php

namespace App\Traits;

use Vinkla\Hashids\HashidsManager;

trait Hashable
{
    /**
     * @var HashidsManager
     */
    protected HashidsManager $hash;

    /**
     * Hashable constructor.
     * @param HashidsManager $hashids
     */
    public function __construct(HashidsManager $hashids)
    {
        parent::__construct();
        $this->hash = $hashids;
    }
}
