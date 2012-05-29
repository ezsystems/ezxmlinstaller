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

$module = $Params['Module'];
$http       = eZHTTPTool::instance();

$tpl = eZTemplate::factory();

$list = eZRole::fetchList( );

$tpl->setVariable( 'role_list', $list );
$tpl->setVariable( "role_count", count( $list ) );


$result = $tpl->fetch( 'design:xmlexport/roles.tpl' );
$result = ltrim(preg_replace('/<!--(.|\s)*?-->/', '', $result));

$doc = new DOMDocument;
$doc->loadXML( $result );


eZExecution::cleanup();
eZExecution::setCleanExit();
header('Content-Type: text/xml');
// header('Content-Type: text/html');
header('Pragma: no-cache' );
header('Expires: 0' );

echo $doc->saveXML();
// echo $result;

exit(0);



?>
