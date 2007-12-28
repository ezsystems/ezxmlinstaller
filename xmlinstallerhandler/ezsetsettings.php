<?php
include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZSetSettings extends eZXMLInstallerHandler
{

    function eZSetSettings( )
    {
    }

    function execute( $xml )
    {
        $settingsFileList = $xml->getElementsByTagName( 'SettingsFile' );
        foreach ( $settingsFileList as $settingsFile )
        {
            $fileName = $settingsFile->getAttribute( 'name' );
            $location = $settingsFile->getAttribute( 'location' );

            $this->writeMessage( "\tSetting settings: $location/$fileName", 'notice' );
            $ini = eZINI::instance( $fileName, $location, null, null, null, true );
            $settingsBlockList = $settingsFile->getElementsByTagName( 'SettingsBlock' );
            foreach ( $settingsBlockList as $settingsBlock )
            {
                $blockName = $settingsBlock->getAttribute( 'name' );
                $values = $settingsBlock->childNodes;
                $settingValue = false;
                foreach ( $values as $value )
                {
                    $variableName = $value->nodeName;
                    if ($value->nodeName == "#text" )
                        continue;

                    if ( get_class($value) == 'DOMElement' && $value->hasAttribute( 'value' ) )
                    {
                        $settingValue = $value->getAttribute( 'value' );
                    }
                    elseif ( get_class($value) == 'DOMElement' && $value->hasChildNodes() )
                    {
                        if ( $value->firstChild->nodeName == $value->lastChild->nodeName && $value->childNodes->length>1 )
                        {
                            $variableName = $value->tagName;
                            $vals = $value->getElementsByTagName( 'value' );
                            $settingValue = array();
                            foreach ( $vals as $val )
                            {
                                $key = $val->getAttribute( 'key' );
                                if ( $key )
                                {
                                    $settingValue[$key] = $this->parseAndReplaceStringReferences( $val->textContent );
                                }
                                else
                                {
                                    $settingValue[] = $this->parseAndReplaceStringReferences( $val->textContent );
                                }
                            }
                        }
                        else
                        {
                            $settingValue  = $this->parseAndReplaceStringReferences( $value->nodeValue );
                       }
                    }
                    elseif ( $value->textContent )
                    {
                        $settingValue = $value->textContent;
                    }
                    else
                    {
                        $settingValue = $this->parseAndReplaceStringReferences( $value->textContent );
                    }
                    $existingVar = $ini->variable( $blockName, $variableName );
                    if ( is_string( $existingVar ) && is_string( $settingValue ) )
                    {
                        $ini->setVariable( $blockName, $variableName, $settingValue );
                    }
                    elseif ( is_array( $existingVar ) && is_string( $settingValue ) )
                    {
                        $existingVar[] = $settingValue;
                        $ini->setVariable( $blockName, $variableName, $existingVar );
                    }
                    elseif ( is_array( $existingVar ) && is_array( $settingValue ) )
                    {
                        $mergedArray = array_merge( $existingVar, $settingValue );
                        $ini->setVariable( $blockName, $variableName, array_unique( $mergedArray ) );
                    }
                    else
                    {
                        $ini->setVariable( $blockName, $variableName, $settingValue );
                    }
                }
            }
            $ini->save( false, ".append.php" );
            unset( $ini );
        }
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'SetSettings', 'Info' => 'manipulate settings files' );
    }
}

?>