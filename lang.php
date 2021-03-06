<?php
class Lang {
    private static $locale_text_array = [];
    
    /**
     * List of supported languages
     */
    private static $SUPPORTED_LANGS = array('en', 'sv');

    /**
     * Block construction
     */
    private function __construct() {}

    /**
     * Block cloning
     */
    private function __clone() {}

    /**
     * Initiate the texts for language $lang
     */
    public static function init($lang) {
        $LANG_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR;
        // Test if the language file exists before it is included. If a language file does not exist
        // for the preferred language then load english
        if(!file_exists($LANG_DIR."$lang.php")) {
            $lang = 'en';
        }
        include ($LANG_DIR."$lang.php");
        self::$locale_text_array = $locale_text_array;
        setlocale(LC_ALL, self::$locale_text_array["php_locale"]);
    }

    /**
     * Write out text with $text_id
     * @param string $text_id
     */
    public static function printText($text_id) {
        if(array_key_exists($text_id, self::$locale_text_array)) {
            echo self::$locale_text_array[$text_id];
        } else {
            echo $text_id;
            error_log("TEXT MISSING: (".self::$locale_text_array["iso_language"].") ".$text_id);
        }
    }

    /**
     * Returns a string with $text_id
     * @param string $text_id
     * @return string
     */
    public static function getText($text_id) {
        if(array_key_exists($text_id, self::$locale_text_array)) {
            return self::$locale_text_array[$text_id];
        }
        error_log("TEXT MISSING: (".self::$locale_text_array["iso_language"].") ".$text_id);
        return $text_id;
    }
    
    /**
     * Uses the Accept-Language header to find the clients preferred language
     * Returns a string with 2 characters that can be used by the init function to load the preferred language file
     * @return string one of sv|en
     */
    public static function getPreferedLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
            if (count($lang_parse[1])){
                $langs = array_combine($lang_parse[1], $lang_parse[4]);
                foreach ($langs as $lang => $val){
                    if ($val === '') $langs[$lang] = 1;
                }
                arsort($langs, SORT_NUMERIC);
            }
            foreach ($langs as $lang => $val){
                foreach(self::$SUPPORTED_LANGS as $la) {
                    if (strpos($lang,$la)===0) {
                        return $la;
                    }
                }
            }
        }
        return "en";
    }
}

// Get preferred language
$lang = Lang::getPreferedLanguage();

// Initiate the texts
Lang::init($lang);
