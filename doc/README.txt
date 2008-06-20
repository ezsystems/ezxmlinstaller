eZ XML Installer extension 0.1.2 README


What is the eZ XML Installer extension?
=======================================

   The eZ XML Installer extension is a plattform to define 
   proccesses for eZ Publish and execute them.
   Proccesses will be defined in XML. The XML can be provided as
   completed XML or as eZ Publish Template.
   This template can contain a set of variables which will the user be ask for
   before the processes will be executed.
   The XML is a set of actions. Each action is bundled to a handler
   which will proccess this action.
   The set of handlers can be extended by custom handers.


eZ XML Installer version
========================

The current version of eZ XML Installer is 0.1.2.
You can find details about changes for this version in
doc/changelogs/


License
=======

This software is licensed under the GNU General Public License. The complete
license agreement is included in the LICENSE file. For more information
or questions please contact info@ez.no

Requirements
============

The following requirements exists for using eZ XML Installer extension:

o  eZ Publish version:

   Make sure you use eZ Publish version 4.0 or higher.

o  PHP version:

   as indicated in your eZ Publish version requirements


Installation
============

Please read the INSTALL file for installation instructions.


Creating a custom proccess XML file
===================================
A proccess XML files will be defined with a enclosing <eZXMLImporter> tag.
This tag contains a set of proccess definitions.
Here is an example:
<eZXMLImporter>
    <ProccessInformation comment="Content in media sections" />
    <MyCustomAction>
        <DoThis value1="foo" value2="bar" />
        <DoThat myvalue="lorem" myothervalue="ipsum" />
    </MyCustomAction>
</eZXMLImporter>

The XML structure of each action is defined by the handler.
The proccess definition will be executed step by step downwards from top.

The eZXMLImporter tag may contain some attributes which will be called "settings".
This can be global settings. E.g. where are some binary files located.

Internal / External references
------------------------------
The XML can contain internal or external referneces.
Currently are only "node_id" and "object_id" as external references are supported.
References can be only used by actions where the handler supports references.
* Internal reference definition
  References will be defined inside the action definition.
  Example:
  <SetReference attribute="object_id" value="CONTENT_MANAGER_GROUP" />
  The "attribute" attribute will define which variable of the current action will be defined as reference.
  The "value" attribute will name the variable the reference can be used with.
* External reference definition
  External references can be used without defintion.
  It is important to only use values which will exist in the installation.
  Otherwise this can cause errors and data inconsistencies.
* Usage of internal references
  Internal references can be used with the "internal:" prefix.
  References are not supported everywhere. Please consult the handler documentation where references can be used.
  Example:
  <RoleAssignment roleID="8" assignTo="internal:CONTENT_MANAGER_GROUP" />
  In this example role with id 8 will be assigned to the newly created group with reference "CONTENT_MANAGER_GROUP".
* Usage of external references
  Internal references can be used with the "node_id:" or "object_id:" prefix.
  References are not supported everywhere. Please consult the handler documentation where references can be used.

Default XML Installer handler
-----------------------------
Here is a short introduction in the hanlder shipped with this extension
* ProccessInformation
  This simple action will only display the given comment and increase the internal step counter.
* AssignRoles
  This action can be used to assign existing roles to user or user groups.
* CreateContent
  This action is used to create content objects based on existing content classes.
  Please note that currently not all datatypes are supported.
* SetSettings
  This action can create new or modify existing setting files.
  Please note that the standard eZ Publish mechanism is used.
  This may cause a loss of comments or unsupported ini usage.


Creating a custom proccess template file
========================================
The template based proccess definition is an extended version of the file based definition.
The XML structure must be defined as described.
The template must be located under templates/xmlinstaller in a valid design.
But it is possible to define a set of variables which will be requested and can be used in the xml.
Futhermore it is possible to use standard template functionallity.

Defing the XML structure
------------------------
The XML must be defined in a variable block with the name "xml_data".
Example:
{set-block variable='xml_data'}
<?xml version = '1.0' encoding = 'ISO-8859-1'?>
<eZXMLImporter>
[...]
</eZXMLImporter>
{/set-block}

Defining and using template variables
-------------------------------------
To request custom input before processing the "tpl_info" variable can be defined in the template.
E.g.
{set $tpl_info=hash(
        'var1',      hash(   'info',     'Number of objects',
                             'type',     'int' ),
        'var2',      hash(   'info',     'URL to siteaccess',
                             'type',     'string',
                             'default',  'http://ez.no' ))}
This will request two variables. "var1" and "var2"
The hash of each variable defines the additional information.
* info:    The info text for the request.
* type:    The type of the variable (int or string)
* default: The default value (used when input is empty).

The variabled will be rquested before proccessing and can thus be used in the template as named.
E.g. {$var1} and {$var2}.


Proccessing the XML
===================
The xmlinstaller.php script will proccess the XML.

The script is located in extension/ezxmlinstaller/bin/php/.

It can be used in two ways:
* Proccessing a template:
  php extension/ezxmlinstaller/bin/php/xmlinstaller.php --template=demo/mynewsiteaccess
  In this case the name of the template (without .tpl, but with additional directories)
  must be given as "template" parameter.
* Proccessing a xml file:
  php extension/ezxmlinstaller/bin/php/xmlinstaller.php --file=path/to/my/xml/demo.xml
  In this case the relative or absolute path to a valid xml file must be given as "file" parameter.


Creating a custom handler
=========================
possible, but not documented yet.


Where to get more help
======================

eZ Publish forums: http://ez.no/community/forum


Troubleshooting
===============

1. Read the FAQ
   ------------

   Some problems are more common than others. The most common ones are listed
   in the FAQ file.

2. Support
   -------

   If you have find any problems not handled by this document or the FAQ you
   can contact eZ system trough the support system:
   http://ez.no/services/support
