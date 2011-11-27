<?php

include_once('extension/ezxmlinstaller/classes/ezxmlinstallerhandler.php');

class eZModifyFileContent extends eZXMLInstallerHandler
{

    function eZModifyFileContent( )
    {
    }

    function execute( $xml )
    {
        $fileList = $xml->getElementsByTagName( 'File' );
        foreach ( $fileList as $file )
        {
            $fileName = $file->getAttribute( 'name' );
            $location = $file->getAttribute( 'location' );
            $key      = $file->getAttribute( 'key' );
            $value    = $this->getReferenceID( $file->getAttribute( 'value' ) );

            $fileNamePath = $location . eZDir::separator( eZDir::SEPARATOR_LOCAL ) . $fileName;
            if ( !file_exists( $fileNamePath ) )
            {
                $this->writeMessage("The file $fileNamePath does not exists", 'error');
                continue;
            }
            elseif ( !is_writable($fileNamePath) )
            {
                $this->writeMessage("The file $fileNamePath is not writable", 'error');
                continue;
            }
            
            $str = file_get_contents( $fileNamePath );
            $str = str_replace( $key, $value, $str);
            
            $fp = fopen( $fileNamePath, 'w' );
            fwrite( $fp, $str );
            fclose( $fp );
            
            $this->writeMessage("The file $fileNamePath has been updated");
        }
    }

    static public function handlerInfo()
    {
        return array( 
            'XMLName' => 'ModifyFileContent', 
            'Info'    => 'modify a specific content in files' 
        );
    }
}


