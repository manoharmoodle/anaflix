<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ .'/../../config.php');
require_once('reshort_form.php');
require_once('renderer.php');
global $OUTPUT, $PAGE, $CFG, $DB, $USER;
$PAGE->requires->jquery();
require_login();
echo $OUTPUT->header();
$mform = new reshort_form();
if ($mform->is_cancelled()) {
    redirect('reshort.php');
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  $data = $fromform->introduction;

    // Sample HTML content
    $html = $data['text']; // Your HTML content goes here

    // Create a new DOMDocument
    $doc = new DOMDocument();
    $doc->loadHTML($html);

    // Create a new DOMXPath
    $xpath = new DOMXPath($doc);

    // Find the first <ul> element
    $ulListall = $xpath->query('//ul');
    $arraydata = [];
    foreach ($ulListall as $ulList) {
        if ($ulList) {
            // Get the first <li> element within the <ul>
            $liElement = $ulList->getElementsByTagName('li')->item(0);
        
            // Check if an <li> element is found
            if ($liElement) {
                // Get the heading within the <li> element
                $heading = $liElement->getElementsByTagName('span')->item(0);

                // Check if a heading is found
                if ($heading) {
                    // Get the text content of the heading
                    $headingText = $heading->textContent;
                    $arraydata[] = $headingText;
                }
            }
        }
    }

    // Get all headings
    $arrayheading = [];
    $headings = $xpath->query('//p/a/b/span');
    foreach ($headings as $heading) {
        $arrayheading[] = $heading->textContent;
    }

    $excludeDomain = 'mail.google.com';

    // XPath query to select all anchor (a) elements with an "href" attribute
    $links = $xpath->query('//a[@href]');

    // Initialize an array to store the filtered links
    $filteredLinks = array();
    foreach ($links as $link) {
        // Get the "href" attribute value
        $href = $link->getAttribute('href');

        // Check if the link does not contain the excluded domain
        if (strpos($href, $excludeDomain) === false) {
            $filteredLinks[] = $href;
        }
    }

    $final = array_combine($arrayheading, $arraydata);
    $i = 0;
    foreach($final as $key => $item) {
        // Create a DOMDocument to parse the HTML
        $url = $filteredLinks[$i];

        $ch = curl_init();
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Execute the cURL session and retrieve the content
        $custhtml = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }

        // Close the cURL session
        curl_close($ch);

        if ($custhtml === false) {
            die("Failed to fetch the web page.");
        }

        $custdoc = new DOMDocument();
        @$custdoc->loadHTML($custhtml); // Use @ to suppress HTML parsing errors

        // Create a DOMXPath object to navigate through the DOM
        $custxpath = new DOMXPath($custdoc);
        $largestImage = '';
        $largestSize = 0;
        $baseurl = '';
        if (strpos($url, '//economictimes.indiatimes.com')) {
            // echo '1';
            // echo '</br>';
            $baseurl = "https://economictimes.indiatimes.com/";
            $images = $custxpath->query('//figure[@class="artImg"]/img');
        } elseif (strpos($url, '//realty.economictimes.indiatimes.com')) {
            // echo '2';
            // echo '</br>';
            $baseurl = "https://realty.economictimes.indiatimes.com/";
            $images = $custxpath->query('//figure[@class="img_container"]/amp-img');
        } elseif (strpos($url, '//www.business-standard.com')) {
            // echo '3';
            // echo '</br>';
            $baseurl = "https://www.business-standard.com/";
            $images = $custxpath->query('//div[@class="position-relative"]/img');
        } elseif (strpos($url, '//www.moneycontrol.com')) {
            // echo '4';
            // echo '</br>';
            $baseurl = "https://www.moneycontrol.com/";
            $images = $custxpath->query('//img');
        } elseif (strpos($url, '//www.bseindia.com')) {
            $largestImage = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTK5hRE-hRAEm-wcu2ZNntBjHs_4-VTlEkDZQ&usqp=CAU";
            $images=[];
        } elseif (strpos($url, '//www.thehindubusinessline.com')) {
            
            // echo '6';
            // echo '</br>';
            $baseurl = "https://www.thehindubusinessline.com/";
            $images = $custxpath->query('//source');

        } elseif (strpos($url, '//www.financialexpress.com')) {
            // echo '7';
            // echo '</br>';
            $baseurl = "https://www.financialexpress.com/";
            $images = $custxpath->query('//img[@class="attachment-post-thumbnail size-post-thumbnail wp-post-image"]');

        } elseif (strpos($url, '//www.livemint.com')) {
            // echo '8';
            // echo '</br>';
            $baseurl = "https://www.livemint.com/";
            $images = $custxpath->query('//source');
        } elseif (strpos($url, '//www.cnbctv18.com')) {
            // echo '9';
            // echo '</br>';
            $baseurl = "https://www.cnbctv18.com/";
            $images = $custxpath->query('//div[contains(@class, "narticle-photo")]/img');
        } elseif (strpos($url, '//www.equitybulls.com')) {
            // echo '10';
            // echo '</br>';
            $baseurl = "https://www.equitybulls.com/";
            $images = $custxpath->query('//img[@class="img-center"]');

        } elseif (strpos($url, '//www.timesnownews.com')) {
            // echo '11';
            // echo '</br>';
            $baseurl = "https://www.timesnownews.com/";
            $images = $custxpath->query('//img');

        }

        // Iterate through the image elements and find the largest one by analyzing their dimensions
        foreach ($images as $image) {

            if (strpos($url, '//www.livemint.com') || strpos($url, '//www.thehindubusinessline.com')) {
                $atr = 'srcset';
            } else {
                $atr = 'src';
            }
            $src = $image->getAttribute($atr);
            $size = @getimagesize($src); // Use @ to suppress errors
            
            if ($size !== false) {
                $width = $size[0];
                $height = $size[1];
                $imageSize = $width * $height;

                if ($imageSize > $largestSize) {
                    $largestSize = $imageSize;
                    $largestImage = $src;
                }
            } elseif ($size == false && $src && !strpos($src, 'ttps://') && !strpos($largestImage, 'ttps://')) {
                $largestImage = $baseurl . $src;
            }
        }
        $rec = [];
        $rec['title'] = $key;
        $rec['content'] = $item;
        $rec['imagepath'] = $largestImage;
        $rec['source'] = $filteredLinks[$i];
        $rec['timecreated'] = time();
        $rec['timemodified'] = time();
        $rec['usermodified'] = $USER->id;
        $id = $DB->insert_record('reshorts', (object)$rec, $returnid=true, $bulk=false);
        $i++;
    }
    redirect('reshort.php', "Records inserted successfully");
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  //displays the form
  $mform->display();
}

