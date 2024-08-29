<?php
/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_Shiprestriction
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */


namespace Mavenbird\Shiprestriction\Debug;

/**
 * For Remote Debug
 * Output is to front
 * @codeCoverageIgnore
 * @codingStandardsIgnoreFile
 */
class VarDump
{
    /**
     * @var array
     */
    private static $addressPath = [
        'REMOTE_ADDR',
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];

    /**
     * @var array
     */
    private static $mavenbirdIps = [
        '213.184.226.82',
        '87.252.238.217',
    ];

    /**
     * @var int
     */
    private static $objectDepthLevel = 1;

    /**
     * @var int
     */
    private static $arrayDepthLevel = 2;

    /**
     * @param int $level
     */
    public static function setObjectDepthLevel($level)
    {
        self::$objectDepthLevel = (int)$level;
    }

    /**
     * @param int $level
     */
    public static function setArrayDepthLevel($level)
    {
        self::$arrayDepthLevel = (int)$level;
    }

    public static function execute()
    {
        if (self::isAllowed()) {
            foreach (func_get_args() as $var) {
                System\Beautifier::getInstance()->beautify(self::dump($var));
            }
        }
    }

    /**
     * @param array $array
     * @param int   $level
     *
     * @return array|string
     */
    private static function castArray($array, $level)
    {
        if ($level > self::$arrayDepthLevel) {
            return '...';
        }
        $result = [];
        foreach ($array as $key => $value) {
            switch (strtolower(gettype($value))) {
                case 'object':
                    $result[$key] = self::castObject($value, $level + 1);
                    break;
                case 'array':
                    $result[$key] = self::castArray($value, $level + 1);
                    break;
                default:
                    $result[$key] = $value;
                    break;
            }
        }
        return $result;
    }

    /**
     * @param     $object
     * @param int $level
     *
     * @return System\MavenbirdDump|string
     */
    private static function castObject($object, $level)
    {
        if ($level > self::$objectDepthLevel) {
            return '...';
        }
        $reflection = new \ReflectionClass($object);

        $mavenbirdDump = new System\MavenbirdDump();

        $mavenbirdDump->className = $reflection->getName();
        $mavenbirdDump->shortClassName = $reflection->getShortName();

        foreach ($reflection->getMethods() as $method) {
            $mavenbirdDump->methods[] = $method->getName();
        }
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyValue = $property->getValue($object);
            switch (strtolower(gettype($propertyValue))) {
                case 'object':
                    $mavenbirdDump->properties[$property->name] = self::castObject($propertyValue, $level + 1);
                    break;
                case 'array':
                    $mavenbirdDump->properties[$property->name] = self::castArray($propertyValue, $level + 1);
                    break;
                default:
                    $mavenbirdDump->properties[$property->name] = $propertyValue;
                    break;
            }
        }
        return $mavenbirdDump;
    }

    /**
     * @param mixed $var
     *
     * @return MavenbirdDump|array|mixed
     */
    public static function dump($var)
    {
        if (self::isAllowed()) {
            switch (strtolower(gettype($var))) {
                case 'object':
                    return self::castObject($var, 0);
                case 'array':
                    return self::castArray($var, 0);
                case 'resource':
                    return stream_get_meta_data($var);
                default:
                    return $var;
            }
        }
    }

    /**
     * Check for Mavenbird Ips
     *
     * @return bool
     */
    public static function isAllowed()
    {
        foreach (self::$addressPath as $path) {
            if (!empty($_SERVER[$path]) && in_array($_SERVER[$path], self::$mavenbirdIps, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $code
     */
    public static function mavenbirdExit($code = 0)
    {
        if (self::isAllowed()) {
            if (class_exists(\Zend\Console\Response::class)) {
                (new \Zend\Console\Response())->send();
            }
        }
    }

    /**
     * @param string $string
     */
    public static function mavenbirdEcho($string)
    {
        if (self::isAllowed()) {
            printf('%s', $string);
        }
    }
}
