<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZCreateRole extends eZXMLInstallerHandler
{

    function eZCreateRole( )
    {
    }

    function execute( $xml )
    {
        $roleList = $xml->getElementsByTagName( 'Role' );
        $refArray = array();
        foreach ( $roleList as $roleNode )
        {

            $roleName              = $roleNode->getAttribute( 'name' );
            $createRoleIfNotExists = $roleNode->getAttribute( 'createRole' );
            $replacePolicies       = $roleNode->getAttribute( 'replacePolicies' );
            $referenceID           = $roleNode->getAttribute( 'referenceID' );

            $this->writeMessage( "\tRole '$roleName' will be created." , 'notice' );


            $rolePolicyList = $roleNode->getElementsByTagName( 'Policy' );
            $policyList = array();
            foreach ( $rolePolicyList as $policyNode )
            {
                $policyModule   = $policyNode->getAttribute( 'module' );
                $policyFunction = $policyNode->getAttribute( 'function' );

                $policyLimitationList = array();
                $policyLimitationNodeList = $policyNode->getElementsByTagName( 'Limitations' )->item( 0 );
                if ( $policyLimitationNodeList )
                {
                    $limitations = $policyLimitationNodeList->childNodes;
                    foreach ( $limitations as $limitation )
                    {
                        if ( $limitation->nodeType == XML_ELEMENT_NODE )
                        {
                            if ( !array_key_exists( $limitation->nodeName, $policyLimitationList ) )
                            {
                                $policyLimitationList[$limitation->nodeName] = array();
                            }
                            $policyLimitationList[$limitation->nodeName][] = $this->getReferenceID( $limitation->textContent );
                        }
                    }
                }
                $policyList[] = array( 'module'     => $policyModule,
                                       'function'   => $policyFunction,
                                       'limitation' => $policyLimitationList );
            }
            $role = eZRole::fetchByName( $roleName );
            if( is_object( $role ) || ( $createRoleIfNotExists == "true" ) )
            {
                if( !is_object( $role ) )
                {
                    $role = eZRole::create( $roleName );
                    $role->store();
                }

                $roleID = $role->attribute( 'id' );
                if( count( $policyList ) > 0 )
                {
                    if ( $replacePolicies == "true" )
                    {
                        $role->removePolicies();
                        $role->store();
                    }
                    foreach( $policyList as $policyDefinition )
                    {
                        if( isset( $policyDefinition['limitation'] ) )
                        {
                            $role->appendPolicy( $policyDefinition['module'], $policyDefinition['function'], $policyDefinition['limitation'] );
                        }
                        else
                        {
                            $role->appendPolicy( $policyDefinition['module'], $policyDefinition['function'] );
                        }
                    }
                }

                if ( $referenceID )
                {
                    $refArray[$referenceID] = $role->attribute( 'id' );
                }
            }
            else
            {
                $this->writeMessage( "\tRole '$roleName' doesn't exist." , 'notice' );
            }
        }
        $this->addReference( $refArray );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'CreateRole', 'Info' => 'create role' );
    }
}

?>