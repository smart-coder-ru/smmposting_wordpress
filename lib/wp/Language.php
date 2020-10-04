<?php
/**
 * Language class
 */
class SMMP_Language {
    private $locale = 'en_en';

    public function __construct($directory = '') {
        $locale = get_locale();
        $locale = strtolower($locale);
        $language_path = SMMP_PLUGIN_DIR . '/language/';

        if (is_dir($language_path . $locale)) {
            $this->locale = $locale;
        }
    }

    public function get($param) {
        include(SMMP_PLUGIN_DIR . '/language/'.$this->locale.'/marketing/smmposting.php');
        return isset($_[$param]) ? $_[$param] : '';
    }

    public function all() {
        return $this->languages();
    }

    public function languages()
    {
        $locale = get_locale();
        $locale = strtolower($locale);

        include(SMMP_PLUGIN_DIR. '/language/'.$this->locale.'/marketing/smmposting.php');
        return isset($_) ? $_ : [];
    }



    public function load($filename, $key = '') {
    }
}