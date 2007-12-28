<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZProccessInformation extends eZXMLInstallerHandler
{

    function eZProccessInformation( )
    {
    }

    function execute( &$xml )
    {
        $comment = $xml->getAttribute( 'comment' );
        $this->writeMessage( "Step " . $this->increaseCouter() . ": " . $comment, 'notice' );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'ProccessInformation', 'Info' => 'Write info about next step.' );
    }
}

?>