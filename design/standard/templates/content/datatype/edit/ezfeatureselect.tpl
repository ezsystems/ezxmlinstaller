{if is_set( $attribute_base )|not()}
    {def $attribute_base='ContentObjectAttribute'}
{/if}
{def $xmlinstaller_feature_list=$attribute.content.xmlinstaller_feature_list
     $installed_feature_list=$attribute.content.installed_feature_list}

<fieldset>
{*<legend>asdf</legend>*}
{foreach $xmlinstaller_feature_list as $key => $data}
    <p><label for="{$attribute_base}_ezfeatureselect_list_{$attribute.id}">{$data.info|wash()}</label>
   {switch match=$data.type}
   {case match='selection'}
        {* Always set the .._selected_array_.. variable, this circumvents the problem when nothing is selected. *} 
        <input type="hidden" name="{$attribute_base}_ezfeatureselect_list_{$attribute.id}" value="" />
        <select id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}" 
                class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" 
                name="{$attribute_base}_ezfeatureselect_list_{$attribute.id}[{$key}]">
        {foreach $data.vars as $var_key => $var_val}
            <option value="{$var_key}" {if $installed_feature_list[$key]|contains($var_key)}selected="selected"{/if}>{$var_val|wash( xhtml )}</option>
        {/foreach}
        </select>
       {break}
   {/case}
   {case match='string'}
        <input type="text"
               size="70"
               class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" 
               {if $installed_feature_list|contains($key)}checked="checked"{/if}
               name="{$attribute_base}_ezfeatureselect_list_{$attribute.id}[{$key}]"
               value="{$installed_feature_list[$key]|wash()}" />
       {break}
   {/case}
   {*case match='boolean' is default*}
   {case}
        <input type="checkbox"
               class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" 
               {if $installed_feature_list|contains($key)}checked="checked"{/if}
               name="{$attribute_base}_ezfeatureselect_list_{$attribute.id}[{$key}]"
               value="{$key|wash()}" />
   {/case}
   {/switch}
   </p>
{/foreach}
</fieldset>

