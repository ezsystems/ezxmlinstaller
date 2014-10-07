<?php


class eZMoveNodes extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xmlNode )
    {
        $xmlTargetNodeID = $xmlNode->getAttribute( 'targetNode' );
        $targetNodeID = $this->getReferenceID( $xmlTargetNodeID );
        
        if ( !$targetNodeID )
        {
            $this->writeMessage( "\tNo target location defined.", 'error' );
            return false;
        }
        
        $targetNode =  eZContentObjectTreeNode::fetch( $targetNodeID );
        if ( !$targetNode )
        {
            $this->writeMessage( "\tTarget node not found.", 'error' );
            return false;
        }
        
        $nodeListObject = $xmlNode->getElementsByTagName( 'Nodes' )->item( 0 );
        if ( $nodeListObject )
        {
            $nodes = $nodeListObject->getElementsByTagName( 'Node' );
            foreach ( $nodes as $xmlMoveNode )
            {
                $moveXMLNodeID = $xmlMoveNode->getAttribute( 'nodeId' );
                $moveNodeID = $this->getReferenceID( $moveXMLNodeID );

                $moveXMLObjectID = $xmlMoveNode->getAttribute( 'objectId' );
                $moveObjectID = $this->getReferenceID( $moveXMLObjectID );

                $sectionXMLID = $xmlMoveNode->getAttribute( 'section' );
                $sectionID = $this->getReferenceID( $sectionXMLID );

                $startTime = microtime(true);
                $opType = "";
                if ( eZOperationHandler::operationIsAvailable( 'content_move' ) )
                {
                    $opType = "by handler";
                    $operationResult = eZOperationHandler::execute( 'content',
                                                                    'move', array( 'node_id'            => $moveNodeID,
                                                                                   'object_id'          => $moveObjectID,
                                                                                   'new_parent_node_id' => $targetNodeID ),
                                                                    null,
                                                                    true );
                }
                else
                {
                    $opType = "directly";
                    eZContentOperationCollection::moveNode( $moveNodeID, $moveObjectID, $targetNodeID );
                }
                
                if ($sectionID)
                {
                    eZContentObjectTreeNode::assignSectionToSubTree( $moveNodeID, $sectionID );
                }
                
                $endTime = microtime(true);
                $this->writeMessage( "\t\tMoved Node " . $moveNodeID . " " . $opType . " to " . $targetNodeID . " (" . round( $endTime - $startTime, 3 ) . "s).", 'notice' );
            }
        }
        unset($targetNode);
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'MoveNodes', 'Info' => 'move content to folder' );
    }
}

?>
