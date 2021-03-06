#!/usr/bin/php
<?php
/* $id$
 *
 * noark5-export
 *
 * Copyright (C) 2017  Ole Aamot
 * Copyright (C) 2017  Thomas Sødring
 *
 * Authors: Ole Aamot <oka@oka.no>, Thomas Sødring <Thomas.Sodring@hioa.no>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once "controller/LoginController.php";
require_once "controller/NikitaEntityController.php";
require_once "controller/NoarkObjectCreator.php";

function getHrefAssociatedWithRel($rel, $links)
{
    if ($links != null && is_array($links))
        foreach ($links[Constants::LINKS] as $item) {
            if (is_array($item)) {
                if ($item[Constants::REL] === $rel) {
                    return $item[Constants::HREF];
                }
            }
        }
    return false;
}

function printSuccess($type)
{
    echo "Successfully created an object of type (" . $type . ")" . PHP_EOL;
}

function printError($type, $information, $status, $description)
{
    echo "Could not create an object of type (" . $type . "). Reason (" . $information . ")";
    if (isset($status) && isset($description)) {
        echo "nikita returned status (" . $status . "), description (" . $description . ")";
    }
    echo PHP_EOL;
}

function processFolder($controller, $mappe, $token)
{
    global $xml;
    printSuccess("mappe");
    $xml->startElement('mappe');
    $xml->WriteAttribute('xsi:type', 'saksmappe');
    if (isset($mappe['systemID'])) {
        $xml->startElement('systemID');
        $xml->text($mappe['systemID']);
        $xml->endElement();
    }
    if (isset($mappe['mappeID'])) {
        $xml->startElement('mappeID');
        $xml->text($mappe['mappeID']);
        $xml->endElement();
    }
    if (isset($mappe['tittel'])) {
        $xml->startElement('tittel');
        $xml->text($mappe['tittel']);
        $xml->endElement();
    }
    if (isset($mappe['beskrivelse'])) {
        $xml->startElement('beskrivelse');
        $xml->text($mappe['beskrivelse']);
        $xml->endElement();
    }
    if (isset($mappe['opprettetDato'])) {
        $xml->startElement('opprettetDato');
        $xml->text($mappe['opprettetDato']);
        $xml->endElement();
    }
    if (isset($mappe['opprettetAv'])) {
        $xml->startElement('opprettetAv');
        $xml->text($mappe['opprettetAv']);
        $xml->endElement();
    }
    if (isset($mappe['avsluttetDato'])) {
        $xml->startElement('avsluttetDato');
        $xml->text($mappe['avsluttetDato']);
        $xml->endElement();
    }
    if (isset($mappe['avsluttetAv'])) {
        $xml->startElement('avsluttetAv');
        $xml->text($mappe['avsluttetAv']);
        $xml->endElement();
    }
    if (isset($mappe['registrering'])) {
        $urlGetRegistration = getHrefAssociatedWithRel(Constants::REL_ARKIVSTRUKTUR_REGISTRERING, $mappe);
        $registrationController = new NikitaEntityController($token);
        $registrationResults = $registrationController->getData($urlGetRegistration);
        if ($registrationResults) {
            // An mappe object can have multiple registrering objects
            if (isset($registrationResults['results'])) {
                foreach ($registrationResults['results'] as $registration) {
                    processRegistration($controller, $registration, $token);
                }
            }
        }
        /* Implement registrering -> korrespondansepart */
    }
    if (isset($mappe['saksaar'])) {
        $xml->startElement('saksaar');
        $xml->text($mappe['saksaar']);
        $xml->endElement();
    }
    if (isset($mappe['sakssekvensnummer'])) {
        $xml->startElement('sakssekvensnummer');
        $xml->text($mappe['sakssekvensnummer']);
        $xml->endElement();
    }
    if (isset($mappe['saksdato'])) {
        $xml->startElement('saksdato');
        $xml->text($mappe['saksdato']);
        $xml->endElement();
    }
    if (isset($mappe['administrativEnhet'])) {
        $xml->startElement('administrativEnhet');
        $xml->text($mappe['administrativEnhet']);
        $xml->endElement();
    }
    if (isset($mappe['saksansvarlig'])) {
        $xml->startElement('saksansvarlig');
        $xml->text($mappe['saksansvarlig']);
        $xml->endElement();
    }
    if (isset($mappe['saksstatus'])) {
        $xml->startElement('saksstatus');
        $xml->text($mappe['saksstatus']);
        $xml->endElement();
    }
    $xml->endElement();
}

