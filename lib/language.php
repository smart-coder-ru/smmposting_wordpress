<?php

class SMMP_Language
{
    private $locale = 'en_en';
    public function __construct()
    {
        $this->selectLanguage();
    }

    public function selectLanguage() {
        $locale = get_locale();
        $locale = strtolower($locale);
        $language_path = SMMP_PLUGIN_DIR . '/language/';

        if (is_dir($language_path . $locale)) {
            $this->locale = $locale;
        }
    }
    public function getFromLanguage($param = '')
    {
        include(SMMP_PLUGIN_DIR . '/language/'.$this->locale.'/marketing/smmposting.php');
        return isset($_[$param]) ? $_[$param] : '';
    }
    public function languages()
    {
        $locale = get_locale();
        $locale = strtolower($locale);

        include(SMMP_PLUGIN_DIR. '/language/'.$this->locale.'/marketing/smmposting.php');
        return isset($_) ? $_ : [];
    }
}