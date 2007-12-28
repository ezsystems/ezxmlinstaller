{set $tpl_info=hash()}
{set-block variable='xml_data'}
<?xml version = '1.0' encoding = 'ISO-8859-1'?>
<eZXMLImporter>
{*    <ProccessInformation comment="Content in media sections" />
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
    </CreateContent>*}
    <ProccessInformation comment="Creating section" />
    <CreateSection sectionName="Test Section" navigationPart="ezcontentnavigationpart" referenceID="SECTION_XY" />
    <ProccessInformation comment="Creating role" />
    <CreateRole>
        <Role name="Anonymous" createRole="true" referenceID="MY_ROLE" replacePolicies="true">
            <Policy module="content" function="read">
            <Limitations>
                <Section>internal:SECTION_XY</Section>
            </Limitations>
            </Policy>
            <Policy module="content" function="pdf">
            <Limitations>
                <Section>1</Section>
            </Limitations>
            </Policy>
            <Policy module="rss" function="feed">
            </Policy>
            <Policy module="user" function="login">
            <Limitations>
                <SiteAccess>2582995467</SiteAccess>
                <SiteAccess>2978804645</SiteAccess>
            </Limitations>
            </Policy>
            <Policy module="content" function="read">
            <Limitations>
                <Class>29</Class>
                <Class>30</Class>
                <Class>31</Class>
                <Class>32</Class>
                <Class>33</Class>
                <Class>40</Class>
                <Class>43</Class>

                <Section>3</Section>
            </Limitations>
            </Policy>
            <Policy module="content" function="read">
            <Limitations>
                <Class>4</Class>
                <Section>2</Section>

            </Limitations>
            </Policy>
        </Role>
    </CreateRole>
</eZXMLImporter>
{/set-block}