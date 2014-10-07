<?php

class eZSetState extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xmlNode )
    {
        $xmlObjectID = $xmlNode->getAttribute( 'objectID' );
        $objectID    = $this->getReferenceID( $xmlObjectID );
        $object      = eZContentObject::fetch( $objectID );

        if (!$object)
        {
            $this->writeMessage( "\tObject not found.", 'error' );
            return false;
        }

        $stateIDList = explode(",", $xmlNode->getAttribute( 'stateIDList' ));

        if (!is_array($stateIDList) || count($stateIDList) == 0)
        {
            $this->writeMessage( "\tState list not found.", 'error' );
            return false;
        }

        $opType = "";
        if ( eZOperationHandler::operationIsAvailable( 'content_updateobjectstate' ) )
        {
            $opType = "by handler";
            $operationResult = eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id'     => $objectID,
                                                                                                   'state_id_list' => $stateIDList ),
                                                            null, true );
        }
        else
        {
            $opType = "directly";
            $operationResult = eZContentOperationCollection::updateObjectState( $objectID, $stateIDList );
        }
        var_dump($operationResult);
        if (array_key_exists("status", $operationResult) && $operationResult["status"])
        {
            $this->writeMessage( "\t\tAssinged states [" . implode(", ", $stateIDList) . "] " . $opType . " to object #" . $objectID . ".", 'success' );
        }
        else
        {
            $this->writeMessage( "\t\tError while assinging states [" . implode(", ", $stateIDList) . "] " . $opType . " to object #" . $objectID . ".", 'error' );
        }
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'SetState', 'Info' => 'set object state' );
    }
}

?>
