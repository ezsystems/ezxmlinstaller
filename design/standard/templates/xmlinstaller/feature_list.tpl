{set $xmlinstaller_feature_list =   hash( 'files2',       hash( 'type',    'boolean',
                                                              'info',    'Files 2   ',
                                                              'default',    'yes' ),
                                          'tasks',     hash(  'type',    'selection' ,
                                                              'info',    'Task List',
                                                              'vars',    hash('var1', 'Var 1', 'caar2', 'Var 2'),
                                                              'default', 'caar2' ),
                                         'files',       hash( 'type',    'boolean',
                                                              'info',    'Files' ),
                                         'forum',       hash( 'type',    'string',
                                                              'info',    'Message Board' ),
                                         'sa_name',     hash( 'info',    'Name of siteaccess',
                                                              'type',    'string' ),
                                          'sa_abbr',    hash( 'info',    'Abbr of siteaccess',
                                                              'type',    'string' ),
                                         'sa_url',      hash( 'info',    'URL to siteaccess',
                                                              'type',    'string',
                                                              'default', 'http://mmc.ez' ),
                                         'data_source', hash( 'info',    'Directory to data',
                                                              'type',    'string',
                                                              'default', 'extension/ezxmlinstaller/data' )
)}
{def $site_access_name=concat($sa_abbr,'_user_dut')
     $site_access_dir=concat('settings/siteaccess/',$site_access_name)}
{set-block variable='xml_data'}
<?xml version = '1.0' encoding = 'ISO-8859-1'?>
<eZXMLInstaller
    name="{$sa_name}"
    abbr="{$sa_abbr}"
    url="{$sa_url}"
    data_source="{$data_source}">
    <ProccessInformation comment="Tasks: {$tasks}" color="cyan" />
    <ProccessInformation comment="Files 2: {$files2}" color="cyan" />
    <ProccessInformation comment="Files: {$files}" color="red" type="error" />
    <ProccessInformation comment="Forum: {$forum}" color="yellow" />
    <ProccessInformation comment="SA Name: {$sa_name}" color="blue" />
    <ProccessInformation comment="SA Abbr: {$sa_abbr}" color="cyan" />
    <ProccessInformation comment="URL: {$sa_url}" color="cyan" />
    <ProccessInformation comment="Data source: {$data_source}" color="cyan" />
</eZXMLInstaller>

