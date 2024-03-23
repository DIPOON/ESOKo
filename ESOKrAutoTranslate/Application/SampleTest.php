<?php
require '../vendor/autoload.php';

use Google\Cloud\Translate\V3\TranslationServiceClient;

$translationClient = new TranslationServiceClient();
$content = [
    "Roar with bloodlust to terrify up to |cffffff6|r nearby enemies, fearing them for <<1>> and setting them Off Balance for <<2>>.\n\nYour Heavy Attacks also are <<3>> faster for <<4>> after casting.\n\nWhile slotted you gain <<5>> and Prophecy, increasing your Weapon and Spell Critical rating by <<6>>.",
    "You are to be congratulated for arriving at Champion 100. As a commemoration of your achievement, the Manimarco costume has been added to your Collection!",

];
$targetLanguage = 'ko';
$locationName = TranslationServiceClient::locationName('horizontal-cab-417404', 'global');
$response = $translationClient->translateText(
    $content,
    $targetLanguage,
    $locationName
);

foreach ($response->getTranslations() as $key => $translation) {
    $separator = $key === 2
        ? '!'
        : ', ';
    echo $translation->getTranslatedText() . $separator;
}