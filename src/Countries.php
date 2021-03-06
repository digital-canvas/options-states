<?php
namespace DigitalCanvas\Options;

use RuntimeException;

class Countries
{
    /**
     * @var string|null
     */
    private static $file;

    /**
     * @var array|null
     */
    private static $countries;

    /**
     * Sets the xml file to use to load countries
     *
     * @param string|null $file
     *
     * @throws RuntimeException
     */
    public static function setXML($file = null)
    {
        if (is_null($file)) {
            $file = __DIR__ . '/countries.xml';
        }
        if (!is_file($file)) {
            throw new RuntimeException('Country XML file does not exist.');
        }
        self::$file = realpath($file);
        self::$countries = null;
    }

    /**
     * Loads the countries from the xml file.
     */
    private static function loadCountries()
    {
        if (is_null(self::$file)) {
            self::setXML();
        }
        // Load xml file
        $xml = simplexml_load_file(self::$file);
        $countries = array();
        // loop through states
        foreach ($xml as $country) {
            $countries[(string) $country->code] = array(
                'code' => (string) $country->code,
                'name' => (string) $country->name
            );
        }
        // Cache states
        self::$countries = $countries;
        // Clear xml instance
        unset($xml, $countries, $country);
    }

    /**
     * Clears the data from the cache.
     */
    public static function clearCache()
    {
        self::$countries = null;
    }

    /**
     * Checks if data has been cached
     *
     * @return boolean
     */
    public static function isCached()
    {
        return !is_null(self::$countries);
    }

    /**
     * Returns array of countries
     *
     * @return array
     */
    public static function getArray()
    {
        // Load States if they are not yet loaded.
        if (is_null(self::$countries)) {
            self::loadCountries();
        }
        return self::$countries;
    }

    /**
     * Returns countries as abbreviation=>name pair array
     *
     * @return array
     */
    public static function getPairs()
    {
        // Load States if they are not yet loaded.
        if (is_null(self::$countries)) {
            self::loadCountries();
        }
        $countries = array();
        foreach (self::$countries as $country) {
            $countries[$country['code']] = $country['name'];
        }
        return $countries;
    }

    /**
     * Returns a single country by abbreviation
     *
     * @param string $country
     * @param bool $name_only
     *
     * @return string|array|null
     */
    public static function getCountry($country, $name_only = true)
    {
        // Load States if they are not yet loaded.
        if (is_null(self::$countries)) {
            self::loadCountries();
        }
        if (array_key_exists($country, self::$countries)) {
            return ($name_only) ? self::$countries[$country]['name'] : self::$countries[$country];
        } else {
            return null;
        }
    }
}
