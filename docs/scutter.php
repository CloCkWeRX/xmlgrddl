<?php
require_once 'XML/GRDDL.php';
require_once 'Log.php';

$log = &Log::singleton('firebug', '', 'PHP',
                       array('buffering' => false),
                       PEAR_LOG_DEBUG);

$options = array('tidy' => true,
                'prettify' => true,
                'quiet' => true,
                'formatOutput' => true,
                'documentTransformations' => true,
                'namespaceTransformations' => true,
                'htmlTransformations' => true,
                'htmlProfileTransformations' => true,
                'log' => $log);

$grddl = XML_GRDDL::factory('xsl', $options);

function fetch(XML_GRDDL_Driver $grddl, $url, $profiles = array()) {
    list($stylesheets, $data) = fetch_stylesheets($grddl, $url, $profiles);

    $rdf_xml = array();
    foreach ($stylesheets as $stylesheet) {
        $rdf_xml[] = $grddl->transform($stylesheet, $data);
    }

    return array_reduce($rdf_xml, array($grddl, 'merge'));
}

function fetch_stylesheets(XML_GRDDL_Driver $grddl, $url, $profiles = array()) {
    $data = $grddl->fetch($url);

    $data = $grddl->appendProfiles($data, isset($profiles) ? $profiles : array());

    $stylesheets = $grddl->inspect($data, $url);

    return array($stylesheets, $data);
}

function render($url, $rdf_xml, $stylesheets = array(), $data = array()) {
    ob_start();
    ?>
    <h2>Results for <a href="<?php print $url; ?>"><?php print $url; ?></a></h2>

    <p>You can submit this to the W3C RDF Validator, or compare it with <a href="http://triplr.org/rdf/<?php print $url; ?>" target="_blank">triplr</a></p>
    <form method="post" target="_blank" action="http://www.w3.org/RDF/Validator/ARPServlet#graph" id="myform_direct">
        <p>
            <textarea rows="20" cols="100" name="RDF"><?php print htmlspecialchars($rdf_xml); ?></textarea>
        </p>
        <p>
            <input value="Parse RDF" name="PARSE" type="submit" />
        </p>

        <input type="hidden" name="TRIPLES_AND_GRAPH" value="PRINT_BOTH" />
        <input type="hidden" name="FORMAT" value="PNG_EMBED" />
    </form>

    <?php if (!empty($stylesheets)) { ?>
        <h3>Stylesheets used</h3>
        <pre>
            <?php print_r($stylesheets); ?>
        </pre>
    <?php } ?>

    <?php if (!empty($data)) { ?>
    <h3>Data used</h3>
    <pre>
        <?php print htmlspecialchars($data); ?>
    </pre>
    <?php } ?>
    <?php
    return ob_get_clean();
}

$html = '';
if (!empty($_GET['action'])) {
    if ($_GET['action'] == 'Fetch') {
        if (empty($_GET['url'])) {
            die("You must pass in a URL");
        }

        $url = $_GET['url'];

        $source = !empty($_GET['source']);

        $profiles = array();

        if (!empty($_GET['profiles']) && is_array($_GET['profiles'])) {
            $profiles = $_GET['profiles'];
        }


        $rdf_xml = fetch($grddl, $url, $profiles);

        $stylesheets = array();
        $data = "";

        if ($source) {
            list($stylesheets, $data) = fetch_stylesheets($grddl, $url, $profiles);
        }

        $html = render($url, $rdf_xml, $stylesheets, $data);

    } elseif ($_GET['action'] == 'Demo') {

        $urls = array();
        $urls[0] = 'http://clockwerx.blogspot.com/';
        $urls[1] = 'http://www.linkedin.com/in/clockwerx';
        $urls[2] = 'http://www.last.fm/user/CloCkWeRX/';
        $urls[3] = 'http://flickr.com/people/clockwerx/';
        $urls[4] = 'http://upcoming.yahoo.com/user/8722/';

        $profiles[$urls[0]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
        $profiles[$urls[1]][] = 'http://microformats.org/wiki/hresume-profile';
        $profiles[$urls[1]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
        $profiles[$urls[2]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
        $profiles[$urls[3]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
        $profiles[$urls[4]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';

        $results = array();
        foreach ($urls as $url) {
            $results[] = fetch($grddl, $url, $profiles[$url]);
        }

        $rdf_xml = array_reduce(array_values($results), array($grddl, 'merge'));
        $html = render('All', $rdf_xml);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Microformats to RDF</title>
        <style type="text/css">

            form {
                max-width: 45em;
                margin: 1.0em;
            }
            input[name="url"] {
                display: block;
                font-size: 2.0em;
                width: 100%;
            }

            input[type=submit] {
                float: right;
            }

            .hint, .options {
                float: left;
                margin: 0.5em;
                background: rgb(240, 240, 240);
                padding: 1.0em;
                max-width: 21em;
            }

            body {
                font-family: Arial;
            }

            .navigation {
                display: inline;
                position: absolute;
                top: 0;
                right: 1.0em;
                list-style: none;
            }
        </style>
    </head>

    <body>
        <h1>Microformats to RDF</h1>
        <p>This is a simple tool to fetch information available via <abbr title="Gleaning Resource Descriptions from Dialects of Languages">GRDDL</abbr>. This includes microformatted data from a number of sites. It is powered by <a href="http://pear.php.net/package/XML_GRDDL/">XML_GRDDL</a>.</p>
        <h2>See it in action</h2>
        <p>You can see a <a href="?action=Demo">demonstration</a>, or try it out yourself.</p>

        <form method="get">
            <h3>Your URL</h3>
            <p><label>A URL about you: <input type="text" name="url" value="http://clockwerx.blogspot.com/"  /></label><input type="submit" name="action" value="Fetch" /></p>

            <br style="clear: both" />
            <div class="hint">You can try any of the sites with microformats
                <ul>
                    <li>http://flickr.com/people/username</li>
                    <li>http://www.linkedin.com/in/username</li>
                    <li>http://www.last.fm/users/username</li>
                    <li>http://upcoming.yahoo.com/user/id/</li>
                </ul>
            </div>
            <div class="options">
                Options:
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" checked="checked" name="profiles[]" value="http://www.w3.org/2002/12/cal/cardcaletc" /> Look for hcards, hcalendar
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" checked="checked" name="profiles[]" value="http://xmlgrddl.googlecode.com/svn/trunk/data/grddl-library/grokAlternate" /> Look for //link[@rel=meta]
                        </label>
                    </li>
                    <li>
                        <abbr title="Gleaning Resource Descriptions from Dialects of Languages">GRDDL</abbr> transformations
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" checked="<?php print $source? "checked" : ""; ?>"  name="source" value="1" /> Show source
                        </label>
                    </li>
                </ul>
            </div>
        </form>
        <br style="clear: both" />
        <div id="results">
            <?php print $html; ?>
        </div>
        <ul class="navigation">
            <li><a href="?">Home</a></li>
            <li><a href="?action=Demo">Demo</a></li>
        </ul>
    </body>
</html>
