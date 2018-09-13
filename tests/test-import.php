<?php
/**
 * Class ImportTest
 *
 * @package Sil_Dictionary_Webonary
 */

/**
 * Sample test case.
 */
class ImportTest extends WP_UnitTestCase {

	function test_convert_fieldworks_audio_to_wordpress() {

		$entry = '<div xmlns="http://www.w3.org/1999/xhtml" class="entry" id="g033a9b25-9bfa-4bf6-b87c-7134139e02d1"><span class="mainheadword"><span lang="tsi"><a href="#g033a9b25-9bfa-4bf6-b87c-7134139e02d1">a dm</a></span></span><span class="pronunciations"><span class="pronunciation"><span class="mediafiles"><span class="mediafile"><audio id="gad3db6b6-5a24-4786-a4e0-183347f300c1"><source src="AudioVisual\a_a-dm-ac-ps-01.mp3" /></audio><a class="CmFile" href="#gad3db6b6-5a24-4786-a4e0-183347f300c1" onclick="document.getElementById(\'gad3db6b6-5a24-4786-a4e0-183347f300c1\').play()">🔊</a></span></span></span></span><span class="senses"><span class="sensecontent"><span class="sense" entryguid="g033a9b25-9bfa-4bf6-b87c-7134139e02d1"><span class="morphosyntaxanalysis"><span class="partofspeech"><span lang="en">CL-INIT subordinating conjunction</span></span></span><span class="definitionorgloss"><span lang="en">in order to; so that</span></span><span class="examplescontents"><span class="examplescontent"><span class="example"><span lang="tsi"> ...a dm wil amukst.</span></span><span class="example_1"><span><audio id="ga_a-dm-ac-ps-01"><source src="AudioVisual\a_a-dm-ac-ps-01.mp3" /></audio><a class="tsi-Zxxx-x-audio" href="#ga_a-dm-ac-ps-01" onclick="document.getElementById(\'ga_a-dm-ac-ps-01\').play()"></a></span></span><span class="translationcontents"><span class="translationcontent"><span class="translation"><span lang="en">...so that he/she would listen.</span></span></span></span></span></span><span class="semanticdomains"><span class="semanticdomain"><span class="abbreviation"><span lang="en">9.6.2.7</span></span><span class="name"><span lang="en">Purpose </span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">9.6.2.5</span></span><span class="name"><span lang="en">Cause</span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">Ts-Y3</span></span><span class="name"><span lang="en">Seriation</span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">3.3.1.1</span></span><span class="name"><span lang="en">Purpose, goal</span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">Ts-Y2</span></span><span class="name"><span lang="en">Relative time, duration</span></span></span></span></span></span></span></div>';

		$doc = new DomDocument();
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($entry);

		$import = new sil_pathway_xhtml_Import();
		$import->dom_xpath = new DOMXPath($doc);
		$import->dom_xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

		$converted = $import->convert_fieldworks_audio_to_wordpress($doc)->saveXML();
		$converted = preg_replace( "/\r|\n/", "", $converted );

		$expected = '<?xml version="1.0"?><div xmlns="http://www.w3.org/1999/xhtml" class="entry" id="g033a9b25-9bfa-4bf6-b87c-7134139e02d1"><span class="mainheadword"><span lang="tsi"><a href="#g033a9b25-9bfa-4bf6-b87c-7134139e02d1">a dm</a></span></span><span class="pronunciations"><span class="pronunciation"><span class="mediafiles"><span class="mediafile"><audio id="gad3db6b6-5a24-4786-a4e0-183347f300c1"><source src="http://example.org/wp-content/uploads/AudioVisual/a_a-dm-ac-ps-01.mp3"/></audio><a class="CmFile" href="#gad3db6b6-5a24-4786-a4e0-183347f300c1" onclick="document.getElementById(\'gad3db6b6-5a24-4786-a4e0-183347f300c1\').play()">&#x1F50A;</a></span></span></span></span><span class="senses"><span class="sensecontent"><span class="sense" entryguid="g033a9b25-9bfa-4bf6-b87c-7134139e02d1"><span class="morphosyntaxanalysis"><span class="partofspeech"><span lang="en">CL-INIT subordinating conjunction</span></span></span><span class="definitionorgloss"><span lang="en">in order to; so that</span></span><span class="examplescontents"><span class="examplescontent"><span class="example"><span lang="tsi"> ...a dm wil amukst.</span></span><span class="example_1"><span><audio id="ga_a-dm-ac-ps-01"><source src="http://example.org/wp-content/uploads/AudioVisual/a_a-dm-ac-ps-01.mp3"/></audio><a class="tsi-Zxxx-x-audio" href="#ga_a-dm-ac-ps-01" onclick="document.getElementById(\'ga_a-dm-ac-ps-01\').play()"/></span></span><span class="translationcontents"><span class="translationcontent"><span class="translation"><span lang="en">...so that he/she would listen.</span></span></span></span></span></span><span class="semanticdomains"><span class="semanticdomain"><span class="abbreviation"><span lang="en">9.6.2.7</span></span><span class="name"><span lang="en">Purpose </span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">9.6.2.5</span></span><span class="name"><span lang="en">Cause</span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">Ts-Y3</span></span><span class="name"><span lang="en">Seriation</span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">3.3.1.1</span></span><span class="name"><span lang="en">Purpose, goal</span></span></span><span class="semanticdomain"><span class="abbreviation"><span lang="en">Ts-Y2</span></span><span class="name"><span lang="en">Relative time, duration</span></span></span></span></span></span></span></div>';
		$this->assertEquals($converted, $expected);
	}

