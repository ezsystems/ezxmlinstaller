<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZAssignRoles extends eZXMLInstallerHandler
{

    function eZAssignRoles( )
    {
    }

    function execute( $xml )
    {
        include_once( 'kernel/classes/ezrole.php' );
        $assignmentList = $xml->getElementsByTagName( 'RoleAssignment' );
        foreach ( $assignmentList as $roleAssignment )
        {
            $roleID            = $this->getReferenceID( $roleAssignment->getAttribute( 'roleID' ) );
            $assignTo          = $this->getReferenceID( $roleAssignment->getAttribute( 'assignTo' ) );
            $sectionLimitation = $this->getReferenceID( $roleAssignment->getAttribute( 'sectionLimitation' ) );
            $subtreeLimitation = $this->getReferenceID( $roleAssignment->getAttribute( 'subtreeLimitation' ) );

            $role = eZRole::fetch( $roleID );
            if ( !$role )
            {
                $this->writeMessage( "\tRole $roleID does not exist.", 'warning' );
                continue;
            }

            $referenceID = $this->getReferenceID( $assignTo );
            if ( !$referenceID )
            {
                $this->writeMessage( "\tInvalid object $referenceID does not exist.", 'warning' );
                continue;
            }

            if ( $sectionLimitation )
            {
                $section = $this->getReferenceID( $sectionLimitation );
                if ( $section )
                {
                    $role->assignToUser( $referenceID, 'section', $section );
                    $this->writeMessage( "\tAssigned role $roleID: $referenceID to $section", 'notice' );
                }
                else
                {
                    $this->writeMessage( "\tInvalid section $sectionLimitation does not exist.", 'warning' );
                    continue;
                }
            }
            elseif ( $subtreeLimitation )
            {
                $subtree = $this->getReferenceID( $subtreeLimitation );
                if ( $subtree )
                {
                    $role->assignToUser( $referenceID, 'subtree', $subtree );
                    $this->writeMessage( "\tAssigned role $roleID: $referenceID to $subtree", 'notice' );
                }
                else
                {
                    $this->writeMessage( "\tInvalid section $subtreeLimitation does not exist.", 'warning' );
                    continue;
                }
            }
            else
            {
                $role->assignToUser( $referenceID );
                    $this->writeMessage( "\tAssigned role $roleID: $referenceID", 'notice' );
            }
        }
      }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'AssignRoles', 'Info' => 'assign roles to user' );
    }
}

?>