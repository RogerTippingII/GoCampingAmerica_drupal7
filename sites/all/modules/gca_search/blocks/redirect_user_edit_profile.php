<?php
global $user;
$newURL = "/user/" . $user->uid . "/edit";
?>
<script type="text/javascript">
  window.location = "<?php echo $newURL; ?>";
</script>