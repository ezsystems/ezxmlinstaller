<div class="block">
    <div class="element">
        <label>{'Template Location'|i18n( 'design/standard/class/datatype' )}:</label>
        {if $class_attribute.data_text1}
            <p>{$class_attribute.data_text1|wash}</p>
        {else}
            <p><i>{'Empty'|i18n( 'design/standard/class/datatype' )}</i></p>
        {/if}
    </div>
</div>
