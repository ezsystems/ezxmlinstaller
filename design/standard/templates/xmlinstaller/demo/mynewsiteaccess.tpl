{set $tpl_info=hash(
        'sa_name',      hash(   'info',     'Name of siteaccess',
                                'type',     'string' ),

        'sa_abbr',      hash(   'info',     'Abbr of siteaccess',
                                'type',     'string' ),

        'sa_url',       hash(   'info',     'URL to siteaccess',
                                'type',     'string',
                                'default',  'http://mmc.ez' ),

        'data_source',  hash(   'info',     'Directory to data',
                                'type',     'string',
                                'default',  'extension/ezxmlinstaller/data' )
)}
{def $site_access_name=concat($sa_abbr,'_user_dut')
     $site_access_dir=concat('settings/siteaccess/',$site_access_name)}
{set-block variable='xml_data'}
<?xml version = '1.0' encoding = 'ISO-8859-1'?>
<eZXMLImporter
    name="{$sa_name}"
    abbr="{$sa_abbr}"
    url="{$sa_url}"
    data_source="{$data_source}">
    <ProccessInformation comment="Content in media sections" />
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
{*    <ProccessInformation comment="Content in content section" />
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
*}  <ProccessInformation comment="Content in user section" />
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
    </AssignRoles>{*
    <ProccessInformation comment="Adding siteaccess settings" />
    <SetSettings>
      <SettingsFile name="site.ini" location="{$site_access_dir}">
        <SettingsBlock name="ExtensionSettings">
            <ActiveAccessExtensions>
              <value></value>
              <value>ezdndsort</value>
              <value>mmc_design</value>
              <value>ezdhtml</value>
              <value>ezlabel</value>
              <value>mmc_general_gyn_settings</value>
              <value>ezarchivecontent</value>
            </ActiveAccessExtensions>
        </SettingsBlock>
        <SettingsBlock name="SiteSettings">
            <IndexPage>/content/view/full/[internal:FRONTEND_NODE_ID]/</IndexPage>
            <DefaultPage>/content/view/full/[internal:FRONTEND_NODE_ID]/</DefaultPage>
        </SettingsBlock>
        <SettingsBlock name="SiteAccessSettings">
            <RequireUserLogin>false</RequireUserLogin>
            <ShowHiddenNodes>false</ShowHiddenNodes>
            <PathPrefix>{$sa_abbr}</PathPrefix>
        </SettingsBlock>
        <SettingsBlock name="DesignSettings">
            <SiteDesign>mmc</SiteDesign>
            <AdditionalSiteDesignList>
                <value>base</value>
            </AdditionalSiteDesignList>
        </SettingsBlock>
        <SettingsBlock name="RegionalSettings">
            <Locale>dut-NL</Locale>
            <ContentObjectLocale>dut-NL</ContentObjectLocale>
            <SiteLanguageList>
              <value></value>
              <value>dut-NL</value>
            </SiteLanguageList>
            <TextTranslation>enabled</TextTranslation>
        </SettingsBlock>
      </SettingsFile>
      <SettingsFile name="content.ini" location="{$site_access_dir}">
        <SettingsBlock name="ArchiveSettings">
            <RootNodeList>
              <value></value>
              <value>401</value>
            </RootNodeList>
            <ArchiveRootFolder>447</ArchiveRootFolder>
        </SettingsBlock>
      </SettingsFile>
      <SettingsFile name="contentstructuremenu.ini" location="{$site_access_dir}">
        <SettingsBlock name="TreeMenu">
            <MaxDepth>2</MaxDepth>
        </SettingsBlock>
      </SettingsFile>
      <SettingsFile name="healthcare.ini" location="{$site_access_dir}">
        <SettingsBlock name="SiteSettings">
            <SiteRootNode>[internal:FRONTEND_NODE_ID]</SiteRootNode>
            <SiteDesignObject>[internal:SITEDESIGN_NODE_ID]</SiteDesignObject>
            <SiteBannerPool_Folder>202</SiteBannerPool_Folder>
            <SiteBannerPool_Article>176</SiteBannerPool_Article>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_section">
            <SiteDesignObject_spotlight1_image>folder_image_pool</SiteDesignObject_spotlight1_image>
            <SiteDesignObject_spotlight2_image>folder_image_pool</SiteDesignObject_spotlight2_image>
            <SiteDesignObject_spotlight3_image>folder_image_pool</SiteDesignObject_spotlight3_image>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_folder">
            <SiteDesignObject_spotlight1_image>folder_image_pool</SiteDesignObject_spotlight1_image>
            <SiteDesignObject_spotlight2_image>folder_image_pool</SiteDesignObject_spotlight2_image>
            <SiteDesignObject_spotlight3_image>folder_image_pool</SiteDesignObject_spotlight3_image>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_article">
            <SiteDesignObject_image>article_image_pool</SiteDesignObject_image>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_news_article">
            <SiteDesignObject_image>news_image_pool</SiteDesignObject_image>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_employee_folder">
            <SiteDesignObject_image>article_image_pool</SiteDesignObject_image>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_news_folder">
            <SiteDesignObject_image>news_image_pool</SiteDesignObject_image>
        </SettingsBlock>
        <SettingsBlock name="PoolSettings_frontpage">
            <SiteDesignObject_spotlight1_image>folder_image_pool</SiteDesignObject_spotlight1_image>
            <SiteDesignObject_spotlight2_image>folder_image_pool</SiteDesignObject_spotlight2_image>
            <SiteDesignObject_spotlight3_image>folder_image_pool</SiteDesignObject_spotlight3_image>
        </SettingsBlock>
      </SettingsFile>
    </SetSettings>
    <ProccessInformation comment="Adding global settings" />
    <SetSettings>
      <SettingsFile name="site.ini" location="settings/override">
        <SettingsBlock name="SiteAccessSettings">
            <RelatedSiteAccessList>
              <value>{$site_access_name}</value>
            </RelatedSiteAccessList>
            <AvailableSiteAccessList>
              <value>{$site_access_name}</value>
            </AvailableSiteAccessList>
            <URIMatchMapItems>
              <value>{$sa_abbr};{$site_access_name}</value>
            </URIMatchMapItems>
        </SettingsBlock>
        <SettingsBlock name="SiteSettings">
            <SiteList>
              <value>{$site_access_name}</value>
            </SiteList>
        </SettingsBlock>
      </SettingsFile>
    </SetSettings>*}
</eZXMLImporter>
{/set-block}