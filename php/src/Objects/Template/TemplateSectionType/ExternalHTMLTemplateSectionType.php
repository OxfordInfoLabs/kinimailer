<?php

namespace Kinimailer\Objects\Template\TemplateSectionType;

use Kinikit\Core\Logging\Logger;

class ExternalHTMLTemplateSectionType implements TemplateSectionType {

    /**
     * @var string
     */
    private $externalURL;

    /**
     * @var string
     */
    private $externalXPath;

    /**
     * @param string $externalURL
     * @param string $externalXPath
     */
    public function __construct($externalURL = null, $externalXPath = null) {
        $this->externalURL = $externalURL;
        $this->externalXPath = $externalXPath;
    }


    /**
     * @return string
     */
    public function getExternalURL() {
        return $this->externalURL;
    }

    /**
     * @param string $externalURL
     */
    public function setExternalURL($externalURL) {
        $this->externalURL = $externalURL;
    }

    /**
     * @return string
     */
    public function getExternalXPath() {
        return $this->externalXPath;
    }

    /**
     * @param string $externalXPath
     */
    public function setExternalXPath($externalXPath) {
        $this->externalXPath = $externalXPath;
    }


    public function returnHTML() {
        $html = '';

        if ($this->externalURL) {
            try {
                $html = file_get_contents($this->externalURL);
            } catch (\Exception $e) {
                // Ignore - problem with url
            }
        }

        if ($html && $this->externalXPath) {
            $dom = new \DOMDocument();
            $dom->loadHTML($html, LIBXML_NOERROR);

            $xPath = new \DOMXPath($dom);
            $xPathResult = $xPath->query($this->externalXPath);
            $html = $dom->saveHTML($xPathResult->item(0));
        }

        return $html;
    }
}