function processFondsCreator($controller, $arkivskaper, $token)
{
    global $xml;
    printSuccess("arkivskaper");

    $xml->startElement('arkivskaper');

    if (isset($arkivskaper['arkivskaperID'])) {
        $xml->startElement('arkivskaperID');
        $xml->text($arkivskaper['arkivskaperID']);
        $xml->endElement();
    }

    if (isset($arkivskaper['arkivskaperNavn'])) {
        $xml->startElement('arkivskaperNavn');
        $xml->text($arkivskaper['arkivskaperNavn']);
        $xml->endElement();
    }

    if (isset($arkivskaper['beskrivelse'])) {
        $xml->startElement('beskrivelse');
        $xml->text($arkivskaper['beskrivelse']);
        $xml->endElement();
    }

    $xml->endElement();
}

function processSeries($controller, $arkivdel, $token)
{

    /**
     * Først tar du all simpleType elementer
     */
    global $xml;

    $xml->startElement('arkivdel');

    if (isset($arkivdel['systemID'])) {
        $xml->startElement('systemID');
        $xml->text($arkivdel['systemID']);
        $xml->endElement();
    }
    if (isset($arkivdel['tittel'])) {
        $xml->startElement('tittel');
        $xml->text($arkivdel['tittel']);
        $xml->text('tittel');
        $xml->endElement();
    }
    if (isset($arkivdel['beskrivelse'])) {
        $xml->startElement('beskrivelse');
        $xml->text($arkivdel['beskrivelse']);
        $xml->endElement();
    }
    if (isset($arkivdel['arkivdelstatus'])) {
        $xml->startElement('arkivdelstatus');
        $xml->text($arkivdel['arkivdelstatus']);
        $xml->endElement();
    }
    if (isset($arkivdel['dokumentmedium'])) {
        $xml->startElement('dokumentmedium');
        $xml->text($arkivdel['dokumentmedium']);
        $xml->endElement();
    }
    if (isset($arkivdel['opprettetDato'])) {
        $xml->startElement('opprettetDato');
        $xml->text($arkivdel['opprettetDato']);
        $xml->endElement();
    }
    if (isset($arkivdel['opprettetAv'])) {
        $xml->startElement('opprettetAv');
        $xml->text($arkivdel['opprettetAv']);
        $xml->endElement();
    }
    if (isset($arkivdel['avsluttetDato'])) {
        $xml->startElement('avsluttetDato');
        $xml->text($arkivdel['avsluttetDato']);
        $xml->endElement();
    }
    if (isset($arkivdel['avsluttetAv'])) {
        $xml->startElement('avsluttetAv');
        $xml->text($arkivdel['avsluttetAv']);
        $xml->endElement();
    }

    /**
     * Har kan du velge hvodran du vil håndtere complexType elementer
     *
     * Her er du nædt til å forstå noark standarden og hvilken elementer det forventes
     * som kan forekomme her.
     */

    $urlGetMappe = getHrefAssociatedWithRel(Constants::REL_ARKIVSTRUKTUR_MAPPE, $arkivdel);
    $mappeController = new NikitaEntityController($token);
    $mappeResults = $mappeController->getData($urlGetMappe);

    if ($mappeResults) {
        // An arkiv object can have multiple arkivdel objects
        if (isset($mappeResults['results'])) {
            foreach ($mappeResults['results'] as $mappe) {
                processFolder($controller, $mappe, $token);
            }
        }
    }

    $xml->endElement();
}

