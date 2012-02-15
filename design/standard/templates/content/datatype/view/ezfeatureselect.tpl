{def $xmlinstaller_feature_list=$attribute.content.xmlinstaller_feature_list
     $installed_feature_list=$attribute.content.installed_feature_list}

<ul>
{foreach $xmlinstaller_feature_list as $key => $data}
    <li><strong>{$key|wash()}</strong>: 
   {switch match=$data.type}
   {case match='selection'}
        {$installed_feature_list[$key]|wash()}
        {break}
   {/case}
   {case match='string'}
        {$installed_feature_list[$key]|wash()}
       {break}
   {/case}
   {*case match='boolean' is default*}
   {case}
       {if $installed_feature_list|contains($key)}
        yes
       {else}
        no
       {/if}
   {/case}
   {/switch}
   </li>
{/foreach}
</ul>
