<h1>CnC – <?=$this->text('menu_main')?></h1>
<?=$this->info()?>
<form action="<?=$this->url()?>" method="post">
    <input type="hidden" name="admin" value="<?=$this->admin()?>">
    <?=$this->csrfToken()?>
    <p>
        <button name="action" value="delete"><?=$this->text('label_delete')?></button>
    </p>
</form>