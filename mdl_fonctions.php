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
 * Devine la langue de l'url demandée selon le nom de domaine.
 *
 * @param String $domaine_request
 *     Le domaine dont on cherche la langue
 * @return String
 *     Le code de langue correspondant à l'url donnée, si on le trouve.
 */
function mdl_langue_url_selon_domaine ($domaine_request) {

    foreach ($GLOBALS['domaines'] as $lang => $domaine) {
        if ($domaine == $domaine_request) {
            return $lang;
        }
    }
    return NULL;
}

/**
 * si le nom de domaine de $url ne correspond pas à $lang, on le
 * remplace par le bon nom de domaine
 *
 * @param String $domaine_request
 *     Le domaine à modifier
 * @param String $lang
 *     La langue que l'on souhaite pour l'url retournée
 * @return String
 *     Une url pointant sur le contenu de l'url donnée, mais sur le
 *     nom de domaine correspondant à la langue passée en paramètre.
 */
function mdl_force_domaine_url_selon_langue ($domaine_request, $lang) {

    $langue_url = mdl_langue_url_selon_domaine($domaine_request);

    if ($langue_url !== $lang) {
        /* On remplace le nom de domaine */
        $domaine_request = str_replace(
            $GLOBALS['domaines'][$langue_url],
            $GLOBALS['domaines'][$lang],
            $domaine_request
        );
    }

    return $domaine_request;
}

/**
 * #URL_SITE_SPIP passe dans cette moulinette.
 *
 * On se sert du contexte du squelette à la compilation pour trouver
 * la langue voulue, et on retourne un version de l'url avec le nom de
 * domaine correspondant.
 */

    $url = html_entity_decode($url);

    $url = mdl_force_domaine_url_selon_langue($url, $contexte['lang']);

    $url = htmlentities($url);

    return $url;
}
