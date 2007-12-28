{default attribute_base='ContentObjectAttribute'
         html_class='full'}

{foreach $attribute.content.availible_feature_list as $key => $text}
    <input type="checkbox"
           size="70"
           class="checkbox"
           {if $attribute.content.installed_feature_list|contains($key)}checked="checked"{/if}
           name="{$attribute_base}_ezfeatureselect_list_{$attribute.id}[]"
           value="{$key|wash()}" />{$text|wash()}<br />
{/foreach}

{/default}

