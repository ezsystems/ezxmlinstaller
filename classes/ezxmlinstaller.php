<?php

include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
include_once( 'kernel/classes/ezcontentclass.php' );
include_once( "lib/ezlocale/classes/ezdatetime.php" );
include_once( 'lib/ezutils/classes/ezoperationhandler.php' );

class eZXMLInstaller
{
    function eZXMLInstaller( $domDocument )
    {
        $this->rootDomNode = $domDocument->documentElement;
        $this->cli = eZCLI::instance();
    }

    function proccessXML( )
    {
        $installerHandlerManager = eZXMLInstallerHandlerManager::instance();
        $installerHandlerManager->initialize();
        if ( $this->rootDomNode &&
             $this->rootDomNode->nodeType == XML_ELEMENT_NODE &&
             $this->rootDomNode->nodeName == 'eZXMLImporter' )
        {
            if ( $this->rootDomNode->hasAttributes() )
            {
                if($this->rootDomNode->hasAttributes())
                {
                    $attributes = $this->rootDomNode->attributes;
                    if(!is_null($attributes))
                    {
                        $settings = array();
                        foreach ($attributes as $index=>$attr)
                        {
                            $settings[$attr->name] = $attr->value;
                        }
                    }
                }
                $installerHandlerManager->setSettings( $settings );
            }
            if ( $this->rootDomNode->hasChildNodes() )
            {
                $children = $this->rootDomNode->childNodes;
                foreach ( $children as $child )
                {
                    if ( $child->nodeType == XML_ELEMENT_NODE )
                    {
                        $installerHandlerManager->executeHandler( $child->nodeName, $child );
                    }
                }
            }
            else
            {
                $installerHandlerManager->writeMessage( "XML has no valid information.", 'error' );
                return false;
            }
        }
        else
        {
            $installerHandlerManager->writeMessage( "XML is not initialized.", 'error' );
            return false;
        }
        return true;
    }

    var $rootDomNode;
    var $cli;
}

?>
