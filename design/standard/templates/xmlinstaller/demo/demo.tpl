{*set $tpl_info=hash(
        'sa_name',      hash(   'info',     'Name of siteaccess',
                                'type',     'string' ),

        'sa_abbr',      hash(   'info',     'Abbr of siteaccess',
                                'type',     'string' ),

        'sa_url',       hash(   'info',     'URL to siteaccess',
                                'type',     'string',
                                'default',  'http://mmc.ez' ),

        'data_source',  hash(   'info',     'Directory to data',
                                'type',     'string',
                                'default',  'extension/ezhealthcare/data' )
)*}
{set $tpl_info=hash()}
{def $site_access_name=concat($sa_abbr,'_user_dut')
     $site_access_dir=concat('settings/siteaccess/',$site_access_name)}
{set-block variable='xml_data'}
<?xml version = '1.0' encoding = 'UTF-8'?>
<eZXMLImporter
    name="{$sa_name}"
    abbr="{$sa_abbr}"
    url="{$sa_url}">

<ProccessInformation comment="Creating roles" />
<CreateRole>
<Role name="Anonymous" createRole="true">
    <Policy module="content" function="read">
    <Limitations>
        <Section>1</Section>
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
    <Role name="PublicTeamroom" createRole="true">
    <Policy module="content" function="*">

    </Policy>
    </Role>
    <Role name="Teamroom Member" createRole="true">
    <Policy module="content" function="pendinglist">
    </Policy>
    <Policy module="content" function="bookmark">
    </Policy>
    <Policy module="content" function="read">
        <Limitations>

        <Class>45</Class>
        <Section>7</Section>
        <Group>1</Group>
        </Limitations>
    </Policy>
    <Policy module="content" function="read">
        <Limitations>

        <Class>3</Class>
        <Class>4</Class>
        <Group>1</Group>
        </Limitations>
    </Policy>
    <Policy module="content" function="read">
        <Limitations>

        <Class>25</Class>
        <Class>51</Class>
        <Section>1</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>
    </Role>
    <Role name="Teamroom Leaders" createRole="true">

    <Policy module="content" function="read">
        <Limitations>
        <Section>7</Section>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    <Policy module="content" function="edit">
        <Limitations>

        <Class>45</Class>
        <Section>1</Section>
        <Section>7</Section>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    <Policy module="content" function="create">

        <Limitations>
        <Class>20</Class>
        <Section>1</Section>
        <Section>7</Section>
        <ParentClass>19</ParentClass>
        </Limitations>
    </Policy>

    <Policy module="content" function="edit">
        <Limitations>
        <Class>20</Class>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    <Policy module="teamroom" function="manageroles">
        <Limitations>

        <Membership>1</Membership>
        <Membership>2</Membership>
        <Membership>3</Membership>
        </Limitations>
    </Policy>
    <Policy module="content" function="read">
        <Limitations>

        <Class>4</Class>
        <Section>1</Section>
        <Section>2</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>
    <Policy module="teamroom" function="manage">

    </Policy>
    </Role> 
    <Role name="Teamroom Read Forum" createRole="true">
    <Policy module="content" function="read">
        <Limitations>
        <Class>35</Class>
        <Class>36</Class>
        <Class>37</Class>

        <Section>1</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Use Forum" createRole="true">
    <Policy module="content" function="create">
        <Limitations>

        <Class>36</Class>
        <Class>37</Class>
        <Section>7</Section>
        </Limitations>
    </Policy>
    <Policy module="content" function="edit">
        <Limitations>

        <Class>36</Class>
        <Class>37</Class>
        <Section>7</Section>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    <Policy module="content" function="remove">

        <Limitations>
        <Class>36</Class>
        <Class>37</Class>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Read Blog" createRole="true">

    <Policy module="content" function="read">
        <Limitations>
        <Class>19</Class>
        <Class>20</Class>
        <Section>7</Section>
        <Group>1</Group>
        </Limitations>

    </Policy>
    </Role> 
    <Role name="Teamroom Use Blog" createRole="true">
    <Policy module="content" function="create">
        <Limitations>
        <Class>20</Class>
        <Section>1</Section>
        <Section>7</Section>

        <ParentClass>20</ParentClass>
        </Limitations>
    </Policy>
    <Policy module="content" function="edit">
        <Limitations>
        <Class>20</Class>
        <Section>1</Section>

        <Section>7</Section>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    <Policy module="content" function="remove">
        <Limitations>
        <Class>20</Class>

        <Owner>1</Owner>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Read Calendar" createRole="true">
    <Policy module="content" function="read">
        <Limitations>
        <Class>38</Class>

        <Class>39</Class>
        <Section>1</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Use Calendar" createRole="true">
    <Policy module="content" function="create">

        <Limitations>
        <Class>38</Class>
        <Section>1</Section>
        <Section>7</Section>
        <ParentClass>39</ParentClass>
        </Limitations>
    </Policy>

    <Policy module="content" function="edit">
        <Limitations>
        <Class>38</Class>
        <Section>1</Section>
        <Section>7</Section>
        <Group>1</Group>
        </Limitations>

    </Policy>
    <Policy module="content" function="remove">
        <Limitations>
        <Class>38</Class>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Read Documents" createRole="true">

    <Policy module="content" function="read">
        <Limitations>
        <Class>28</Class>
        <Class>50</Class>
        <Section>1</Section>
        <Section>7</Section>
        </Limitations>

    </Policy>
    </Role> 
    <Role name="Teamroom Use Documents" createRole="true">
    <Policy module="content" function="create">
        <Limitations>
        <Class>28</Class>
        <Section>1</Section>
        <Section>7</Section>

        <ParentClass>50</ParentClass>
        </Limitations>
    </Policy>
    <Policy module="content" function="edit">
        <Limitations>
        <Class>28</Class>
        <Section>1</Section>

        <Section>7</Section>
        </Limitations>
    </Policy>
    <Policy module="content" function="remove">
        <Limitations>
        <Class>28</Class>
        <Owner>1</Owner>

        </Limitations>
    </Policy>
    </Role>     
