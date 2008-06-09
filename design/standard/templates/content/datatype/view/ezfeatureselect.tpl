<ul>
{foreach $attribute.content.availible_feature_list as $key => $text}
    {if $attribute.content.installed_feature_list|contains($key)}
        <li>{$text.title|wash()}</li>
    {/if}
{/foreach}
</ul>