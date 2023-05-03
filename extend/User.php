<?php

declare(strict_types=1);

namespace extend;

use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\DatabaseInterface;

class User
{

//    /** @var DatabaseInterface */
//    protected $dbi;
//
//    public function __construct(?DatabaseInterface $dbi = null)
//    {
//        if (empty($dbi)) {
//            $dbi = $GLOBALS['dbi'];
//        }
//
//        $this->dbi = $dbi;
//    }

    public static function getUserName() : array
    {
        return Array('name'=>'--no-user-xyz-from-extend-user--');
    }

}