function processRegistration($controller, $registrering, $token)
{
    global $xml;
    printSuccess("registrering");
    $xml->startElement('registrering');
    $xml->WriteAttribute('xsi:type', 'journalpost');
    if (isset($registrering['systemID'])) {
        $xml->startElement('systemID');
        $xml->text($registrering['systemID']);
        $xml->endElement();
    }
    if (isset($registrering['opprettetDato'])) {
        $xml->startElement('opprettetDato');
        $xml->text($registrering['opprettetDato']);
        $xml->endElement();
    }
    if (isset($registrering['opprettetAv'])) {
        $xml->startElement('opprettetAv');
        $xml->text($registrering['opprettetAv']);
        $xml->endElement();
    }
    if (isset($registrering['arkivertDato'])) {
        $xml->startElement('arkivertDato');
        $xml->text($registrering['arkivertDato']);
        $xml->endElement();
    }
    if (isset($registrering['arkivertAv'])) {
        $xml->startElement('arkivertAv');
        $xml->text($registrering['arkivertAv']);
        $xml->endElement();
    }
    if (isset($registrering['dokumentbeskrivelse'])) {
        $urlGetDokumentBeskrivelse = getHrefAssociatedWithRel(Constants::REL_ARKIVSTRUKTUR_DOKUMENTBESKRIVELSE, $registrering);
        $dokumentBeskrivelseController = new NikitaEntityController($token);
        $dokumentBeskrivelseResults = $mappeController->getData($urlGetDokumentBeskrivelse);
        if ($dokumentBeskrivelseResults) {
            $xml->startElement('dokumentbeskrivelse');
            foreach ($dokumentbeskrivelseData["results"] as $dokumentbeskrivelseResult) {
                $xml->startElement('systemID');
                $xml->text($dokumentbeskrivelseResults['systemID']);
                $xml->endElement();
                $xml->startElement('dokumenttype');
                $xml->text($dokumentbeskrivelseResults['dokumenttype']);
                $xml->endElement();
                $xml->startElement('dokumentstatus');
                $xml->text($dokumentbeskrivelseResults['dokumentstatus']);
                $xml->endElement();
                $xml->startElement('tittel');
                $xml->text($dokumentbeskrivelseResults['tittel']);
                $xml->endElement();
                $xml->startElement('beskrivelse');
                $xml->text($dokumentbeskrivelseResults['beskrivelse']);
                $xml->endElement();
                $xml->startElement('forfatter');
                $xml->text($dokumentbeskrivelseResults['forfatter']);
                $xml->endElement();
                $xml->startElement('opprettetDato');
                $xml->text($dokumentbeskrivelseResults['opprettetDato']);
                $xml->endElement();
                $xml->startElement('opprettetAv');
                $xml->text($dokumentbeskrivelseResults['opprettetAv']);
                $xml->endElement();
                $xml->startElement('dokumentmedium');
                $xml->text($dokumentbeskrivelseResults['dokumentmedium']);
                $xml->endElement();
                $xml->startElement('tilknyttetRegistreringSom');
                $xml->text($dokumentbeskrivelseResults['tilknyttetRegistreringSom']);
                $xml->endElement();
                $xml->startElement('dokumentnummer');
                $xml->text($dokumentbeskrivelseResults['dokumentnummer']);
                $xml->endElement();
                $urldokumentobjektData = $dokumentbeskrivelseController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_REGISTRERING);
                $dokumentobjektController = new NikitaEntityController($token);
                $dokumentobjektData = $dokumentobjektController->getData($urldokumentobjektData);
                $xml->startElement('dokumentobjekt');
                foreach ($dokumentobjektData["results"] as $dokumentobjektResult) {
                    $xml->startElement('versjonsnummer');
                    $xml->text($dokumentobjektResults['versjonsnummer']);
                    $xml->endElement();
                    $xml->startElement('variantformat');
                    $xml->text($dokumentobjektResults['variantformat']);
                    $xml->endElement();
                    $xml->startElement('format');
                    $xml->text($dokumentobjektResults['format']);
                    $xml->endElement();
                    $xml->startElement('opprettetDato');
                    $xml->text($dokumentobjektResults['opprettetDato']);
                    $xml->endElement();
                    $xml->startElement('opprettetAv');
                    $xml->text($dokumentobjektResults['opprettetAv']);
                    $xml->endElement();
                    $xml->startElement('referanseDokumentfil');
                    $xml->text($dokumentobjektResults['referanseDokumentfil']);
                    $xml->endElement();
                    $xml->startElement('sjekksum');
                    $xml->text($dokumentobjektResults['sjekksum']);
                    $xml->endElement();
                    $xml->startElement('sjekksumAlgoritme');
                    $xml->text($dokumentobjektResults['sjekksumAlgoritme']);
                    $xml->endElement();
                    $xml->startElement('filstoerrelse');
                    $xml->text($dokumentobjektResults['filstoerrelse']);
                    $xml->endElement();
                }
            }
            $xml->endElement();
        }
        // Denne tilnærming her funker dårlig ....
        // Det er ikke fleksibel .. Noark er ikke en statisk struktur og da trenger du metode kall
        // sånn som i andre steder ...
        $xml->endElement();
    }
    $xml->endElement();
}

