<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZHideUnhide extends eZXMLInstallerHandler
{

    function eZHideUnhide( )
    {
    }

    function execute( $xmlNode )
    {
        $action = $xmlNode->attributeValue( 'action' );
        $xmlNodeID = $xmlNode->attributeValue( 'nodeID' );

        $nodeID = $this->getReferenceID( $xmlNodeID );
        if ( !$nodeID )
        {
            $this->writeMessage( "\tInvalid node $nodeID does not exist.", 'warning' );
            return false;
        }

        if ( !$action )
        {
            $action = 'toogle';
        }
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node )
        {
            $this->writeMessage( "\tNo node defined.", 'error' );
            return false;
        }

        switch ( $action )
        {
            case 'unhide':
            {
                if ( $node->attribute( 'is_hidden' ) )
                {
                    eZContentObjectTreeNode::unhideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be unhidden.", 'error' );
                }
            } break;
            case 'hide':
            {
                if ( !$node->attribute( 'is_hidden' ) )
                {
                    eZContentObjectTreeNode::hideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be hidden.", 'error' );
                }
            } break;
            case 'toogle':
            {
                if ( $node->attribute( 'is_hidden' ) )
                {
                    eZContentObjectTreeNode::unhideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be unhidden.", 'error' );
                }
                else
                {
                    eZContentObjectTreeNode::hideSubTree( $node );
                    $this->writeMessage( "\tNode " . $node->attribute( 'name' ) . " has be hidden.", 'error' );
                }
            } break;
        }
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'HideUnhide', 'Info' => 'hide/unhide subtree' );
    }
}

?>