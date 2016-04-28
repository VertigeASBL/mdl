<?php
/**
 * Fonctions utiles au plugin Multi-Domaines par langues
 *
 * @plugin     Multi-Domaines par langues
 * @copyright  2014
 * @author     Vertige ASBL
 * @licence    GNU/GPL
 * @package    SPIP\Mdl\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Retourne une version sans protocole et avec slash de fin d'une url
 *
 * Facilite la comparaison entre les urls…
 *
 * @param String $url
 *     L'url à normaliser
 * @return String
 *     La version normalisée de l'url
 */
function mdl_normaliser_url ($url) {

    /* Le protocole utilisé ne rentre pas en ligne de compte, alors on
       utilise des urls implicites (commencant par "//") */
    include_spip('inc/filtres_mini');
    $url = protocole_implicite($url);

    if (strpos($url, '//') !== 0) {
        $url = '//' . $url;
    }

    $url = preg_replace('#([^/])$#', '$1/', $url);

    return $url;
}

/**
 * Devine la langue de l'url demandée selon le nom de domaine.
 *
 * @param String $url
 *     L'url dont on cherche la langue
 * @return String
 *     Le code de langue correspondant à l'url donnée, si on le trouve.
 */
function mdl_langue_url_selon_domaine ($url) {

    $url = mdl_normaliser_url($url);

    foreach ($GLOBALS['domaines'] as $lang => $domaine) {
        if (strpos($url, mdl_normaliser_url($domaine)) === 0) {
            return $lang;
        }
    }
    return NULL;
}

/**
 * si le nom de domaine de $url ne correspond pas à $lang, on le
 * remplace par le bon nom de domaine
 *
 * @param String $url
 *     L'url à modifier
 * @param String $lang
 *     La langue que l'on souhaite pour l'url retournée
 * @return String
 *     Une url pointant sur le contenu de l'url donnée, mais sur le
 *     nom de domaine correspondant à la langue passée en paramètre.
 */
function mdl_force_domaine_url_selon_langue ($url, $lang) {

    $url = mdl_normaliser_url($url);

    $langue_url = mdl_langue_url_selon_domaine($url);

    if ($langue_url !== $lang) {

        /* S'il n'y a pas de protocole dans l'url, la fonction
           parse_url est buggée pour php < 5.4, on fait donc une
           bidouille pour contourner le bug */
        if (strpos($url, '//') === 0) {
            $no_scheme = true;
            $url = 'http:' . $url;
        }

        $composants_url = parse_url($url);

        if ($no_scheme) {
            unset($composants_url['scheme']);
        }
        /* On remplace le nom de domaine */
        $composants_url['host'] = trim($GLOBALS['domaines'][$lang],'/');
        $url = mdl_unparse_url($composants_url);

        /* Du coup plus besoin de paramètre lang dans l'url */
        $url = parametre_url($url, 'lang', '');
    }

    return mdl_normaliser_url($url);
}

/**
 * #URL_SITE_SPIP passe dans cette moulinette.
 *
 * On se sert du contexte du squelette à la compilation pour trouver
 * la langue voulue, et on retourne un version de l'url avec le nom de
 * domaine correspondant.
 */
function mdl_traitement_domaine_par_langue($url, $contexte) {

    $url = html_entity_decode($url);

    $url = mdl_force_domaine_url_selon_langue($url, $contexte['lang']);

    $url = htmlentities($url);

    return $url;
}

/**
 * L'inverse de parse_url
 *
 * Inspiré par http://php.net/manual/en/function.parse-url.php#106731
 *
 * @param array $parsed_url : Des bouts d'urls, tels que retournés par la fonction parse_url
 *
 * @return String : L'url correspondant aux bouts qu'on a passé en argument
 */
function mdl_unparse_url ($parsed_url) {

    $scheme   = isset($parsed_url['scheme'])   ? $parsed_url['scheme'] . '://'  : '//';
    $host     = isset($parsed_url['host'])     ? $parsed_url['host']            : '';
    $port     = isset($parsed_url['port'])     ? ':' . $parsed_url['port']      : '';
    $user     = isset($parsed_url['user'])     ? $parsed_url['user']            : '';
    $pass     = isset($parsed_url['pass'])     ? ':' . $parsed_url['pass']      : '';
    $pass     = ($user || $pass)               ? "$pass@"                       : '';
    $path     = isset($parsed_url['path'])     ? $parsed_url['path']            : '';
    $query    = isset($parsed_url['query'])    ? '?' . $parsed_url['query']     : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment']  : '';

    return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
}