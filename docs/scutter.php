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

function fetch(XML_GRDDL_Driver $grddl, $urls, $profiles = array()) {

    $results = array();
    foreach ($urls as $n => $url) {
        $data = $grddl->fetch($url);

        $data = $grddl->appendProfiles($data, isset($profiles[$url]) ? $profiles[$url] : array());

        $stylesheets = $grddl->inspect($data, $url);

        $rdf_xml = array();
        foreach ($stylesheets as $stylesheet) {
            $rdf_xml[] = $grddl->transform($stylesheet, $data);
        }

        $results[$url] = array_reduce($rdf_xml, array($grddl, 'merge'));
    }

    return $results;
}

function render($url, $rdf_xml) {
    ob_start();
    ?>
    <h2>Results for <?php print $url; ?></h2>
    <p>You can submit this to the W3C RDF Validator</p>
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
    <?php
    return ob_get_clean();
}

$html = '';
if (!empty($_GET['action'])) {
    if ($_GET['action'] == 'Fetch') {
        $urls = array();

        if (empty($_GET['url'])) {
            die("You must pass in a URL");
        }

        $urls[] = $_GET['url'];
        $profiles = array();

        if (!empty($_GET['profiles']) && is_array($_GET['profiles'])) {
            $profiles = $_GET['profiles'];
        }

        $results = fetch($grddl, $urls, $profiles);


        foreach ($results as $url => $rdf_xml) {
            $html .= render($url, $rdf_xml);
        }
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

        $results = fetch($grddl, $urls, $profiles);

        //Extract to a $_GET
        $reduce = true;

        if (!$reduce) {
            foreach ($results as $url => $rdf_xml) {
                $html .= render($url, $rdf_xml);
            }
        } else {
            $rdf_xml = array_reduce(array_values($results), array($grddl, 'merge'));

            $html = render('All', $rdf_xml);
        }


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
        </style>
    </head>

    <body>
        <h1>Microformats to RDF</h1>
        <p>This is a simple tool to fetch information available via GRDDL. This includes microformatted data from a number of sites.</p>
        <h2>See it in action</h2>
        <p>You can see a <a href="?action=Demo">Demonstration</a>, or try it out yourself.</p>

        <form method="get" action="#results">
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
                We'll find:
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" checked="checked" name="profiles[]" value="http://www.w3.org/2002/12/cal/cardcaletc" /> Look for hcards, hcalendar
                        </label>
                    </li>
                    <li>
                        <abbr title="Gleaning Resource Descriptions from Dialects of Languages">GRDDL</abbr> transformations
                    </li>
                    <li>
                        <abbr title="Gleaning Resource Descriptions from Dialects of Languages">GRDDL</abbr> namespaceTransformations
                    </li>
                    <li>
                        <abbr title="Gleaning Resource Descriptions from Dialects of Languages">GRDDL</abbr> profileTransformations
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
