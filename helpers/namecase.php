<?php

/**
 * Properly capitalize a name.
 *
 * @package   NameCase
 * @version   1.0.2
 * @author    Alex Dunae, Dialect <alex[at]dialect[dot]ca>
 * @copyright Copyright (c) 2008, Alex Dunae
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      http://dialect.ca/code/name-case/
 */
 
/**
 * Apply properly capitalization rules to a name.
 *
 * @param string $str
 * @returns string
 */    
function name_case($str) {
    // basic check for e-mail addresses to allow copy-and-paste of e-mail lists
    if(strpos($str, '@') && strpos($str, '.'))
        return $str;

    if(function_exists('mb_convert_encoding'))
        $str = @mb_convert_encoding($str, 'UTF-8', 'auto'); 
    
    $processed_chunks = array();

    // build name chunks
    $buffer = '';
    for($i = 0; $i < strlen($str); $i++) {
        // check for delimiters
        if(preg_match('/[\s]+/', $str[$i]) > 0 || $str[$i] == '-' || 
           $str[$i] == '.' || $str[$i] == ',') {
            $processed_chunks[] = _process_name_case_chunk($buffer . $str[$i]);
            $buffer = '';
        } else {
            $buffer .= $str[$i];
        }
    }

    $processed_chunks[] = _process_name_case_chunk($buffer);
    return trim(implode('', $processed_chunks));
}

/**
 * Process the chunks generated by the namecase function.
 *
 * This function should not be called directly.
 *
 * @param string $str
 * @returns string
 * @see name_case
 */    
function _process_name_case_chunk($str) {
    // Surname prefixes
    if(preg_match('/^(van|von|der|la|d[aeio]|d[ao]s|dit)[\s,]*$/i', $str))
        return strtolower($str);

    // Ordinal suffixes (I - VIII only)
    if(preg_match('/^(i{3}|i{1,2}v?|v?i{1,2})[\s,]*$/i', $str))
        return strtoupper($str);

    if(function_exists('mb_convert_case'))
        $str = mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    else
        $str = ucfirst(strtolower($str));

    // Second letter capitalized, like D'Angelo, McDonald, St. John, 0'Neil
    if(preg_match('/(^|\s)+(Mc|[DO]\'|St\.|St[\.]?[\s]|Dewolf)/i', $str)) {
        $str[2] = strtoupper($str[2]);
        return $str;
    }

    // Third letter capitalized, like MacDonald, MacRae
    if(preg_match('/(^|\s*)(Mac)(allist|arth|b|c(allu|art|ask|l|r|ull)|d|f|g|i(nn|nty|saa|v)|kinn|kn|l(a|ea|eo)|m|na[mu]|n[ei]|ph|q|ra|sw|ta|w)/i', $str)) {
        // not h,
        $str[3] = strtoupper($str[3]);
        return $str;
    }

    return $str;
}

/* References
 * - http://www.zu.ac.ae/publications/editorial/arabic.html
 * - http://snippets.dzone.com/posts/show/2010
 * - http://www.johncardinal.com/tmgutil/capitalizenames.htm
 * - http://freejava.info/capitalize-english-names/
 * - http://www.census.gov/genealogy/names/names_files.html
 */

?>