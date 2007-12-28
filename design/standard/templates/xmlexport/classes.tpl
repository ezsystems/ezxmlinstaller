<?xml version = '1.0' encoding = 'UTF-8'?>
<eZXMLImporter>
    <CreateClass>
{foreach $class_list as $class}

       <ContentClass isContainer="{if $class.is_container|eq(1)}true{else}false{/if}"
                     identifier="{$class.identifier}"
                     remoteID="{$class.remote_id}"
                     objectNamePattern="{$class.contentobject_name|wash}"
                     urlAliasPattern="{$class.url_alias_name|wash}"
                     classExistAction="new">
        <Names {foreach $class.nameList as $key => $name}{$key}="{$name}" {/foreach}/>
        <Groups>
            {foreach $class.ingroup_list as $group}
            <Group id="{$group.group_id}" name="{$group.group_name}" />
            {/foreach}
        </Groups>
        <Attributes>
            {foreach $class.data_map as $attribute}
            <Attribute datatype="{$attribute.data_type_string}"
                    required="{if $attribute.is_required|eq(1)}true{else}false{/if}"
                    searchable="{if $attribute.is_searchable|eq(1)}true{else}false{/if}"
                    informationCollector="{if $attribute.is_information_collector|eq(1)}true{else}false{/if}"
                    translatable="{if $attribute.can_translate|eq(1)}true{else}false{/if}"
                    identifier="{$attribute.identifier}"
                    placement="{$attribute.placement}">
                <Names {foreach $attribute.nameList as $key => $name}{$key}="{$name|urlencode}" {/foreach} />
                <DatatypeParameters>
                    {foreach $opt_list[$class.identifier][$attribute.identifier] as $key => $value}
                    <{$key}>{$value}</{$key}>
                {/foreach}
                </DatatypeParameters>
            </Attribute>
            {/foreach}
          </Attributes>
       </ContentClass>

{/foreach}
    </CreateClass>
</eZXMLImporter>
