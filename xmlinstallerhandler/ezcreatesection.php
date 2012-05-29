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

class eZCreateSection extends eZXMLInstallerHandler
{

    function __construct( )
    {
    }

    function execute( $xmlNode )
    {
        // ezcontentnavigationpart
        $sectionName    = $xmlNode->getAttribute( 'sectionName' );
        $sectionIdentifier    = $xmlNode->getAttribute( 'sectionIdentifier' );
        $navigationPart = $xmlNode->getAttribute( 'navigationPart' );
        $referenceID    = $xmlNode->getAttribute( 'referenceID' );

        if( $sectionIdentifier )
        {
            $sectionID = eZSection::fetchByIdentifier( $sectionIdentifier );
        }

        if( !$sectionID )
        {
            $sectionID = $this->sectionIDbyName( $sectionName );
        }

        if( $sectionID )
        {
            $this->writeMessage( "\tSection '$sectionName' already exists." , 'notice' );
        }
        else
        {
            $section = new eZSection( array() );
            $section->setAttribute( 'name', $sectionName );
            $section->setAttribute( 'identifier', $sectionIdentifier );
            $section->setAttribute( 'navigation_part_identifier', $navigationPart );
            $section->store();
            $sectionID = $section->attribute( 'id' );
        }
        $refArray = array();
        if ( $referenceID )
        {
            $refArray[$referenceID] = $sectionID;
        }
        $this->addReference( $refArray );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'CreateSection', 'Info' => 'create new section' );
    }

    private function sectionIDbyName( $name )
    {
        $sectionID = false;
        $sectionList = eZSection::fetchFilteredList( array( 'name' => $name ), false, false, true );
        if( is_array( $sectionList ) && count( $sectionList ) > 0 )
        {
            $section = $sectionList[0];
            if( is_object( $section ) )
            {
                $sectionID = $section->attribute( 'id' );
            }
        }
        return $sectionID;
    }

}

?>
