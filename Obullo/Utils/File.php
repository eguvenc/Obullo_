<?php

namespace Utils;

/**
 * File helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class File
{
    /**
     * Don't show root paths for security
     * reason.
     * 
     * @param string $file file path
     * 
     * @return string
     */
    public static function getSecurePath($file)
    {
        $CONSTANTS = [
            'ASSETS',
            'DATA',
            'TRANSLATIONS',
            'CLASSES',
            'TEMPLATES',
            'TASKS',
            'RESOURCES',
            'FOLDERS',
            'OBULLO',
            'VENDOR',
            'APP',
            'ROOT',
        ];
        if (! is_string($file)) {
            return $file;
        }
        foreach ($CONSTANTS as $constant) {
            $value = constant($constant);
            if (strpos($file, $value) === 0) {
                $file = $constant .'/'. substr($file, strlen($value));
            }
        }
        return $file;
    }

    // TESTS:

    // echo \Utils\File::getSecurePath(ROOT)."<br>";
    // echo \Utils\File::getSecurePath(APP)."<br>";
    // echo \Utils\File::getSecurePath(OBULLO)."<br>";
    // echo \Utils\File::getSecurePath(VENDOR)."<br>";
    // echo \Utils\File::getSecurePath(FOLDERS)."<br>";
    // echo \Utils\File::getSecurePath(ASSETS)."<br>";
    // echo \Utils\File::getSecurePath(DATA)."<br>";
    // echo \Utils\File::getSecurePath(TRANSLATIONS)."<br>";
    // echo \Utils\File::getSecurePath(CLASSES)."<br>";
    // echo \Utils\File::getSecurePath(TEMPLATES)."<br>";
    // echo \Utils\File::getSecurePath(TASKS)."<br>";
    // echo \Utils\File::getSecurePath(RESOURCES)."<br>";

}