<Role name="Teamroom Read Wiki" createRole="true">
    <Policy module="content" function="read">
        <Limitations>
        <Class>24</Class>
        <Section>1</Section>

        <Section>7</Section>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Use Wiki" createRole="true">
    <Policy module="content" function="create">
        <Limitations>
        <Class>24</Class>

        <Section>1</Section>
        <Section>7</Section>
        <ParentClass>24</ParentClass>
        </Limitations>
    </Policy>
    <Policy module="content" function="edit">
        <Limitations>

        <Class>24</Class>
        <Section>1</Section>
        <Section>7</Section>
        <Group>1</Group>
        </Limitations>
    </Policy>
    <Policy module="content" function="remove">

        <Limitations>
        <Class>24</Class>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    </Role> 
    <Role name="Teamroom Read Tasklist" createRole="true">
    <Policy module="content" function="read">

        <Limitations>
        <Class>47</Class>
        <Class>48</Class>
        <Section>1</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>

    </Role>
    <Role name="Teamroom Use Tasklist" createRole="true">
    <Policy module="content" function="create">
        <Limitations>
        <Class>48</Class>
        <Section>1</Section>
        <Section>7</Section>
        <ParentClass>47</ParentClass>

        </Limitations>
    </Policy>
    <Policy module="content" function="edit">
        <Limitations>
        <Class>48</Class>
        <Section>1</Section>
        <Section>7</Section>

        </Limitations>
    </Policy>
    <Policy module="tasklist" function="modify">
    </Policy>
    <Policy module="content" function="remove">
        <Limitations>
        <Class>48</Class>
        <Owner>1</Owner>

        </Limitations>
    </Policy>
    </Role>     
