{if is_set( $attribute_base )|not()}
    {def $attribute_base='ContentObjectAttribute'}
{/if}
{if is_set( $html_class )|not()}
    {def $html_class='full'}
{/if}

{foreach $attribute.content.availible_feature_list as $key => $text}
    <input type="checkbox"
           size="70"
           class="checkbox"
           {if $attribute.content.installed_feature_list|contains($key)}checked="checked"{/if}
           name="{$attribute_base}_ezfeatureselect_list_{$attribute.id}[]"
           value="{$key|wash()}" />{$text.title|wash()}<br />
{/foreach}
