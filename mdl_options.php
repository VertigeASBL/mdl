<?php
/**
 * Options du plugin Multi-Domaines par languesau chargement
 *
 * @plugin     Multi-Domaines par langues
 * @copyright  2014
 * @author     Vertige ASBL
 * @licence    GNU/GPL
 * @package    SPIP\Mdl\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('mdl_fonctions');

/**
 * Initialisation
 */

/* On permet de prendre la main sur la configuration en définissant
   soi-même la globale dans mes_options.php. */
if ( ! isset($GLOBALS['domaines'])) {
    include_spip('inc/config');
    $GLOBALS['domaines'] = lire_config('mdl/domaines');
}

/**
 * Aiguillage
 */

$domaine_request = $_SERVER['HTTP_HOST'];

/* Si la langue est demandée explicitement, et que le nom de domaine
   ne correspond pas, on redirige sur le bon nom de domaine. */
include_spip('inc/utils');
if ($lang = _request('lang')) {

    $cible = mdl_force_domaine_url_selon_langue($domaine_request, $lang);
    if ( $cible !== $domaine_request ) {
        // Redirection sur le bon domaine
        include_spip('inc/headers');
        redirige_par_entete('//'.$cible.$_SERVER['REQUEST_URI']);
    }



/* Si la langue n'est pas demandée explicitement, on essaie de deviner
   selon l'url. */
} else {
    set_request('lang', mdl_langue_url_selon_domaine($url_requete));
}


/**
 * Traitements auto
 */

$GLOBALS['table_des_traitements']['URL_SITE_SPIP'][] = 'mdl_traitement_domaine_par_langue(%s, $Pile[0])';
