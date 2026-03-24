<div class="panel">
    <h3>{l s='Information' mod='pinterestfeed'}</h3>
    <div class="form-wrapper">
        <div class="alert alert-info">
            {$returnAlert}
        </div>
    </div>
    <div class="panel-footer">
        <form method="post">
            <input type="hidden" name="id_pms" value="0"/>
            <button type="submit" class="btn btn-default pull-right" name="newfeed" value=""><i class="process-icon-refresh"></i>{l s='create new feed.' mod='pinterestfeed'}</button>
        </form>
    </div>
</div>