function processFonds($controller, $arkiv, $isRoot, $token)
{
    global $xml;
    $xml->startElement('arkiv');

    if ($isRoot) {
        $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->writeAttribute('xmlns', 'http://www.arkivverket.no/standarder/noark5/arkivstruktur');
        $xml->writeAttribute('xmlns:n5mdk', 'http://www.arkivverket.no/standarder/noark5/metadatakatalog');
        $xml->writeAttribute('xsi:schemaLocation', 'http://www.arkivverket.no/standarder/noark5/arkivstruktur arkivstruktur.xsd');
    }

    if (isset($arkiv["systemID"])) {
        $xml->startElement('systemID');
        $xml->text($arkiv["systemID"]);
        $xml->endElement();
    }

    if (isset($arkiv["tittel"])) {
        $xml->startElement('tittel');
        $xml->text($arkiv["tittel"]);
        $xml->endElement();
    }
    if (isset($arkiv["arkivstatus"])) {
        $xml->startElement('beskrivelse');
        $xml->text($arkiv["beskrivelse"]);
        $xml->endElement();
    }
    if (isset($arkiv["arkivstatus"])) {
        $xml->startElement('arkivstatus');
        $xml->text($arkiv["arkivstatus"]);
        $xml->endElement();
    }
    if (isset($arkiv["dokumentmedium"])) {
        $xml->startElement('dokumentmedium');
        $xml->text($arkiv["dokumentmedium"]);
        $xml->endElement();
    }
    if (isset($arkiv["opprettetDato"])) {
        $xml->startElement('opprettetDato');
        $xml->text($arkiv["opprettetDato"]);
        $xml->endElement();
    }
    if (isset($arkiv["opprettetAv"])) {
        $xml->startElement('opprettetAv');
        $xml->text($arkiv["opprettetAv"]);
        $xml->endElement();
    }
    if (isset($arkiv["avsluttetDato"])) {
        $xml->startElement('avsluttetDato');
        $xml->text($arkiv["avsluttetDato"]);
        $xml->endElement();
    }
    if (isset($arkiv["avsluttetAv"])) {
        $xml->startElement('avsluttetAv');
        $xml->text($arkiv["avsluttetAv"]);
        $xml->endElement();
    }

    $urlGetArkivskaper = getHrefAssociatedWithRel(Constants::REL_ARKIVSTRUKTUR_ARKIVSKAPER, $arkiv);
    $arkivskaperController = new NikitaEntityController($token);
    $arkivskaperResults = $arkivskaperController->getData($urlGetArkivskaper);

    if ($arkivskaperResults) {
        foreach ($arkivskaperResults as $arkivskaper) {
            processFondsCreator($controller, $arkivskaper, $token);
        }
    }
    //$links = (array) $arkiv['_links'];
    $urlGetArkivdel = getHrefAssociatedWithRel(Constants::REL_ARKIVSTRUKTUR_ARKIVDEL, $arkiv);
    $arkivdelController = new NikitaEntityController($token);
    $arkivdelResults = $arkivdelController->getData($urlGetArkivdel);

    if ($arkivdelResults) {
        // An arkiv object can have multiple arkivdel objects
        if (isset($arkivdelResults['results'])) {
            foreach ($arkivdelResults['results'] as $arkivdel) {
                processSeries($controller, $arkivdel, $token);
            }
        }
    }

    $xml->endElement();
}

if ($argc > 4) {

    $baseurl = $argv[2];
    $user = $argv[3];
    $pass = $argv[4];
    $data = array("username" => $user, "password" => $pass);
    $data_string = json_encode($data);
    $loginController = new LoginController($baseurl);
    $token = $loginController->login($user, $pass);
    if (!isset($token)) {
        echo "Could not login into nikita ... exiting\n";
        exit(1);
    }
    echo "Successfully logged onto nikita. Token is " . $token . "\n";
    $applicationController = new NikitaEntityController($token);
    $applicationData = $applicationController->getData($baseurl);

    $urlArkivstruktur = $applicationController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR);
    $arkivstrukturController = new NikitaEntityController($token);
    $arkivstrukturData = $arkivstrukturController->getData($urlArkivstruktur);

    $urlArkiv = $arkivstrukturController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_ARKIV);
    $arkivController = new NikitaEntityController($token);
    $arkivDataResults = $arkivController->getData($urlArkiv);

    if (isset($arkivDataResults['results'])) {
        $count = 0;
        foreach ($arkivDataResults['results'] as $arkiv) {


            $xml = new XMLWriter();
            $xml->openURI("attempt " . $count++ . "-" . $argv[1]);
            $xml->setIndent(true);
            $xml->startDocument('1.0', 'UTF-8');

            processFonds($arkivController, $arkiv, true, $token);

            $xml->flush();
            $xml->endDocument();
        }
    }

    exit(0);
} else {
    echo "noark5-export.php FILE BASEURL USER PASS\n";
    exit(0);
}
?>
