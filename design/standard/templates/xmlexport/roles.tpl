<?xml version = '1.0' encoding = 'UTF-8'?>
<eZXMLImporter>
  <CreateRole>
{foreach $role_list as $role}
     <Role name="{$role.name}" createRole="true">
        {foreach $role.policies as $policy}
        <Policy module="{$policy.module_name}" function="{$policy.function_name}">
            {if $policy.limitations|count()}
            <Limitations>
            {foreach $policy.limitations as $limitation}
                {foreach $limitation.values_as_array as $value}
                    <{$limitation.identifier}>{$value}</{$limitation.identifier}>
                {/foreach}
            {/foreach}
            </Limitations>
            {/if}
        </Policy>
        {/foreach}
    </Role>
{/foreach}
    </CreateRole>
</eZXMLImporter>
