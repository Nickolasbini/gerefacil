<?php

// returns translation of text or creates a new translation based on it
function translate($text = null)
{
    if(!$text)
        return;
    $userLanguage = (session()->get('userLanguage') ? session()->get('userLanguage') : env('USER_LANGUAGE'));
    $pathToTranslations = storage_path('app/translations/systemTranslations.json');
    if(!file_exists($pathToTranslations)){
        mkdir(storage_path('app/translations'));
        file_put_contents($pathToTranslations, '[]');
    }
    $systemTranslations = json_decode(file_get_contents($pathToTranslations), true);
    if(!array_key_exists($text, $systemTranslations)){
        $systemTranslations[$text] = [
            'en' => $text,
            'pt' => '',
            'es' => ''
        ];
        file_put_contents($pathToTranslations, json_encode($systemTranslations));
        $translatedText = $text;
    }else{
        $translatedText = ($systemTranslations[$text][$userLanguage] != '' ? $systemTranslations[$text][$userLanguage] : $systemTranslations[$text]['en']);
    }
    return $translatedText;
}
?>