{*
    <CreateContent parentNode="43">
        <ContentObject contentClass="folder" section="3" remoteID="{$sa_abbr}_folder_image_pool">
            <Attributes>
                <name>{$sa_abbr|upcase} - Folder Image Pool</name>
                <short_name>{$sa_abbr|upcase} - Folder Image Pool</short_name>
            </Attributes>
            <SetReference attribute="object_id" value="FOLDER_IMAGE_POOL" />
        </ContentObject>
        <ContentObject contentClass="folder" section="3" remoteID="{$sa_abbr}_article_image_pool">
            <Attributes>
                <name>{$sa_abbr|upcase} - Article Image Pool</name>
                <short_name>{$sa_abbr|upcase} - Article Image Pool</short_name>
            </Attributes>
            <SetReference attribute="object_id" value="ARTICLE_IMAGE_POOL" />
            <Childs>
                <ContentObject contentClass="image" section="3" remoteID="">
                    <Attributes>
                        <name>Image 1</name>
                        <image src="images/34674783.jpg" title="asdf" />
                    </Attributes>
                    <SetReference attribute="object_id" value="IMAGE_1" />
                </ContentObject>
            </Childs>
        </ContentObject>
        <ContentObject contentClass="folder" section="3" remoteID="{$sa_abbr}_news_image_pool">
            <Attributes>
                <name>{$sa_abbr|upcase} - News Image Pool</name>
                <short_name>{$sa_abbr|upcase} - News Image Pool</short_name>
            </Attributes>
            <SetReference attribute="object_id" value="NEWS_IMAGE_POOL" />
        </ContentObject>
    </CreateContent>
    <ProccessInformation comment="Content in content section" />
    <CreateContent parentNode="2">
        <ContentObject contentClass="frontpage" section="1" remoteID="{$sa_abbr}_frontpage_object">
            <Attributes>
                <title>{$sa_name}</title>
                <short_title>{$sa_abbr|upcase}</short_title>
                <banner>internal:IMAGE_1</banner>
                <spotlight1_title>my spotlight</spotlight1_title>
                <spotlight1_image>internal:IMAGE_1</spotlight1_image>
            </Attributes>
            <Childs>
                <ContentObject contentClass="feedback_form" section="1" remoteID="{$sa_abbr}_contact_form">
                    <Attributes>
                      <title>Get in contact</title>
                      <short_title>contact</short_title>
                      <recipient>dis@ez.no</recipient>
                    </Attributes>
                    <SetReference attribute="object_id" value="CONTACT_FORM" />
                </ContentObject>
                <ContentObject contentClass="section" section="7" remoteID="{$sa_abbr}_patient_section">
                    <Attributes>
                      <title>Patientinformatie</title>
                      <short_title>patienten</short_title>
                      <view_mode>0</view_mode>
                    </Attributes>
                </ContentObject>
                <ContentObject contentClass="section" section="8" remoteID="{$sa_abbr}_visitor_section">
                    <Attributes>
                      <title>Visitorinformatie</title>
                      <short_title>visitors</short_title>
                      <view_mode>0</view_mode>
                    </Attributes>
                </ContentObject>
                <ContentObject contentClass="section" section="9" remoteID="{$sa_abbr}_professional_section">
                    <Attributes>
                      <title>Professionalinformatie</title>
                      <short_title>professionals</short_title>
                      <view_mode>0</view_mode>
                    </Attributes>
                </ContentObject>
                <ContentObject contentClass="section" section="1" remoteID="{$sa_abbr}_over_section">
                    <Attributes>
                      <title>Over {$sa_abbr|upcase}</title>
                      <short_title>over {$sa_abbr}</short_title>
                      <view_mode>0</view_mode>
                    </Attributes>
                </ContentObject>
                <ContentObject contentClass="metadata" section="1" remoteID="{$sa_abbr}_metadata">
                    <Attributes>
                      <title>Metadata</title>
                      <description>MMC {$sa_abbr|upcase} division</description>
                      <author>{$sa_abbr|upcase}@mmc.nl</author>
                      <copyright>(c)2007 MMC</copyright>
                      <keywords></keywords>
                    </Attributes>
                </ContentObject>
                <ContentObject contentClass="template_look" section="1" remoteID="{$sa_abbr}_pagelayout">
                    <Attributes>
                      <title>Pagelayout</title>
                      <page_title>{$sa_abbr|upcase} - {$sa_name}</page_title>
                      <site_url url="http://mmc.ez/{$sa_abbr}" title="{$sa_abbr|upcase}" />
                      <image src="images/logo.png" title="{$sa_abbr|upcase} - {$sa_name}" />
                      <footer_text>(c)2007 - {$sa_name}</footer_text>
                      <contact_form>internal:CONTACT_FORM</contact_form>
                      <disclaimer>object_id:208</disclaimer>
                      <colofon>object_id:209</colofon>
                      <folder_image_pool>internal:FOLDER_IMAGE_POOL</folder_image_pool>
                      <article_image_pool>internal:ARTICLE_IMAGE_POOL</article_image_pool>
                      <news_image_pool>internal:NEWS_IMAGE_POOL</news_image_pool>
                    </Attributes>
                    <SetReference attribute="node_id" value="SITEDESIGN_NODE_ID" />
                </ContentObject>
            </Childs>
            <SetReference attribute="node_id" value="FRONTEND_NODE_ID" />
        </ContentObject>
    </CreateContent>
  <ProccessInformation comment="Content in user section" />
  <CreateContent parentNode="5">
        <ContentObject contentClass="user_group" section="2" remoteID="{$sa_abbr}_content_manager_group">
            <Attributes>
                <name>{$sa_abbr|upcase}</name>
                <website_toolbar_access>1</website_toolbar_access>
            </Attributes>
            <SetReference attribute="object_id" value="CONTENT_MANAGER_GROUP" />
            <Childs>
                <ContentObject contentClass="user" section="2" remoteID="{$sa_abbr}_content_manager_testuser">
                    <Attributes>
                        <first_name>Test</first_name>
                        <last_name>User</last_name>
                        <user_account login="{$sa_abbr}_test_user" email="no_spamm@ez.no" password="publish" />
                    </Attributes>
                </ContentObject>
            </Childs>
        </ContentObject>
    </CreateContent>
    <ProccessInformation comment="Assigning roles to user groups" />
    <AssignRoles>
<!--         <RoleAssignment roleID="" assignTo="" sectionLimitation="" subtreeLimitation="" /> -->
        <RoleAssignment roleID="6"  assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
        <RoleAssignment roleID="8"  assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
        <RoleAssignment roleID="10" assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
        <RoleAssignment roleID="12" assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
        <RoleAssignment roleID="20" assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
        <RoleAssignment roleID="23" assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
    </AssignRoles>
    <ProccessInformation comment="Add location of teamroom leader" />
<AddLocation contentObject="" addToNode=""/>

*}
{/set-block}
