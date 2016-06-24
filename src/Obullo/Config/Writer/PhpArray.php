<?php

namespace Obullo\Config\Writer;

use RuntimeException;

/**
 * PhpArray Writer
 * 
 * Borrowed from Zend Framework 
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class PhpArray extends AbstractWriter
{
    /**
     * Index string
     * 
     * @var string
     */
    const INDENT_STRING = '    ';

    /**
     * Use bracket array syntax
     * 
     * @var boolean
     */
    protected $useBracketArraySyntax = true;

    /**
     * Defined by AbstractWriter.
     *
     * @param array $config config
     * 
     * @return string
     */
    public function processConfig(array $config)
    {
        $arraySyntax = array(
            'open' => $this->useBracketArraySyntax ? '[' : 'array(',
            'close' => $this->useBracketArraySyntax ? ']' : ')'
        );

        return "<?php\n\n" .
               "return array(\n\n" . $this->processIndented($config, $arraySyntax).");\n";
    }

    /**
     * Sets whether or not to use the PHP 5.4+ "[]" array syntax.
     *
     * @param bool $value value
     * 
     * @return self
     */
    public function setUseBracketArraySyntax($value)
    {
        $this->useBracketArraySyntax = $value;
        return $this;
    }

    /**
     * Defined by Writer interface.
     *
     * @param string $filename      filename
     * @param mixed  $config        config
     * @param bool   $exclusiveLock exclusive lock
     * 
     * @see    WriterInterface::toFile()
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     * 
     * @return void
     */
    public function toFile($filename, $config, $exclusiveLock = true)
    {
        if (empty($filename)) {
            throw new RuntimeException('No file name specified');
        }
        if (! is_writable($filename)) {   // Check file is writable
            throw new RuntimeException(
                sprintf(
                    '%s file is not writable.', 
                    $filename
                )
            );
        }
        $flags = 0;
        if ($exclusiveLock) {
            $flags |= LOCK_EX;
        }
        set_error_handler(
            function ($error, $message = '', $file = '', $line = 0) use ($filename) {
                $file = $line = null;
                throw new RuntimeException(
                    sprintf('Error writing to "%s": %s', $filename, $message),
                    $error
                );
            },
            E_WARNING
        );
        try {
            // for Windows, paths are escaped.
            $dirname = str_replace('\\', '\\\\', dirname($filename));

            $string = $this->toString($config);
            $string = str_replace("'" . $dirname, "__DIR__ . '", $string);

            file_put_contents($filename, $string, $flags);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
        restore_error_handler();
    }

    /**
     * Recursively processes a PHP config array structure into a readable format.
     *
     * @param array $config      config
     * @param array $arraySyntax array syntac
     * @param int   $indentLevel indent level
     * 
     * @return string
     */
    protected function processIndented(array $config, array $arraySyntax, &$indentLevel = 1)
    {
        $arrayString = "";
        foreach ($config as $key => $value) {
            $arrayString .= str_repeat(self::INDENT_STRING, $indentLevel);
            $arrayString .= (is_int($key) ? $key : "'" . addslashes($key) . "'") . ' => ';

            if (is_array($value)) {
                if ($value === array()) {
                    $arrayString .= $arraySyntax['open'] . $arraySyntax['close'] . ",\n";
                } else {
                    $indentLevel++;
                    $arrayString .= $arraySyntax['open'] . "\n"
                                  . $this->processIndented($value, $arraySyntax, $indentLevel)
                                  . str_repeat(self::INDENT_STRING, --$indentLevel) . $arraySyntax['close'] . ",\n";
                }
            } elseif (is_object($value) || is_string($value)) {
                $arrayString .= var_export($value, true) . ",\n";
            } elseif (is_bool($value)) {
                $arrayString .= ($value ? 'true' : 'false') . ",\n";
            } elseif ($value === null) {
                $arrayString .= "null,\n";
            } else {
                $arrayString .= $value . ",\n";
            }
        }
        return $arrayString;
    }
}