// URL of the web page
//$url = 'https://economictimes.indiatimes.com/industry/services/property-/-cstruction/bobba-group-forays-into-warehousing-with-75000-sq-ft-facility-in-bengaluru/articleshow/98215490.cms'; // Replace with the URL of the web page you want to analyze  (done)


// $url = 'https://realty.economictimes.indiatimes.com/amp/news/retail/lake-shore-india-invests-rs-415-crore-in-3-3-lakh-sq-ft-mall-in-pune/104598208'; //(done)


//  $url = 'https://www.business-standard.com/companies/news/signature-global-buys-26-acre-land-in-gurugram-to-build-housing-project-123102301191_1.html'; // done


// $url = 'https://www.moneycontrol.com/news/business/real-estate/mumbai-real-estate-update-property-registrations-cross-1-lakh-mark-in-2023-11585321.html';


//$url = 'https://www.thehindubusinessline.com/news/real-estate/indian-real-estate-funding-up-82-in-q3-2023-report/article67460792.ece'; (done)


 // $url = 'https://www.bseindia.com/xml-data/corpfiling/AttachLive/a8da0b92-f865-4077-82fe-72cf7867f421.pdf';


// $url = 'https://www.financialexpress.com/business/digital-transformation-mahindra-lifespaces-introduces-home-buying-experience-on-the-metaverse-3287608/'; (done)


// $url = 'https://www.livemint.com/industry/why-everyone-wants-a-slice-of-mumbai-realty-11697730064352.html'; (done)
//$url = 'https://www.timesnownews.com/bengaluru/phoenix-mall-of-asia-in-hebbal-bengaluru-set-to-open-on-october-27-article-104588032'; //(done)


//  $url = 'https://www.cnbctv18.com/real-estate/mahindra-lifespaces-acquires-5-38-acre-land-in-pune-upscale-wagholi-18037551.htm'; //done
// $url = 'https://www.equitybulls.com/category.php?id=338715';

// $url = $filteredLinks[$i];

// $ch = curl_init();
// // Set cURL options
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// // Execute the cURL session and retrieve the content
// $custhtml = curl_exec($ch);

