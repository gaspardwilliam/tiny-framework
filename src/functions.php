<?php
function pre($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function slugify($string)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

function generateSlug($value)
{

    // Convert all dashes to hyphens
    $value = str_replace('—', '-', $value);
    $value = str_replace('‒', '-', $value);
    $value = str_replace('―', '-', $value);

    // Convert underscores and spaces to hyphens
    $value = str_replace('_', '-', $value);
    $value = str_replace(' ', '-', $value);

    // Convert all accented latin-1 supplement characters to their non-accented counterparts
    // Characters are taken from https://en.wikipedia.org/wiki/Latin-1_Supplement_(Unicode_block)
    $accents = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ');
    $noAccents = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'B', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'p', 'y');

    $value = str_replace($accents, $noAccents, $value);

    // Remove everything except 0-9, a-z, A-Z and hyphens
    $value = preg_replace('/[^A-Za-z0-9-]+/', '', $value);

    // Make lowercase - no need for this to be multibyte since there are only 0-9, a-z, A-Z and hyphens left in the string
    $value = strtolower($value);

    // Only allow single hyphens
    do {

        $value = str_replace('--', '-', $value);

    } while (mb_substr_count($value, '--') > 0);

    return $value;

}