	function test_convert_fieldworks_images_to_wordpress()
	{
		$entry = '<div  xmlns="http://www.w3.org/1999/xhtml" class="entry" id="g05e110fb-fb35-42e5-bd3d-7ae9a7d03989"> <span class="mainheadword"> <span lang="txo-Qaaa-x-Toto"> <a href="#g05e110fb-fb35-42e5-bd3d-7ae9a7d03989">abc</a> </span> <span lang="txo-Latn-fonipa-x-emic"> <a href="#g05e110fb-fb35-42e5-bd3d-7ae9a7d03989">dingbako layrung</a> </span> <span lang="txo-Beng-fonipa-x-emic"> <a href="#g05e110fb-fb35-42e5-bd3d-7ae9a7d03989">দিংবাকো লায়রুং</a> </span> </span> <span class="senses"> <span class="sharedgrammaticalinfo"> <span class="morphosyntaxanalysis"> <span class="partofspeech"> <span lang="en">n</span> </span> </span> </span> <span class="sensecontent"> <span class="sense" entryguid="g05e110fb-fb35-42e5-bd3d-7ae9a7d03989"> <span class="gloss"> <span lang="en">dragonfly</span> </span> </span> </span> </span> <span class="pictures"> <div class="picture"> <img class="thumbnail" src="pictures/AOR_4-3r.png" id="gf2e6b06b-7433-41b5-a071-38af8b37fd83"/> </div> </span> </div>';

		$doc = new DomDocument();
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($entry);

		$import = new sil_pathway_xhtml_Import();
		$import->dom_xpath = new DOMXPath($doc);
		$import->dom_xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

		$converted = $import->convert_fieldworks_images_to_wordpress($doc)->saveXML();
		$converted = preg_replace( "/\r|\n/", "", $converted );
		$expected = '<?xml version="1.0"?><div xmlns="http://www.w3.org/1999/xhtml" class="entry" id="g05e110fb-fb35-42e5-bd3d-7ae9a7d03989"><span class="mainheadword"><span lang="txo-Qaaa-x-Toto"><a href="#g05e110fb-fb35-42e5-bd3d-7ae9a7d03989">abc</a></span><span lang="txo-Latn-fonipa-x-emic"><a href="#g05e110fb-fb35-42e5-bd3d-7ae9a7d03989">dingbako layrung</a></span><span lang="txo-Beng-fonipa-x-emic"><a href="#g05e110fb-fb35-42e5-bd3d-7ae9a7d03989">&#x9A6;&#x9BF;&#x982;&#x9AC;&#x9BE;&#x995;&#x9CB; &#x9B2;&#x9BE;&#x9AF;&#x9BC;&#x9B0;&#x9C1;&#x982;</a></span></span><span class="senses"><span class="sharedgrammaticalinfo"><span class="morphosyntaxanalysis"><span class="partofspeech"><span lang="en">n</span></span></span></span><span class="sensecontent"><span class="sense" entryguid="g05e110fb-fb35-42e5-bd3d-7ae9a7d03989"><span class="gloss"><span lang="en">dragonfly</span></span></span></span></span><span class="pictures"><div class="picture"><a class="image" href="http://example.org/wp-content/uploads/images/original/AOR_4-3r.png"><img src="http://example.org/wp-content/uploads/images/thumbnail/AOR_4-3r.png"/></a></div></span></div>';

		$this->assertEquals($converted, $expected);
	}

}
