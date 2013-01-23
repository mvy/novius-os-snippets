<?php
/**
 * @copyright  Yves `M'vy` Stadler
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://os.yves-stadler.fr/
 *
 * For use with Novius-OS : https://github.com/novius-os/novius-os¬
 * http://www.novius-os.org/¬
 */

/* DOM parser that review the links inside a page content */
function get_links($wysiwyg) {
    $xml = new DOMDocument();
    /* Prefix header to set encoding */
    $xml->loadHTML('<?xml encoding="UTF-8">' . $wysiwyg);

    $links = array(
        'inner' => array(),
        'internal' => array(),
        'external' => array()
        );

    foreach($xml->getElementsByTagName('a') as $link) {
        /* Anchors have name attribute, but we need the parent to get some title
           because NOS does not allow for anchors with nodeValue */
        $name = $link->getAttribute('name');

        if(!empty($name)) {
            $parent = $link->parentNode;
            $links['inner'][] = array('text' => $parent->nodeValue, 'href' =>
                    $_SERVER["REQUEST_URI"] . "#" . $link->getAttribute('name'));
        } else {
            /* Internal links does not get the http:// prefix in NOS */
            $name = $link->getAttribute('href');

            if(substr($name, 0, 4) == "http") {
                $links['internal'][] = array('text' => $link->nodeValue, 'href'
                => $link->getAttribute('href'));
            } else {
                /* The rest is external links */
                if(!empty($name)) {
                    $links['external'][] = array('text' => $link->nodeValue,
                    'href' => $link->getAttribute('href'));
                }
            }
        }
    }

    return $links;
}?>

<? $links = get_links($wysiwyg);
/* Booleans */
$inner = count($links['inner']);
$internal = count($links['internal']);
$external = count($links['external']);

/* Display of the inner page links box */
if($inner) {
    ?>
    <div id="navigation">
    <h1>Navigation</h1>
    <ul>
        <? foreach($links['inner'] as $link) { ?>
            <li><a href="<?= $link['href'] ?>"><?= $link['text'] ?></a></li>  
        <? } ?>
    </ul>
    </div>
        <?  } 
/* Display of the internal links box */
if($internal) {
    ?>
    <div id="internal">
    <h1>Liens internes</h1>
    <ul>
        <? foreach($links['internal'] as $link) { ?>
            <li><a href="<?= $link['href'] ?>"><?= $link['text'] ?></a></li>  
        <? } ?>
    </ul>
    </div>
        <?  } 
/* Display of the external links box */
if($external) { 
        ?>
    <div id="external">
    <h1>Liens externes</h1>
    <ul>
        <?  foreach($links['external'] as $link) { ?>
            <li><a href="<?= $link['href'] ?>"><?= $link['text'] ?></a></li>  
        <?  } ?>
    </ul>
    </div>
<?  } ?>
