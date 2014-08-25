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
        if (strpos($url, $domaine) === 0) {
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
        /* On remplace le nom de domaine */
        $url = str_replace($GLOBALS['domaines'][$langue_url], $GLOBALS['domaines'][$lang], $url);
        /* Du coup plus besoin de paramètre lang dans l'url */
        $url = parametre_url($url, 'lang', '');
    }

    return $url;
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