// // Check for cURL errors
// if (curl_errno($ch)) {
//     echo 'cURL error: ' . curl_error($ch);
// }

// // Close the cURL session
// curl_close($ch);

// if ($custhtml === false) {
//     die("Failed to fetch the web page.");
// }

// $custdoc = new DOMDocument();
// @$custdoc->loadHTML($custhtml); // Use @ to suppress HTML parsing errors

// // Create a DOMXPath object to navigate through the DOM
// $custxpath = new DOMXPath($custdoc);
// $largestImage = '';
// $baseurl = '';
// if (strpos($url, '//economictimes.indiatimes.com')) {
//     echo '1';
//     echo '</br>';
//     $baseurl = "https://economictimes.indiatimes.com/";
//     $images = $custxpath->query('//figure[@class="artImg"]/img');
// } elseif (strpos($url, '//realty.economictimes.indiatimes.com')) {
//     echo '2';
//     echo '</br>';
//     $baseurl = "https://realty.economictimes.indiatimes.com/";
//     $images = $custxpath->query('//figure[@class="img_container"]/amp-img');
// } elseif (strpos($url, '//www.business-standard.com')) {
//     echo '3';
//     echo '</br>';
//     $baseurl = "https://www.business-standard.com/";
//     $images = $custxpath->query('//div[@class="position-relative"]/img');
// } elseif (strpos($url, '//www.moneycontrol.com')) {
//     echo '4';
//     echo '</br>';
//     $baseurl = "https://www.moneycontrol.com/";
//     $images = $custxpath->query('//img');
// } elseif (strpos($url, '//www.bseindia.com')) {
//     echo '5';
//     echo '</br>';
//     $largestImage = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTK5hRE-hRAEm-wcu2ZNntBjHs_4-VTlEkDZQ&usqp=CAU";
// } elseif (strpos($url, '//www.thehindubusinessline.com')) {
    
//     echo '6';
//     echo '</br>';
//     $baseurl = "https://www.thehindubusinessline.com/";
//     $images = $custxpath->query('//source');

// } elseif (strpos($url, '//www.financialexpress.com')) {
//     echo '7';
//     echo '</br>';
//     $baseurl = "https://www.financialexpress.com/";
//     $images = $custxpath->query('//img[@class="attachment-post-thumbnail size-post-thumbnail wp-post-image"]');

// } elseif (strpos($url, '//www.livemint.com')) {
//     echo '8';
//     echo '</br>';
//     $baseurl = "https://www.livemint.com/";
//     $images = $custxpath->query('//source');
// } elseif (strpos($url, '//www.cnbctv18.com')) {
//     echo '9';
//     echo '</br>';
//     $baseurl = "https://www.cnbctv18.com/";
//     $images = $custxpath->query('//div[contains(@class, "narticle-photo")]/img');
// } elseif (strpos($url, '//www.equitybulls.com')) {
//     echo '10';
//     echo '</br>';
//     $baseurl = "https://www.equitybulls.com/";
//     $images = $custxpath->query('//img[@class="img-center"]');

// } elseif (strpos($url, '//www.timesnownews.com')) {
//     echo '11';
//     echo '</br>';
//     $baseurl = "https://www.timesnownews.com/";
//     $images = $custxpath->query('//img');

// }


// // Iterate through the image elements and find the largest one by analyzing their dimensions
// foreach ($images as $image) {

//     if (strpos($url, '//www.livemint.com') || strpos($url, '//www.thehindubusinessline.com')) {
//         $atr = 'srcset';
//     } else {
//         $atr = 'src';
//     }
//     $src = $image->getAttribute($atr);
//     $size = @getimagesize($src); // Use @ to suppress errors

//     if ($size !== false) {
//         $width = $size[0];
//         $height = $size[1];
//         $imageSize = $width * $height;

//         if ($imageSize > $largestSize) {
//             $largestSize = $imageSize;
//             $largestImage = $src;
//         }
//     } elseif ($size == false && $src && !strpos($src, 'ttps://') && !strpos($largestImage, 'ttps://')) {
//         $largestImage = $baseurl . $src;
//     }
// }
// var_dump($largestImage);


$tabs = array();
if (is_siteadmin()) {
    $tabs[] = new tabobject('review', 'reshort.php', 'In Review', 'In Review', false);
    $tabs[] = new tabobject('published', 'reshort_pending.php', 'Published', 'Published', false);
}
$currenttab = 'review';
print_tabs(array($tabs), $currenttab);

echo render_reshorts();
echo $OUTPUT->footer();