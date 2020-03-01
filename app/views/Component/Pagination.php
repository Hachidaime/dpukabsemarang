<nav aria-label="Page navigation example" class="px-2 table-responsive">
    <ul class="pagination">
        {for $foo=1 to $data.total_page}
        <li class="page-item {if $foo eq 1}active{/if}">
            <a class="page-link {$data.selector}" data-page="{$foo}" href="javascript:void(0);">{$foo}</a>
        </li>
        {/for}
    </ul>
</nav>