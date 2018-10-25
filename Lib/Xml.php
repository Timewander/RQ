<?php

class Xml {

    public static function stringToArray($xml_sting) {

        libxml_disable_entity_loader(true);
        $xml = simplexml_load_string($xml_sting, 'SimpleXMLElement', LIBXML_NOCDATA);
        return (array) $xml;
    }
}