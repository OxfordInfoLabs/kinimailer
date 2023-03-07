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
    private $xPathSelector;

    /**
     * @param string $externalURL
     * @param string $externalXPath
     */
    public function __construct($externalURL = null, $externalXPath = null) {
        $this->externalURL = $externalURL;
        $this->xPathSelector = $externalXPath;
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
    public function getXPathSelector() {
        return $this->xPathSelector;
    }

    /**
     * @param string $xPathSelector
     */
    public function setXPathSelector($xPathSelector) {
        $this->xPathSelector = $xPathSelector;
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

        if ($html && $this->xPathSelector) {
            $dom = new \DOMDocument();
            $dom->loadHTML($html, LIBXML_NOERROR);
            $xPath = new \DOMXPath($dom);
            $xPathResult = $xPath->query($this->xPathSelector);
            $html = $dom->saveHTML($xPathResult->item(0));
        }

        return $html;
    }
}
