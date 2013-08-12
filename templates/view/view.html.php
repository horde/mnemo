<div class="header">
 <div class="rightFloat">
  <?php echo Horde::widget(array('url' => $this->pdfurl, 'title' => _("Save as PDF"), 'class' => 'smallheader')) ?>
<?php if ($this->edit): ?>
  <?php echo ' | ' . $this->edit ?>
<?php endif ?>
<?php if ($this->delete): ?>
  <?php echo ' | ' . $this->delete ?>
<?php endif ?>
 </div>
 <?php echo $this->h($this->desc) ?>
</div>

<table cellspacing="0" width="100%" class="headerbox striped nowrap">
<tr>
  <td class="rightAlign"><strong><?php echo _("Category") ?></strong>&nbsp;</td>
  <td width="100%"><?php echo $this->h($this->category ?: _("Unfiled")) ?></td>
</tr>

<?php if ($this->created): ?>
<tr>
  <td class="rightAlign"><strong><?php echo _("Created") ?></strong>&nbsp;</td>
  <td>
    <?php echo $this->created ?>
    <?php echo $this->h($this->createdby) ?>
  </td>
</tr>
<?php endif ?>

<?php if ($this->modified): ?>
<tr>
  <td class="rightAlign"><strong><?php echo _("Last Modified") ?></strong>&nbsp;</td>
  <td>
    <?php echo $this->modified ?>
    <?php echo $this->h($this->modifiedby) ?>
</td>
</tr>
<?php endif ?>

 <tr>
  <td colspan="2">
<?php if ($this->passphrase): ?>
   <div class="notePassphrase">
    <form action="view.php" name="passphrase" method="post">
     <?php echo Horde_Util::formInput() ?>
     <input type="hidden" name="memolist" value="<?php echo $this->h($this->listid) ?>" />
     <input type="hidden" name="memo" value="<?php echo $this->h($this->id) ?>" />
     <?php echo Horde::label('memo_passphrase', _("_Password")) ?>:
     <input type="password" id="mnemo-passphrase" name="memo_passphrase" />
     <input type="submit" class="horde-default" value="<?php echo _("Decrypt") ?>" />
    </form>
   </div>
<?php else: ?>
   <div class="noteBody">
    <?php echo $this->body ?>
   </div>
<?php endif; ?>
  </td>
 </tr>
</table>
