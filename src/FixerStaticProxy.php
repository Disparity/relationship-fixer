<?php

namespace Fixrel;

final class FixerStaticProxy
{
    /**
     * @var Fixer
     */
    private static $fixer;


    private function __construct()
    {
    }

    /**
     * @param Fixer $fixer
     */
    public static function setFixer(Fixer $fixer)
    {
        static::$fixer = $fixer;
    }

    /**
     * @return Fixer
     */
    public static function getFixer()
    {
        return static::$fixer;
    }
}