<Role name="Teamroom Read Milestones" createRole="true">
    <Policy module="content" function="read">
        <Limitations>
        <Class>52</Class>
        <Class>53</Class>

        <Section>1</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>
    </Role>     
<Role name="Teamroom Use Milestones" createRole="true">
    <Policy module="content" function="create">
        <Limitations>

        <Class>52</Class>
        <Section>1</Section>
        <Section>7</Section>
        <ParentClass>53</ParentClass>
        </Limitations>
    </Policy>
    <Policy module="content" function="edit">

        <Limitations>
        <Class>52</Class>
        <Section>1</Section>
        <Section>7</Section>
        </Limitations>
    </Policy>
    <Policy module="content" function="remove">

        <Limitations>
        <Class>52</Class>
        <Owner>1</Owner>
        </Limitations>
    </Policy>
    </Role>
</CreateRole>




    <ProccessInformation comment="Creating classes" />
  <CreateClass>
    <ContentClass isContainer="true" identifier="blog" remoteID="3a6f9c1f075b3bf49d7345576b196fe8" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Blog" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="3">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="blog_post" remoteID="7ecb961056b7cbb30f22a91357e0a007" objectNamePattern="&lt;title&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Blog post" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
        <Group id="5" name="Teamroom"/>
      </Groups>
      
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="body" placement="2">
          <Names eng-GB="Body" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>25</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="true" informationCollector="false" translatable="true" identifier="publication_date" placement="3">
          <Names eng-GB="Publication date" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="true" informationCollector="false" translatable="true" identifier="unpublish_date" placement="4">
          <Names eng-GB="Unpublish date" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="5">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezboolean" required="false" searchable="true" informationCollector="false" translatable="true" identifier="enable_comments" placement="6">
          <Names eng-GB="Enable comments" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <default-value/>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="product" remoteID="77f3ede996a3a39c7159cc69189c5307" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Product" always-available="eng-GB"/>
      <Groups>
        
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="product_number" placement="2">
          <Names eng-GB="Product number" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_description" placement="3">
          <Names eng-GB="Short description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="4">
          
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezprice" required="false" searchable="false" informationCollector="false" translatable="true" identifier="price" placement="5">
          <Names eng-GB="Price" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <vat-included/>
            <vat-type/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezimage" required="false" searchable="false" informationCollector="false" translatable="true" identifier="image" placement="6">
          <Names eng-GB="Image" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-size>0</max-size>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="caption" placement="7">
          <Names eng-GB="Caption %28Image%29" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezmultioption" required="false" searchable="true" informationCollector="false" translatable="true" identifier="additional_options" placement="8">
          <Names eng-GB="Additional options" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="9">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
            
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="feedback_form" remoteID="df0257b8fc55f6b8ab179d6fb915455e" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Feedback form" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
        
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="true" searchable="false" informationCollector="true" translatable="false" identifier="sender_name" placement="3">
          
          <Names eng-GB="Sender name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="true" translatable="true" identifier="subject" placement="4">
          <Names eng-GB="Subject" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="eztext" required="true" searchable="true" informationCollector="true" translatable="true" identifier="message" placement="5">
          <Names eng-GB="Message" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezemail" required="true" searchable="false" informationCollector="true" translatable="false" identifier="email" placement="6">
          <Names eng-GB="Email" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezemail" required="false" searchable="false" informationCollector="false" translatable="false" identifier="recipient" placement="7">
          <Names eng-GB="Recipient" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="frontpage" remoteID="e36c458e3e4a81298a0945f53a2c81f4" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      
      <Names eng-GB="Frontpage" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezobjectrelation" required="false" searchable="false" informationCollector="false" translatable="true" identifier="billboard" placement="2">
          <Names eng-GB="Billboard" always-available="eng-GB"/>
          <DatatypeParameters>
            <selection-type/>
            <fuzzy-match/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="left_column" placement="3">
          <Names eng-GB="Left column" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>20</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="center_column" placement="4">
          
          <Names eng-GB="Center column" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>20</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="right_column" placement="5">
          <Names eng-GB="Right column" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <text-column-count>20</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="bottom_column" placement="6">
          <Names eng-GB="Bottom column" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="7">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="documentation_page" remoteID="d4a05eed0402e4d70fedfda2023f1aa2" objectNamePattern="&lt;title&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Documentation page" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Title" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="body" placement="2">
          <Names eng-GB="Body" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>20</text-column-count>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="3">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezboolean" required="false" searchable="false" informationCollector="false" translatable="true" identifier="show_children" placement="4">
          <Names eng-GB="Display sub items" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="infobox" remoteID="0b4e8accad5bec5ba2d430acb25c1ff6" objectNamePattern="&lt;header&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Infobox" always-available="eng-GB"/>
      
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="false" informationCollector="false" translatable="true" identifier="header" placement="1">
          <Names eng-GB="Header" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezobjectrelation" required="false" searchable="true" informationCollector="false" translatable="true" identifier="box_icon" placement="2">
          <Names eng-GB="Box Icon" always-available="eng-GB"/>
          <DatatypeParameters>
            <selection-type/>
            <fuzzy-match/>
            <default-selection/>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="false" informationCollector="false" translatable="true" identifier="content" placement="3">
          <Names eng-GB="Content" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezurl" required="false" searchable="false" informationCollector="false" translatable="true" identifier="url" placement="4">
          <Names eng-GB="URL" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="false" informationCollector="false" translatable="true" identifier="module_url" placement="5">
          <Names eng-GB="Module URL" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="file" remoteID="637d58bfddf164627bdfd265733280a0" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="File" always-available="eng-GB"/>
      <Groups>
        <Group id="3" name="Media"/>
        
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string>New file</default-string>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezselection" required="false" searchable="true" informationCollector="false" translatable="true" identifier="category" placement="2">
          <Names eng-GB="Category" always-available="eng-GB"/>
          <DatatypeParameters>
            <options/>
            <is-multiselect>0</is-multiselect>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="eztext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="3">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>2</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezbinaryfile" required="false" searchable="false" informationCollector="false" translatable="true" identifier="file" placement="4">
          <Names eng-GB="File" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-size>0</max-size>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="5">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
          
        </Attribute>
      </Attributes>
    </ContentClass>
    <ContentClass isContainer="true" identifier="gallery" remoteID="6a320cdc3e274841b82fcd63a86f80d1" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      
      <Names eng-GB="Gallery" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_description" placement="2">
          <Names eng-GB="Short description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="3">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezobjectrelation" required="false" searchable="true" informationCollector="false" translatable="true" identifier="image" placement="4">
          
          <Names eng-GB="Image" always-available="eng-GB"/>
          <DatatypeParameters>
            <selection-type/>
            <fuzzy-match/>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="forum" remoteID="b241f924b96b267153f5f55904e0675a" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Forum" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
            
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="forum_topic" remoteID="71f99c516743a33562c3893ef98c9b60" objectNamePattern="&lt;subject&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Forum topic" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
        
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="subject" placement="1">
          <Names eng-GB="Subject" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="eztext" required="true" searchable="true" informationCollector="false" translatable="true" identifier="message" placement="2">
          <Names eng-GB="Message" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezboolean" required="false" searchable="true" informationCollector="false" translatable="true" identifier="sticky" placement="3">
          
          <Names eng-GB="Sticky" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezsubtreesubscription" required="false" searchable="false" informationCollector="false" translatable="true" identifier="notify_me" placement="4">
          <Names eng-GB="Notify me about updates" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
          
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="forum_reply" remoteID="80ee42a66b2b8b6ee15f5c5f4b361562" objectNamePattern="&lt;subject&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Forum reply" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="subject" placement="1">
          <Names eng-GB="Subject" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="eztext" required="true" searchable="true" informationCollector="false" translatable="true" identifier="message" placement="2">
          <Names eng-GB="Message" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="event" remoteID="5857f3db3c3957cb42ba9ea198ce329f" objectNamePattern="&lt;short_title|title&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Event" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Full title" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-length>55</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="short_title" placement="2">
          <Names eng-GB="Short title" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <max-length>19</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="text" placement="3">
          <Names eng-GB="Text" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="category" placement="4">
          <Names eng-GB="Category" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="true" searchable="false" informationCollector="false" translatable="false" identifier="from_time" placement="5">
          <Names eng-GB="From Time" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="false" informationCollector="false" translatable="false" identifier="to_time" placement="6">
          <Names eng-GB="To Time" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="localtion" placement="7">
          <Names eng-GB="Localtion" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="event_calendar" remoteID="020cbeb6382c8c89dcec2cd406fb47a8" objectNamePattern="&lt;short_title|title&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Event calendar" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Full Title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>65</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_title" placement="2">
          
          <Names eng-GB="Short Title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>25</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezselection" required="true" searchable="false" informationCollector="false" translatable="false" identifier="view" placement="3">
          <Names eng-GB="View" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <options>
              
              
            </options>
            <is-multiselect>0</is-multiselect>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="banner" remoteID="9cb558e25fd946246bbb32950c00228e" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Banner" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="false" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="false" informationCollector="false" translatable="true" identifier="url" placement="2">
          <Names eng-GB="URL" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezimage" required="true" searchable="false" informationCollector="false" translatable="true" identifier="image" placement="3">
          <Names eng-GB="Image" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-size>0</max-size>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="eztext" required="false" searchable="false" informationCollector="false" translatable="true" identifier="image_map" placement="4">
          <Names eng-GB="Image map" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="5">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="forums" remoteID="60a921e54c1efbb9456bd2283d9e66cb" objectNamePattern="&lt;title&gt;" urlAliasPattern="" classExistAction="replace">
      
      <Names eng-GB="Forums" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
          
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="news_folder" remoteID="dedc38e5deb439180957c578d7815d3a" objectNamePattern="&lt;short_name|name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names always-available="eng-GB" eng-GB="News Folder"/>
      <Groups>
        <Group id="1" name="Content"/>
      </Groups>
      
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_name" placement="1">
          <Names always-available="eng-GB" eng-GB="Short name"/>
          <DatatypeParameters>
            <max-length>100</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="2">
          <Names always-available="eng-GB" eng-GB="Name"/>
          <DatatypeParameters>
            <max-length>255</max-length>
            <default-string>Folder</default-string>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_description" placement="3">
          
          <Names always-available="eng-GB" eng-GB="Summary"/>
          <DatatypeParameters>
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="4">
          <Names always-available="eng-GB" eng-GB="Description"/>
          <DatatypeParameters>
            
            <text-column-count>20</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezboolean" required="false" searchable="false" informationCollector="false" translatable="false" identifier="show_children" placement="5">
          <Names always-available="eng-GB" eng-GB="Display sub items"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="6">
          <Names always-available="eng-GB" eng-GB="Tags"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="true" informationCollector="false" translatable="true" identifier="publish_date" placement="7">
          <Names always-available="eng-GB" eng-GB="Publish date"/>
          <DatatypeParameters>
            
            <default-value/>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="personal_frontpage" remoteID="9492e0520daac8dc3809bd9550bb7b6f" objectNamePattern="&lt;short_title|title&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Personalized Frontpage" always-available="eng-GB"/>
      <Groups>
        <Group id="1" name="Content"/>
        <Group id="5" name="Teamroom"/>
      </Groups>
      
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_title" placement="2">
          <Names eng-GB="Short title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="3">
          
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezinteger" required="true" searchable="true" informationCollector="false" translatable="true" identifier="num_of_columns" placement="4">
          <Names eng-GB="Number of columns" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value>3</default-value>
            <min-value>1</min-value>
            <max-value>10</max-value>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezboolean" required="false" searchable="true" informationCollector="false" translatable="true" identifier="allow_minimization" placement="5">
          <Names eng-GB="Allow Minimization" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="default_arrangement" placement="6">
          <Names eng-GB="Default arrangement" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string>[[0,1],[2,3,4],[5,6,7]]</default-string>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="teamroom" remoteID="a3d405b81be900468eb153d774f4f0d2" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Teamroom" always-available="eng-GB"/>
      <Groups>
        
        <Group id="1" name="Content"/>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>255</max-length>
            
            <default-string>My Teamroom</default-string>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezselection" required="true" searchable="false" informationCollector="false" translatable="true" identifier="access_type" placement="2">
          <Names eng-GB="Teamroom Access Type" always-available="eng-GB"/>
          <DatatypeParameters>
            <options/>
            <is-multiselect>0</is-multiselect>
            
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="3">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="default_arrangement" placement="4">
          <Names eng-GB="Default Arrangement" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezfeatureselect" required="false" searchable="false" informationCollector="false" translatable="true" identifier="feature_list" placement="5">
          
          <Names eng-GB="Feature list" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-string>teamroom/createteamroom.tpl</default-string>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="team" remoteID="91cffc68fe24b3ad8dc7d1700bc0b830" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      
      <Names eng-GB="Team" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
          
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="task_list" remoteID="6029cf922fe32163735c8372c086f4a1" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Task list" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
      </Groups>
      
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="task" remoteID="60fe9c6a4e7c030678e516872995b5ee" objectNamePattern="&lt;title&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Task" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Title" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-length>0</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="eztext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="2">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezinteger" required="true" searchable="true" informationCollector="false" translatable="true" identifier="progress" placement="3">
          <Names eng-GB="Progress" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value>0</default-value>
            <min-value>0</min-value>
            <max-value>100</max-value>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="true" informationCollector="false" translatable="true" identifier="planned_end_date" placement="4">
          <Names eng-GB="Planned end date" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="true" informationCollector="false" translatable="true" identifier="end_date" placement="5">
          <Names eng-GB="End Date" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezinteger" required="false" searchable="true" informationCollector="false" translatable="true" identifier="est_hours" placement="6">
          <Names eng-GB="Estimated Effort %28hours%29" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value>0</default-value>
            <min-value>0</min-value>
            <max-value>100</max-value>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezinteger" required="false" searchable="true" informationCollector="false" translatable="true" identifier="est_minutes" placement="7">
          <Names eng-GB="Estimated Effort %28minutes%29" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value>0</default-value>
            
            <min-value>0</min-value>
            <max-value>60</max-value>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="true" informationCollector="false" translatable="true" identifier="tags" placement="8">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezobjectrelationlist" required="false" searchable="true" informationCollector="false" translatable="true" identifier="users" placement="9">
          <Names eng-GB="Users" always-available="eng-GB"/>
          <DatatypeParameters>
            <type>2</type>
            <class-constraints/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezobjectrelationlist" required="false" searchable="true" informationCollector="false" translatable="true" identifier="documents" placement="10">
          <Names eng-GB="Related documents" always-available="eng-GB"/>
          <DatatypeParameters>
            <type>2</type>
            <class-constraints/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezselection" required="false" searchable="true" informationCollector="false" translatable="true" identifier="access_type" placement="11">
          
          <Names eng-GB="Task visibility" always-available="eng-GB"/>
          <DatatypeParameters>
            <options/>
            <is-multiselect>0</is-multiselect>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezinteger" required="true" searchable="false" informationCollector="false" translatable="true" identifier="priority" placement="12">
          <Names eng-GB="Priority" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <default-value>5</default-value>
            <min-value>1</min-value>
            <max-value>5</max-value>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
      
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="file_folder" remoteID="a3d405b81be900468eb153d774f4f0d2" objectNamePattern="&lt;short_name|name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="File Folder" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>255</max-length>
            <default-string>Folder</default-string>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_name" placement="2">
          <Names eng-GB="Short name" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <max-length>100</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_description" placement="3">
          <Names eng-GB="Summary" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="4">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>20</text-column-count>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="false" informationCollector="false" translatable="true" identifier="tags" placement="5">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="box_folder" remoteID="a3d405b81be900468eb153d774f4f0d2" objectNamePattern="&lt;name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Box Folder" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            
            <max-length>255</max-length>
            <default-string>Folder</default-string>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="false" identifier="milestone" remoteID="abb36385bc3d3469bd0e99e9a45c6f25" objectNamePattern="&lt;title&gt;" urlAliasPattern="" classExistAction="replace">
      
      <Names eng-GB="Milestone" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="title" placement="1">
          <Names eng-GB="Title" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>0</max-length>
            
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezboolean" required="false" searchable="true" informationCollector="false" translatable="true" identifier="closed" placement="2">
          <Names eng-GB="Closed" always-available="eng-GB"/>
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="3">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>10</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezdatetime" required="false" searchable="true" informationCollector="false" translatable="true" identifier="date" placement="4">
          <Names eng-GB="Date" always-available="eng-GB"/>
          
          <DatatypeParameters>
            <default-value/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezobjectrelationlist" required="false" searchable="true" informationCollector="false" translatable="true" identifier="related_tasks" placement="5">
          <Names eng-GB="Related Tasks" always-available="eng-GB"/>
          <DatatypeParameters>
            <type>2</type>
            
            <class-constraints/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezobjectrelationlist" required="false" searchable="true" informationCollector="false" translatable="true" identifier="related_files" placement="6">
          <Names eng-GB="Related FIles" always-available="eng-GB"/>
          <DatatypeParameters>
            <type>2</type>
            <class-constraints/>
            
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
    
    <ContentClass isContainer="true" identifier="milestone_folder" remoteID="a3d405b81be900468eb153d774f4f0d2" objectNamePattern="&lt;short_name|name&gt;" urlAliasPattern="" classExistAction="replace">
      <Names eng-GB="Milestone Folder" always-available="eng-GB"/>
      <Groups>
        <Group id="5" name="Teamroom"/>
        
      </Groups>
      <Attributes>
        <Attribute datatype="ezstring" required="true" searchable="true" informationCollector="false" translatable="true" identifier="name" placement="1">
          <Names eng-GB="Name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>255</max-length>
            <default-string>Folder</default-string>
          </DatatypeParameters>
          
        </Attribute>
        <Attribute datatype="ezstring" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_name" placement="2">
          <Names eng-GB="Short name" always-available="eng-GB"/>
          <DatatypeParameters>
            <max-length>100</max-length>
            <default-string/>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="short_description" placement="3">
          <Names eng-GB="Summary" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>5</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezxmltext" required="false" searchable="true" informationCollector="false" translatable="true" identifier="description" placement="4">
          <Names eng-GB="Description" always-available="eng-GB"/>
          <DatatypeParameters>
            <text-column-count>20</text-column-count>
          </DatatypeParameters>
        </Attribute>
        <Attribute datatype="ezkeyword" required="false" searchable="false" informationCollector="false" translatable="true" identifier="tags" placement="5">
          <Names eng-GB="Tags" always-available="eng-GB"/>
          <DatatypeParameters>
          </DatatypeParameters>
        </Attribute>
      </Attributes>
    </ContentClass>
  </CreateClass>
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
    </CreateContent>
    <ProccessInformation comment="Assigning roles to user groups" />
    <AssignRoles>
        <RoleAssignment roleID="6"  assignTo="internal:CONTENT_MANAGER_GROUP" subtreeLimitation="internal:FRONTEND_NODE_ID" />
    </AssignRoles>
</eZXMLImporter>
{/set-block}
