<?php
//
// Created on: <2007-12-28 06:09:00 dis>
//
// SOFTWARE NAME: eZ XML Installer extension for eZ Publish
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2012 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//


class eZAssignRoles extends eZXMLInstallerHandler
{

    function __construct( )
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
