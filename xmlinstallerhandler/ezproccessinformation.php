<?php
//
// Created on: <2007-12-28 06:09:00 dis>
//
// SOFTWARE NAME: eZ XML Installer extension for eZ Publish
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2010 eZ Systems AS
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
